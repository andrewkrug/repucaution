<?php

class Keyword_rank extends DataMapper {

    var $table = 'keyword_rank';
    
    
    var $has_one = array(
        'keyword',
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

}