<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Terms extends MY_Controller {

	 function __construct()
	{	
		$this->template->set('paymentsEnabled', false);
		$this->set_default_css();
        $this->set_default_js();
        $this->bitly_load();
	} 

	//redirect if needed, otherwise display the user list
	function index()
	{
		
		$this->template->layout = 'layouts/auth';
		$this->template->render();
	}
	
	
	
}
