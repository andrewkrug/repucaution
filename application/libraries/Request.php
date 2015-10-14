<?php
/**
 * Request 
 *
 * @author ajorjik
 */
class Request 
{
    public function getFiles()
    {
        return $_FILES['file'];
    }
    
    public function getServer()
    {
        return $_SERVER;
    }
    
    public function getPost($key = null)
    {
        return ($key) ? $_POST[$key] : $_POST;
    }
   

}