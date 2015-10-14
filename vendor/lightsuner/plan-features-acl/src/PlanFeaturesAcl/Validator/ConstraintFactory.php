<?php

namespace PlanFeaturesAcl\Validator;

use RuntimeException;

class ConstraintFactory implements ConstraintFactoryInterface
{
    protected $constraints = array();
    protected $constraintsClasses = array();
    protected $namespaces = array();

    public function __construct()
    {
        $this->addNamespace(__NAMESPACE__."\\Constraint\\");
    }

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    public function getConstraint($type)
    {
        $type = strtolower($type);

        if (isset($this->constraints[$type])) {
            return $this->constraints[$type];
        }

        if (isset($this->constraintsClasses[$type])) {
            $class = $this->constraintsClasses[$type];

            return $this->tryToCreateInstance($type, $class);
        }

        $className = ucfirst($type);
        $lastNamespace = end($this->namespaces);

        foreach ($this->namespaces as $namespace) {
            $isLast = ($namespace === $lastNamespace);

            try {
                return $this->tryToCreateInstance($type, $namespace.$className);
            } catch (RuntimeException $e) {
                if ($isLast) {
                    throw $e;
                }
            }
        }


    }

    /**
     * Add new namespace to find constraints
     *
     * @param string $namespace
     *
     * @return $this
     */
    public function addNamespace($namespace)
    {
        array_unshift($this->namespaces, $namespace);

        return $this;
    }

    /**
     * Add array of namespaces
     *
     * @param array $namespaces
     *
     * @return $this
     */
    public function addNamespaces(array $namespaces)
    {
        $count = count($namespaces);

        for ($i = $count-1; $i>=0; $i++) {
            $this->addNamespace($namespaces[$i]);
        }

        return $this;
    }

    /**
     * Set constraint class
     *
     * @param string $type
     * @param string $class
     *
     * @return $this
     */
    public function setConstraintClass($type, $class)
    {
        $type = strtolower($type);

        $this->constraintsClasses[$type] = $class;

        return $this;
    }

    /**
     *
     * @param string $type
     * @param string $class
     *
     * @return \PlanFeaturesAcl\Validator\FeatureValidatorInterface
     * @throws \RuntimeException
     */
    protected function tryToCreateInstance($type, $class)
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf("Can not find class %s.", $class));
        }

        $this->constraints[$type] = new $class();

        return $this->constraints[$type];
    }
}
