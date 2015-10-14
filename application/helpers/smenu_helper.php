<?php if(!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: Dred
 * Date: 26.12.12
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

class sMenu {

    /**
     * Singleton object
     * @var null
     */
    protected static $instance = null;
    private $ci;
    protected $raw_links;
    protected $links;
    protected $role;
    protected $level_weight = 1000;
    protected $pseudo_active_link = null;

    /**
     * Protect Singleton from multi creation
     */
    private function __construct() { /* ... @return Singleton */
        $this->ci = get_instance();
        $this->ci->load->config('smenufb', true);
        $this->raw_links = $this->ci->config->item('smenufb', 'smenufb');
        if( count($this->raw_links) == 1 ){
            $this->role = key($this->raw_links);
        }
    }


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
     * @return sMenu
     */
    public static function getInst() { // @return Singleton
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set current role
     *
     * @param $role
     * @return sMenu
     */
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    /**
     * Set pseudo active link
     *
     * @param $url
     */
    public function set_pal($url){
        $this->pseudo_active_link = $url;
    }

    /**
     * Add custom menu link
     *
     * @param $path
     * @param $data
     * @param null $role
     *
     * @return sMenu
     * @throws Exception
     */
    public function addLink($path,$data, $role = null){
        if(empty($role)){
            $role = $this->role;
        }
        if( empty($role) ){
            throw new Exception('Unknown role!');
            return $this;
        }

        $this->raw_links[$role][$path] = $data;
        return $this;
    }

    /**
     * Perse links for a role
     *
     * @param $role
     * @return sMenu
     */
    public  function parse_links(){
        if(empty($this->role) || empty($this->raw_links[$this->role])){
            throw new Exception('Unknown role!');
            return $this;
        }
        $value = $this->raw_links[$this->role];
        $this->links = array();
        $this->set_multilevel_weight($value);

        uasort($value, array($this,'menu_order' ));



        foreach($value as $key => $_val){
            $current_array = &$this->links;

            $ignore_children = isset($_val['#ignore_children']);

            if(strpos($key, '/') !== false && ! $ignore_children){
                $link_path = explode('/', $key);
                $link_path = array_filter($link_path);

                foreach($link_path as $_link) {
                    if(!isset($current_array[ $_link ])){
                        $current_array[ $_link ] = array();
                    }
                    $current_array = &$current_array[ $_link ];
                }


            } else {
                if(!isset($current_array[$key])){
                    $current_array[$key] = array();
                }
                $current_array = &$current_array[$key];
            }

            if(!isset($_val['#link'])){
                $_val['#link']= $key;
            }

            $current_array = Arr::merge($current_array, $_val);
        }
        return $this;
    }

    /**
     * Render menu
     *
     * @return string
     */
    public function render(){

        $out = '<ul class="nav nav-list">';

        if ( ! empty($this->links)) {
            foreach($this->links as $_link) {
                $out .= $this->render_link($_link);
            }
        }

        $out .= '</ul>';
        return $out;
    }

    /**
     * Check: is current link  active?
     *
     * @param $uri
     *
     * @return bool
     */
    protected function uri_is_active($uri){
        if(isset($this->pseudo_active_link)){
            $current = $this->pseudo_active_link;
        } else {
            $current = implode('/', $this->ci->uri->segment_array());
        }

        if ( ! $current) $current = implode('/', $this->ci->uri->rsegment_array());
        $uri_len = strlen($uri);
        $current = substr($current, 0, $uri_len);
        return ($current === $uri);
    }

    /**
     * Render link
     *
     * <li> ...... </li>
     *
     * @param $link_settings
     *
     * @return string
     */
    protected function render_link(&$link_settings){

        $out = ' <li class="';
        $has_children = $this->sub_menu_exist($link_settings);

        if(!empty($link_settings['#link']) ){

            if(isset($link_settings['#li_class'])) {
                $out .= ' ' . $link_settings['#li_class'] . ' ';
            }

            if($this->uri_is_active($link_settings['#link']) 
                OR (isset($link_settings['#always_active']) && $link_settings['#always_active'])
            ){
                $out .= 'active ';
            } else {
                $out .= 'hiddenList ';
            }

        }
        if($has_children){
            $out .= 'toggle ';
            if (!empty($link_settings['#open'])) {
                $out .= 'open ';
            }
        }

        $out .= '">';

        $out .= $this->render_a($link_settings, $has_children);


        if($has_children){
            $out .= $this->generate_sub_menu( $link_settings );
        }

        $out .= '</li>';
        return $out;

    }

    /**
     * Render "a" - element
     *
     * <a>.....</a>
     *
     * @param $link_settings
     *
     * @return string
     */
    protected function render_a(&$link_settings, $has_children){
        $out = '<a';

        $title = !empty($link_settings['#title']) ? $link_settings['#title'] : '';
        $link = isset($link_settings['#link']) ? $link_settings['#link'] : null;
        $data = !empty($link_settings['#data']) ? $link_settings['#data'] : array();

        if($link){
            $_char = substr($link, 0, 1);
            if($_char != '#'){
                $link = site_url($link);
            }
            $out .= ' href="'.$link.'"';
        }

        foreach($data as $key=> $_val){
            $out .= ' data-'.$key.'="'.$_val.'"';
        }

        if(!empty($link_settings['#class'])){
            $out .= ' class="';
            foreach($link_settings['#class'] as $class) {
                $out .= $class.' ';
            }
            $out .= '"';
        }

        $out .= '>';

        if( ! empty($link_settings['#icon_class'])) {
            $out .= '<i class="' . $link_settings['#icon_class'] . '"></i> ';
        }

        $out .= $title;

        if(!empty($link_settings['#variables'])){
            foreach($link_settings['#variables'] as $key => $_val){
                $out .= '<span class="'.$key.'">'.$_val.'</span>';
            }
        }

        if($has_children OR (! empty($link_settings['#force_arrow']) && $link_settings['#force_arrow'])) {
            $out .= '<span class="arrow"></span>';
        }

        $out .= '</a>';
        return $out;
    }

    /**
     * Check if submenu exist
     *
     * @param $link_settings
     *
     * @return bool
     */
    protected function sub_menu_exist(&$link_settings){
        foreach($link_settings as $key => $_val){
            $_char = substr($key,0,1);
            if($_char == '#'){
                continue;
            }
            return TRUE;
        }
        return FALSE;
    }


    /**
     * Render submenu
     *
     * @param $link_settings
     *
     * @return string
     */
    protected function generate_sub_menu(&$link_settings){
        $out = ' <ul class="sub_menu" style="">';
        foreach($link_settings as $key => $_val){
            $_char = substr($key,0,1);
            if($_char == '#'){
                continue;
            }
            $out .= $this->render_link($_val);
        }
        $out .= '</ul>';
        return $out;
    }

    /**
     * Find menu element by path
     *
     * @param $path
     *
     * @return mixed
     */
    protected function &get_element_by_path($path){
        static $elements;

        if(empty($elements[$path])){

            $selected_link = &$this->links;

            if(strpos($path, '/') !== false){
                $link_path = explode('/', $path);
                $link_path = array_filter($link_path);
                foreach($link_path as $_segment) {
                    if(!isset($selected_link[$_segment])){
                        $selected_link[$_segment] = array();
                    }
                    $selected_link = &$selected_link[$_segment];
                }

            } else {
                if(!isset($selected_link[$path])){
                    $selected_link[$path] = array();
                }
                $selected_link = &$selected_link[$path];
            }

            $elements[$path] = &$selected_link;
        }

        return $elements[$path];
    }

    /**
     * Set menu item variables
     *
     *  set_vars('user/list', array('num' => 10) )
     *
     * @param $path
     * @param $vars
     *
     * @return bool
     */
    public function set_vars($path, $vars){
        if(!is_array($vars)){
            return FALSE;
        }
        $selected_link = &$this->get_element_by_path($path);


        if(!isset($selected_link['#variables'])){
            $selected_link['#variables'] = array();
        }

        $selected_link['#variables'] = Arr::merge($selected_link['#variables'], $vars);

    }

    /**
     * Set data to menu item
     *
     * set_data('user/list', array('toggle' => 'model') )
     * render: <a data-toggle="modal">....</a>
     *
     * @param $path
     * @param $data
     *
     * @return bool
     */
    public function set_data($path, $data){
        if(!is_array($data)){
            return FALSE;
        }
        $selected_link = &$this->get_element_by_path($path);

        if(!isset($selected_link['#data'])){
            $selected_link['#data'] = array();
        }

        $selected_link['#data'] = Arr::merge($selected_link['#data'], $data);
    }

    /**
     * Set Class to menu item
     *
     * set_data('user/list', array('first', 'menu-iem') )
     * render: <a class="first menu-iem">....</a>
     *
     * @param $path
     * @param array $class
     *
     * @return bool
     */
    public function set_class($path, $class = array() ){
        if(!is_string($class)){
            $class= array($class);
        }elseif(!is_array($class)){
            return FALSE;
        }
        $selected_link = &$this->get_element_by_path($path);

        if(!isset($selected_link['#class'])){
            $selected_link['#class'] = array();
        }

        $selected_link['#class'] = Arr::merge($selected_link['#class'], $class);
    }

    /**
     * Sort menu items by #weight
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    public function menu_order($a, $b){
        if(isset($a['#weight']) && !isset($b['#weight']) ){
            return -1;
        }elseif( !isset($a['#weight']) && isset($b['#weight'])){
            return 1;
        }elseif( (!isset($a['#weight']) && !isset($b['#weight'])) || ($a['#weight'] == $b['#weight']) ){
            return 0;
        }

        if($a['#weight']  === $b['#weight']){
            return 0;
        }

        return ($a['#weight']  < $b['#weight'] ) ? -1 : 1;

    }

    /**
     * Set additional value, depending on the menu depth level
     *
     * @param $raw_path
     */
    public function set_multilevel_weight(&$raw_path){

        foreach($raw_path as $key => &$_path) {
            $level = substr_count($key, '/');
            if( isset($_path['#weight']) ){
                $_path['#weight'] += $this->level_weight * $level;
            } else {
                $_path['#weight'] = $this->level_weight * $level;
            }
        }

    }
}