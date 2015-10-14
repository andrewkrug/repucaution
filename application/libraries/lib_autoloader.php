<?php
/**
 * User: Dred
 * Date: 22.02.13
 * Time: 16:15
 */
/**
 * Class lib_autoloader
 *
 * Load unloaded classes from 'libraries' directory
 *
 * @version 0.1
 * @author lightuner
 */
class lib_autoloader {

    /**
     * path to search unloaded classes
     *
     * @var string
     */
    protected $lib_dir = '';


    public function __construct(){
        $this->lib_dir = APPPATH.'libraries'.DIRECTORY_SEPARATOR.'autoload';

        spl_autoload_register(array($this,'autoload'));
    }

    /**
     * autoload for classes
     *
     * @param $class
     *
     * @return bool
     */
    public function autoload($class){

        $dir = realpath($this->lib_dir).DIRECTORY_SEPARATOR;

        if( ($path =  $this->find_path($dir,$class)) !== FALSE ){
             require_once $path;
            return TRUE;
        }
        return;
    }

    /**
     * Try to find file by class name (recursive)
     *
     * @param $dir
     * @param $class
     *
     * @return bool|string
     */
    protected function find_path($dir,$class){
        $path = $dir.$class.EXT;
        if( file_exists($path) ){
            return $path;
        }

        $pos = strpos($class, '_');

        if($pos === FALSE){
            return $pos;
        }
        if(mb_strlen($class) <= ($pos+1) ){
            return FALSE;
        }
        $after = substr($class, $pos+1 );
        $before = substr($class, 0,$pos );

        $dir .= $before.DIRECTORY_SEPARATOR;
        return $this->find_path($dir,$after);

    }

}