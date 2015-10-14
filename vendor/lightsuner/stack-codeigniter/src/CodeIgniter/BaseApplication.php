<?php

namespace StackCI\CodeIgniter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;
use DomainException;
use RuntimeException;
use Closure;
use Exception;

/**
 * Class BaseApplication
 */
class BaseApplication
{

    /**
     * There only one instance can be run
     *
     * @var bool
     */
    protected static $isInitialized = false;

    /**
     * Base path to CI
     *
     * @var string
     */
    protected $basePath;

    /**
     * CI environment
     *
     * @var string
     */
    protected $environment;

    /**
     * CodeIgniter's Version
     *
     * @var string
     *
     */
    protected $version;

    /**
     * CodeIgniter Branch (Core = TRUE, Reactor = FALSE)
     *
     * @var bool
     */
    protected $coreBranchType;

    /**
     * Application folder
     *
     * @var string
     */
    protected $applicationFolder = 'application';

    /**
     * System folder
     *
     * @var string
     */
    protected $systemFolder = 'system';

    /**
     * Collection of closures running before ci's kernel load
     *
     * @var array
     */
    protected $beforeKernelCollection = array();

    /**
     * @param string $basePath
     * @param string $environment (development, testing, production)
     * @param string $version
     * @param bool $coreBranchType
     */
    public function __construct($basePath, $environment = 'production', $version = '2.1.4', $coreBranchType = false)
    {

        $this->checkInstance();

        $this->setBaseDir($basePath);
        $this->setEnvironment($environment);

        $this->version        = $version;
        $this->coreBranchType = $coreBranchType;

    }

