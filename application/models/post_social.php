<?php

class Post_social extends DataMapper {

    // social - "facebook", "twitter"


    var $table = 'post_socials';    
    
    var $has_one = array(
        'post',
    );

    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}