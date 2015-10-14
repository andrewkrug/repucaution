<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Nsoap {
	
	private $_authenticated;
	
	public function __construct($wsdl){
		
		return new SoapClient($wsdl);
		
		
	}
	
	/* public function wsdl() {
	
        $wsdl = $this->load->library('wsdldocument','api');
        header('Content-Type: text/xml');
        echo $wsdl->saveXML();
    } 
	
	public function index() {
        $this->load->helper('url');
        
        ini_set('soap.wsdl_cache_limit', 0);
        ini_set('soap.wsdl_cache_ttl', 0);
        $base_url = base_url();
        $wsdl = $base_url.'/index.php/api/wsdl';
        $url['uri'] = $base_url.'/index.php/api'; //Take out the index.php if you are using rewrite to do ths same
        $server = new SOAPServer($wsdl, $url);
       
        $server->setClass('api');
        $server->handle();
    }
	
	public function someMethod($var1, $var2, $var3) 
    {
        $this->checkAuth();
        //dostuff
        
        return true;
    } 
	
	 public function AuthHeader($Header)
    {
        $this->_authenticated = false;
        //You could get this from a database 
        if($Header->username == 'user' && $Header->password == 'pass')
        {
            $this->authenticated = true;
        }
            
        return $this->authenticated;

    } 
	
	protected function checkAuth()
    {
        if(!$this->_authenticated){
            log_info('User not valid denying access');
            $this->output->set_status_header('403');
            die();
        }

    }  */
	
	
	
}