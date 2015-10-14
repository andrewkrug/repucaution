<?php

class Post_category extends DataMapper {

    var $table = 'post_categories';    
    
    var $has_one = array();

    var $has_many = array(
        'post' => array(
            'join_self_as' => 'category',
        ),
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}