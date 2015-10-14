<?php

class Short_link extends DataMapper {

    var $table = 'short_links';    
    
    var $has_one = array();

    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}