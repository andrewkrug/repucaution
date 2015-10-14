<?php

class MY_Form_validation extends CI_Form_validation
{
     function __construct($config = array()) {
          parent::__construct($config);
     }

    function create_error($error = '') {
        if ( ! empty($error)) {
            $this->_error_array[] = strval($error);
        }
    }

    function error_array() {
        return $this->_error_array;
    }
}