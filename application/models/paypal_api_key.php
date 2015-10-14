<?php

class Paypal_api_key extends DataMapper {

    var $table = 'paypalapikey';


    var $has_one = array(
    );
    var $has_many = array(
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}