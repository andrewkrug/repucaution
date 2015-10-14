<?php
/**
 * User: alkuk
 * Date: 12.03.14
 * Time: 17:47
 */

namespace Core\Service\DIContainer;

use Pimple;
use CI_Controller;
use OutOfBoundsException;
use ReflectionClass;
use Core\Utils\Collection\SimpleArrayCollection;

class PimpleContainer
{
    /**
     * To use callable as factory
     * (each request return new instance)
     */
    const TYPE_FACTORY = 'factory';

    /**
     * To use callable as service
     * (Single instance)
     */
    const TYPE_SERVICE = 'service';

    /**
     *  To use callable as is (as param)
     */
    const TYPE_PARAM = 'protected';

    /**
     * @var \CI_Controller
     */
    protected $codeIgniter;

    /**
     * @var string
     */
    protected $servicesConfigName;

    /**
     * @var array
     */
    protected $servicesConfig;

    /**
     * @var string
     */
    protected $parametersConfigName;

    /**
     * @var array
     */
    protected $parametersConfig;

    /**
     * @var \Pimple
     */
    protected $container;

    public function __construct(CI_Controller $CI)
    {

        $this->codeIgniter = $CI;

        $this->servicesConfigName = 'dependency_injection';
        $this->parametersConfigName = 'parameters';

        $config = $this->codeIgniter->config;

        $config->load($this->servicesConfigName, true, true);
        $config->load($this->parametersConfigName, true, true);

        $this->servicesConfig = $config->item($this->servicesConfigName);
        $this->parametersConfig = new SimpleArrayCollection($config->item($this->parametersConfigName));

        $this->loadExtraParameters();


        $this->container = new Pimple();

        $this->container['services.container'] = $this;

        $this->container['parameters.container'] = $this->parametersConfig;

        $this->loadServices();
    }

    /**
     * Get service from DI container
     *
     * @param $name
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function get($name)
    {
        if (!$this->container->offsetExists($name)) {
            throw new OutOfBoundsException('Service - "' . $name . '" doesn\'t esist.');
        }

        return $this->container[$name];
    }

    /**
     * Get parameter value by name
     *
     * @param string $name
     * @throws \OutOfBoundsException if parameter not exists
     * @return mixed
     */
    public function param($name)
    {
        if (!isset($this->parametersConfig)) {
            throw new OutOfBoundsException('Parameter - "' . $name . '" doesn\'t exist.');
        }
        return $this->parametersConfig->get($name);
    }

    /**
     * Load services
     */
    protected function loadServices()
    {
        foreach ($this->servicesConfig as $name => $data) {

            if (!is_array($data) || empty($data['type']) ||  !in_array($data['type'], array(
                        self::TYPE_FACTORY,
                        self::TYPE_SERVICE,
                        self::TYPE_PARAM,
                    ))) {
                $this->addSimple($name, $data);
                continue;
            }

            if (empty($data['class']) && ($data['type'] != self::TYPE_PARAM || empty($data['function']))) {
                continue;
            }

            $this->addFactory($name, $data);

        }
    }

    /**
     * Simply add service to container
     *
     * @param $name
     * @param $data
     */
    protected function addSimple($name, $data)
    {
        $this->container[$name] = $data;
    }

    /**
     * Add service to container wrapping it before
     *
     * @param string $name
     * @param mixed $data
     */
    protected function addFactory($name, $data)
    {
        if ($data['type'] === self::TYPE_PARAM) {
            $this->container[$name] = $this->container->protect($data['function']);
            return ;
        }

        $parameters = $this->parametersConfig;

        $closure = function ($c) use ($data, $parameters) {

            $args = array();

            // fetch arguments for injecting into object
            if (!empty($data['arguments']) && is_array($data['arguments'])) {
                foreach ($data['arguments'] as $argument) {

                    if (is_string($argument) && strlen($argument) > 1 &&
                        ($argument[0] == '@' || $argument[0] == '%' &&
                            $argument[strlen($argument) - 1] == '%' && strlen($argument) > 2)
                    ) {
                        $name = substr($argument, 1);

                        switch ($argument[0]) {
                            case '@':
                                $args[] = $c[$name];
                                break;
                            case '%':
                                $name = substr($name, 0, -1);
                                if ($parameters->has($name)) {
                                    $args[] = $parameters->get($name);
                                }
                                break;
                        }


                    } else {
                        $args[] = $argument;
                    }

                }

            }

            $class = new ReflectionClass($data['class']);

            return $class->newInstanceArgs($args);
        };

        if ($data['type'] === self::TYPE_FACTORY) {
            $closure = $this->container->factory($closure);
        }

        $this->container[$name] = $closure;

    }

    /**
     * Load params from config/Parameters folder
     */
    protected function loadExtraParameters()
    {
        $configPath = APPPATH.'/config/Parameters';

        $directory = new \RecursiveDirectoryIterator($configPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($files as $file) {
            $config = null;
            include $file[0];
            if (!empty($config)) {
                $this->parametersConfig->add($config);
            }
        }

    }
}
