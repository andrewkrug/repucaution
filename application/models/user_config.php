<?php

/**
 * Class User_config
 *
 * @author Ajorjik
 */
class User_config extends DataMapper {

    var $table = 'user_configs';


    var $has_one = array(
        'user',
        'config'
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }
}