<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: Dred
 * Date: 26.12.12
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

class CssJs {

    /**
     * Singleton object
     * @var null
     */
    protected static $instance = null;

    /**
     * Array of css styles
     *
     * @var array
     */
    protected $css_array = array();

    /**
     * Array of js files to header
     *
     * @var array
     */
    protected $js_header_array = array();

    /**
     * Array of js files to footer
     *
     * @var array
     */
    protected $js_footer_array = array();

    /**
     * Path to local css files
     *
     * @var string
     */
    protected $css_path = '';

    /**
     * Path to js files
     * @var string
     */
    protected $js_path = '';

    /**
     * Version of css and js
     *
     * @var string
     */
    protected $version = '?';


    /**
     * Protect Singleton from multi creation
     */
    private function __construct() { /* ... @return Singleton */
        $ci = get_instance();
        $ci->load->config('cssjs', true);
        $this->js_path = base_url().$ci->config->item('js_path','cssjs');
        $this->css_path = base_url().$ci->config->item('css_path','cssjs');
        $this->version .= $ci->config->item('files_version','cssjs');
    } // Защищаем от создания через new Singleton

    /**
     * Protect Singleton from clone
     */
    private function __clone() { /* ... @return Singleton */
    } // Защищаем от создания через клонирование

    /**
     * Protect Singleton from wakeup
     */
    private function __wakeup() { /* ... @return Singleton */
    } // Защищаем от создания через unserialize


    /**
     * Get instance of this object (CssJs)
     *
     * @return CssJs
     */
    public static function getInst() { // @return Singleton
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add css
     *
     * You can use add_css('styles.css');
     * add_css('.nav{display:none;}', 'inline');
     * add_css('http://google.com/styles.css','external');
     * OR use array
     * add_css( array(
     *      'styles.css',
     *      array('css' => '.nav{display:none;}', 'type' => 'inline'),
     *      array('css' => 'http://google.com/styles.css', 'type' => 'external'),
     * ) );
     *
     *
     * @param array|string $data
     * @param null|external|inline $type
     *
     * @return CssJs
     */
    public function add_css($data, $type = null) {
        if(is_string($data)) {
            $this->add_single_css($data, $type);
        } elseif(is_array($data)) {
            foreach($data as $_val) {
                if(empty($_val)) {
                    continue;
                }

                if(is_string($_val)) {
                    $this->add_single_css($_val, $type);
                } elseif(is_array($_val) && !empty($_val['css']) && isset($_val['type'])) {
                    $this->add_single_css($_val['css'], $_val['type']);
                }
            }

        }

        return $this;
    }

    /**
     * Add single css to css array
     *
     * @param $css
     * @param $type
     */
    protected function add_single_css($css, $type) {
        $out = '';
        switch($type) {
            case 'external':
                $out = '<link rel="stylesheet" href="' . $css . '" type="text/css">';
                break;
            case 'inline':
                $out = '<style type="text/css">' . $css . '</style>';
                break;
            default:
                $link = $this->css_path . $css . $this->version;
                $out = '<link rel="stylesheet" href="' . $link . '" type="text/css">';
                break;
        }


        $this->css_array[] = $out;

    }

    /**
     * Get all added css links
     *
     * @return string
     */
    public function get_css() {
        $out = '';
        foreach($this->css_array as $_css) {
            $out .= $_css.PHP_EOL;
        }
        return $out;
    }


    /**
     * Add js
     *
     * You can use add_js('func.js');
     * add_js('alert("Hellow World!");', 'inline');
     * add_js('http://ajax.google.com/jquery.1.8.1.js','external');
     * OR use array
     * add_js( array(
     *      'func.js',
     *      array('js' => 'alert("Hellow World!");', 'type' => 'inline', 'location' => 'header'),
     *      array('js' => 'http://ajax.google.com/jquery.1.8.1.js', 'type' => 'external'),
     * ) );
     *
     * @param $data
     * @param null|external|inline $type
     * @param footer|header $location
     *
     * @return bool|CssJs
     */
    public function add_js($data, $type = null, $location = 'footer') {
        $save_suffix = 'js_';
        $save_prefix = '_array';
        $save_to = $save_suffix . $location . $save_prefix;
        if(!isset($this->{$save_to})) {
            return FALSE;
        }

        if(is_string($data)) {
            $this->add_single_js($data, $type, $save_to);
        } elseif(is_array($data)) {
            foreach($data as $_js) {
                if(is_string($_js)) {
                    $this->add_single_js($_js, $type, $save_to);
                } elseif(is_array($_js) && !empty($_js['js'])) {
                    $_local_save_to = $save_to;
                    if(isset($_js['location']) && isset($this->{$save_suffix . $_js['location'] . $save_prefix})) {
                        $_local_save_to = $save_suffix . $_js['location'] . $save_prefix;
                    }
                    if(!isset($_js['type'])){
                        $_js['type'] = null;
                    }
                    $this->add_single_js($_js['js'], $_js['type'], $_local_save_to);
                }
            }

        }

        return $this;
    }


    /**
     * Add js - equal controller-method path
     *
     * @param $controller
     * @param $method
     */
    public function c_js($controller = null, $method = null){
        if(!$controller){
            $controller = get_instance()->router->class;
        }
        if(!$method){
            $method = get_instance()->router->method;
        }
        $this->add_js('controller/'.$controller.'/'.$method.'.js');
        return $this;
    }

    /**
     * Add single js file to array
     *
     * @param $data
     * @param $type
     * @param $location
     */
    protected function add_single_js($js, $type, $location) {
        $out = '';
        switch($type) {
            case 'external':
                $protocol = get_instance()->input->getRequest()->getScheme();
                $out = '<script type="text/javascript" src="' .$protocol.'://'.$js. '" ></script>';
                break;
            case 'inline':
                $out = '<script type="text/javascript">' . $js.'</script>';
                break;
            default:
                $link = $this->js_path . $js . $this->version;
                $out = '<script type="text/javascript" src="' . $link . '" ></script>';
                break;
        }


        $this->{$location}[] = $out;

    }

    /**
     * Get all js to header
     *
     * @return string
     */
    public function get_header_js() {
        $out = '';
        foreach($this->js_header_array as $_js) {
            $out .= $_js.PHP_EOL;
        }
        return $out;
    }

    /**
     * Get all js to footer
     *
     * @return string
     */
    public function get_footer_js() {
        $out = '';
        foreach($this->js_footer_array as $_js) {
            $out .= $_js.PHP_EOL;
        }
        return $out;
    }

}