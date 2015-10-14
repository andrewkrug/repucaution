<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * How to use??
 *
 *  JsSettings::instance()->add('base_url', URL::base());
 *  JsSettings::instance()->add( array('one' => 1, 'two' => 2 ) );
 *
 * Put this in template header:
 * echo JsSettings::instance()->get_settings_string();
 *
 * And you can use g_settings.base_url or g_settings.two
 */

class JsSettings extends Kohana_JsSettings {}

abstract class Kohana_JsSettings {


    /**
     * Singleton instance
     *
     * @var
     */
    protected static $_instance;


    /**
     * Settings array
     *
     * @var array
     */
    protected $_js_settings = array();


    /**
     * Name of global js settings variable
     *
     * @var string
     */
    protected $_js_settings_var_name = 'g_settings';

    private function __construct(){ /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
    private function __clone()    { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
    private function __wakeup()   { /* ... @return Singleton */ }  // Защищаем от создания через unserialize


    /**
     * Get singleton instance
     *
     * @return Kohana_JsSettings
     */
    static public function instance()
    {
        if ( ! is_object(self::$_instance))
        {
            self::$_instance = new static();
        }

        return self::$_instance;
    }


    /**
     * Add settings to js global array
     *
     * @param $name string|array
     * @param null $value
     *
     * @return Kohana_JsSettings
     */public function add($name, $value = null){
    if( is_array($name) ){
        $this->_js_settings = Arr::merge($this->_js_settings, $name);
    } else {
        $this->_js_settings[$name] = $value;
    }
    return $this;
}


    /**
     * Generate script string
     *
     * @return string <script>var ....</script>
     */
    public function get_settings_string(){
        return '<script>var '.$this->_js_settings_var_name.' = '.json_encode($this->_js_settings).';</script>';
    }

}