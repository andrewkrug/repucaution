<?php

use StackCI\CodeIgniter\Orig\Core\Loader;


class CI_Loader extends Loader
{

	public function __construct()
	{
	    parent::__construct();

        $this->_ci_library_paths = array(APPPATH, STACKCIEXTPATH, BASEPATH);
	}

}
