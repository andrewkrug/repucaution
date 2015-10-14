<?php

class User_feed extends DataMapper {

    var $table = 'media';


    var $has_one = array(
        // user
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}