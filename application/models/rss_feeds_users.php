<?php

class Rss_feeds_users extends DataMapper {

    var $table = 'rss_feeds_users';    
    
    var $has_one = array(
        'rss_feed',
        'user'
    );
        
    var $has_many = array(

    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

}