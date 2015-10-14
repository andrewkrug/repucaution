<?php

class Post extends DataMapper {

    var $table = 'posts';    
    
    var $has_one = array(
        // user
/*
        'post_category' => array(
            'model' => 'post_category',
            'join_other_as' => 'category',
        ), */
    );

    var $has_many = array(
     //   'media',
        'post_social',
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}