    /**
     * Set application folder
     *
     * @param $applicationPath
     *
     * @return $this
     */
    public function setAppFolder($applicationPath)
    {
        $this->applicationFolder = rtrim($applicationPath, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Set system folder
     *
     * @param $systemFolder
     *
     * @return $this
     */
    public function setSystemFolder($systemFolder)
    {
        $this->systemFolder = rtrim($systemFolder, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * Initialize CodeIgniter Application
     *
     * @return $this
     */
    public function init()
    {
        $this->checkInstance();

        $this->defineConstants();

        foreach ($this->beforeKernelCollection as $callable) {
            $callable();
        }

        $this->loadKernel();

        static::$isInitialized = true;

        return $this;
    }

    /**
     *
     * @param callable $callable
     *
     * @return $this
     */
    public function beforeKernelLoad(Closure $callable)
    {
        $this->beforeKernelCollection[] = $callable;

        return $this;
    }

    /**
     * Run CodeIngiter's application
     *
     * @param Request $request
     * @param Response $response
     */
    public function run(Request $request, Response $response)
    {
        global $RTR, $OUT, $CFG, $IN;

        try {
            // set request and response to Input object
            $IN->setRequest($request)->setResponse($response);
            // set request to config
            $CFG->setRequest($request);

            // set request to router
            $RTR->_set_routing($request);

            // set request and response to Output object
            $OUT->setRequest($request)->setResponse($response);

            $this->loadBaseController();


            //For Modular Extensions - HMVC
            if ($CFG instanceof \CI_Config && get_class($CFG) != 'CI_Config') {
                $CFG->setRequest($request);
            }

            $this->execute();

            $OUT->_display();
        } catch(Exception $e) {
            $exceptionCatcher = load_class('ExceptionCatcher', 'core');
            $exceptionCatcher->setEnvironment($this->environment);
            $exceptionCatcher->setException($e);

            throw $exceptionCatcher->getHttpException();
        }

    }

    /**
     * Call hook "post_system" and close db connection
     */
    public function quit()
    {
        global $EXT, $CI;

        $EXT->_call_hook('post_system');

        /*
         * ------------------------------------------------------
         *  Close the DB connection if one exists
         * ------------------------------------------------------
         */
        if (class_exists('CI_DB') AND isset($CI->db)) {
            $CI->db->close();
        }
    }


    /**
     * Load CodeIgniter's kernel
     *
     * @return $this
     */
    protected function loadKernel()
    {
        $this->loadCommon();
        $this->loadConstants();
        $this->setErrorHandler();
        $this->setSubclassPrefix();
        $this->setMaxExecutionTime();
        $this->instantiateBenchmark();
        $this->instantiateHooksCore();
        $this->callPreSystemHook();
        $this->instantiateConfig();
        $this->instantiateUtf8();
        $this->instantiateURI();
        $this->instantiateRouter();
        $this->instantiateSecurity();
        $this->instantiateInput();
        $this->instantiateLang();
        $this->instantiateOutput();

        return $this;
    }

    /**
     * Check if there is another instance of CodeIgniter Application
     *
     * @throws \RuntimeException
     */
    protected function checkInstance()
    {
        if (static::$isInitialized) {
            throw new RuntimeException('There can only be one instance of CodeIgniter Application.');
        }
    }

    /**
     * Set base path
     *
     * @param $basePath
     *
     * @throws \InvalidArgumentException
     */
    protected function setBaseDir($basePath)
    {
        $basePath = realpath($basePath);
        if (!$basePath || !is_dir($basePath)) {
            throw new InvalidArgumentException(sprintf('Invalid CodeIgniter base path (%s).', $basePath));
        }

        $this->basePath = $basePath;
    }

    /**
     * Set environment
     *
     * @param $environment
     *
     * @throws \DomainException
     */
    protected function setEnvironment($environment)
    {

        $this->environment = $environment;

        switch ($this->environment) {
            case 'development':
                error_reporting(E_ALL);
                break;

            case 'testing':
            case 'production':
                error_reporting(0);
                break;

            default:
                throw new DomainException(sprintf('Unknown environment type: %s', $this->environment));
        }
    }


    /**
     * Load base CodeIgniter's Contoller
     */
    protected function loadBaseController()
    {
        require_once STACKCIEXTPATH . 'core/BaseControllerLoader.php';
    }


    /**
     * Execute current request
     */
    protected function execute()
    {

        global $RTR, $BM, $EXT, $CI, $URI;
        // Load the local application controller
        // Note: The Router class automatically validates the controller path using the router->_validate_request().
        // If this include fails it means that the default controller in the Routes.php file is not resolving to something valid.
        if (!file_exists(APPPATH . 'controllers/' . $RTR->fetch_directory() . $RTR->fetch_class() . '.php')) {
            show_error('Unable to load your default controller. Please make sure the controller specified in your Routes.php file is valid.');
        }

        include_once(APPPATH . 'controllers/' . $RTR->fetch_directory() . $RTR->fetch_class() . '.php');

        // Set a mark point for benchmarking
        $BM->mark('loading_time:_base_classes_end');

        /*
         * ------------------------------------------------------
         *  Security check
         * ------------------------------------------------------
         *
         *  None of the functions in the app controller or the
         *  loader class can be called via the URI, nor can
         *  controller functions that begin with an underscore
         */
        $class  = $RTR->fetch_class();
        $method = $RTR->fetch_method();

        if (!class_exists($class) OR strncmp($method, '_', 1) == 0 OR in_array(strtolower($method), array_map('strtolower', get_class_methods('CI_Controller')))
        ) {
            if (!empty($RTR->routes['404_override'])) {
                $x      = explode('/', $RTR->routes['404_override']);
                $class  = $x[0];
                $method = (isset($x[1]) ? $x[1] : 'index');
                if (!class_exists($class)) {
                    if (!file_exists(APPPATH . 'controllers/' . $class . '.php')) {
                        show_404("{$class}/{$method}");
                    }

                    include_once(APPPATH . 'controllers/' . $class . '.php');
                }
            } else {
                show_404("{$class}/{$method}");
            }
        }

        /*
         * ------------------------------------------------------
         *  Is there a "pre_controller" hook?
         * ------------------------------------------------------
         */
        $EXT->_call_hook('pre_controller');

        /*
         * ------------------------------------------------------
         *  Instantiate the requested controller
         * ------------------------------------------------------
         */
        // Mark a start point so we can benchmark the controller
        $BM->mark('controller_execution_time_( ' . $class . ' / ' . $method . ' )_start');

        $CI = new $class();

        /*
         * ------------------------------------------------------
         *  Is there a "post_controller_constructor" hook?
         * ------------------------------------------------------
         */
        $EXT->_call_hook('post_controller_constructor');

        /*
         * ------------------------------------------------------
         *  Call the requested method
         * ------------------------------------------------------
         */
        // Is there a "remap" function? If so, we call it instead
        if (method_exists($CI, '_remap')) {
            $CI->_remap($method, array_slice($URI->rsegments, 2));
        } else {
            // is_callable() returns TRUE on some versions of PHP 5 for private and protected
            // methods, so we'll use this workaround for consistent behavior
            if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($CI)))) {
                // Check and see if we are using a 404 override and use it.
                if (!empty($RTR->routes['404_override'])) {
                    $x      = explode('/', $RTR->routes['404_override']);
                    $class  = $x[0];
                    $method = (isset($x[1]) ? $x[1] : 'index');
                    if (!class_exists($class)) {
                        if (!file_exists(APPPATH . 'controllers/' . $class . '.php')) {
                            show_404("{$class}/{$method}");
                        }

                        include_once(APPPATH . 'controllers/' . $class . '.php');
                        unset($CI);
                        $CI = new $class();
                    }
                } else {
                    show_404("{$class}/{$method}");
                }
            }

            // Call the requested method.
            // Any URI segments present (besides the class/function) will be passed to the method for convenience
            call_user_func_array(array(
                &$CI,
                $method
            ), array_slice($URI->rsegments, 2));
        }


        // Mark a benchmark end point
        $BM->mark('controller_execution_time_( ' . $class . ' / ' . $method . ' )_end');

        /*
         * ------------------------------------------------------
         *  Is there a "post_controller" hook?
         * ------------------------------------------------------
         */
        $EXT->_call_hook('post_controller');
    }

    /**
     * Is there a valid cache file?  If so, we're done...
     */
    protected function callDisplayCache()
    {
        global $EXT, $OUT, $CFG, $URI;
        if ($EXT->_call_hook('cache_override') === false) {
            if ($OUT->_display_cache($CFG, $URI) == true) {
                exit;
            }
        }
    }

    /**
     * Define all CodeIgniter's default constants
     */
    protected function defineConstants()
    {
        $frontController = realpath($_SERVER['SCRIPT_FILENAME']);

        // Path to component StackCI
        define('STACKCIPATH', __DIR__);
        define('STACKCIEXTPATH', STACKCIPATH . '/Ext/');

        define('ENVIRONMENT', $this->environment);
        define('CI_CORE', $this->coreBranchType);
        define('CI_VERSION', $this->version);
        define('EXT', '.php');
        define('SELF', pathinfo($frontController, PATHINFO_BASENAME));

        $this->defineSystemFolder();
        $this->defineAppFolder();

        define('FCPATH', str_replace(SELF, '', $frontController));

        define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

    }

    /**
     * Define system folder
     *
     * @throws \InvalidArgumentException
     */
    protected function defineSystemFolder()
    {
        $systemPath     = $this->systemFolder;
        $realSystemPath = realpath($this->basePath . DIRECTORY_SEPARATOR . $systemPath);

        if ($realSystemPath) {
            $systemPath = $realSystemPath;
        }

        $systemPath .= DIRECTORY_SEPARATOR;

        if (!is_dir($systemPath)) {
            throw new InvalidArgumentException(sprintf('Invalid CodeIgniter system path (%s).', $systemPath));
        }

        define('BASEPATH', $systemPath);
    }

    /**
     * Define application folder
     *
     * @throws \InvalidArgumentException
     */
    protected function defineAppFolder()
    {
        $applicationPath     = $this->applicationFolder;
        $realApplicationPath = realpath($this->basePath . DIRECTORY_SEPARATOR . $applicationPath);

        if ($realApplicationPath) {
            $applicationPath = $realApplicationPath;
        }

        $applicationPath .= DIRECTORY_SEPARATOR;

        if (!is_dir($applicationPath)) {
            throw new InvalidArgumentException(sprintf('Invalid CodeIgniter application path (%s).', $applicationPath));
        }

        define('APPPATH', $applicationPath);
    }

    /**
     * Load CI common
     */
    protected function loadCommon()
    {
        require(STACKCIEXTPATH . 'core/Common.php');
        require(BASEPATH . 'core/Common.php');
    }

    /**
     * Load custom constants, depending on current environment
     */
    protected function loadConstants()
    {
        if (defined('ENVIRONMENT') AND file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php')) {
            require(APPPATH . 'config/' . ENVIRONMENT . '/constants.php');
        } else {
            require(APPPATH . 'config/constants.php');
        }
    }

    /**
     * Define a custom error handler so we can log PHP errors
     */
    protected function setErrorHandler()
    {
        set_error_handler('_exception_handler');
    }

    /**
     * Set the subclass_prefix
     *
     * Normally the "subclass_prefix" is set in the config file.
     * The subclass prefix allows CI to know if a core class is
     * being extended via a library in the local application
     * "libraries" folder. Since CI allows config items to be
     * overriden via data set in the main index. php file,
     * before proceeding we need to know if a subclass_prefix
     * override exists.  If so, we will set this value now,
     * before any classes are loaded
     * Note: Since the config file data is cached it doesn't
     * hurt to load it here.
     */
    protected function setSubclassPrefix()
    {
        if (isset($assign_to_config['subclass_prefix']) AND $assign_to_config['subclass_prefix'] != '') {
            get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
        }
    }

    /**
     * Set a liberal script execution time limit
     */
    protected function setMaxExecutionTime()
    {
        if (function_exists("set_time_limit") == true AND @ini_get("safe_mode") == 0) {
            @set_time_limit(300);
        }
    }

    /**
     * Start the timer... tick tock tick tock...
     */
    protected function instantiateBenchmark()
    {
        $GLOBALS['BM'] =& load_class('Benchmark', 'core');
        $GLOBALS['BM']->mark('total_execution_time_start');
        $GLOBALS['BM']->mark('loading_time:_base_classes_start');
    }

    /**
     * Instantiate the hooks class
     */
    protected function instantiateHooksCore()
    {
        $GLOBALS['EXT'] =& load_class('Hooks', 'core');
    }

    /**
     * Is there a "pre_system" hook?
     */
    protected function callPreSystemHook()
    {
        global $EXT;
        $EXT->_call_hook('pre_system');
    }

    /**
     * Instantiate the config class
     */
    protected function instantiateConfig()
    {
        $GLOBALS['CFG'] = & load_class('Config', 'core');

        // Do we have any manually set config items in the index.php file?
        if (isset($assign_to_config)) {
            $GLOBALS['CFG']->_assign_to_config($assign_to_config);
        }
    }

    /**
     * Instantiate the UTF-8 class
     *
     * Note: Order here is rather important as the UTF-8
     * class needs to be used very early on, but it cannot
     * properly determine if UTf-8 can be supported until
     * after the Config class is instantiated.
     */
    protected function instantiateUtf8()
    {
        $GLOBALS['UNI'] =& load_class('Utf8', 'core');
    }

    /**
     * Instantiate the URI class
     */
    protected function instantiateURI()
    {
        $GLOBALS['URI'] =& load_class('URI', 'core');
    }

    /**
     * Instantiate the routing class and set the routing
     */
    protected function instantiateRouter()
    {
        $GLOBALS['RTR'] =& load_class('Router', 'core');
    }

    /**
     * Load the security class for xss and csrf support
     */
    protected function instantiateSecurity()
    {
        $GLOBALS['SEC'] =& load_class('Security', 'core');
    }

    /**
     * Load the Input class and sanitize globals
     */
    protected function instantiateInput()
    {
        $GLOBALS['IN'] =& load_class('Input', 'core');
    }

    /**
     * Load the Language class
     */
    protected function instantiateLang()
    {
        $GLOBALS['LANG'] =& load_class('Lang', 'core');
    }

    /**
     * Load CodeIgniter's Output class
     */
    protected function instantiateOutput()
    {
        $GLOBALS['OUT'] =& load_class('Output', 'core');
    }
}
 