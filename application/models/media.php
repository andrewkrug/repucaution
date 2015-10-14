<?php

class Media extends DataMapper {

    // type - "image", "video"

    var $table = 'media';
    

    var $has_one = array(
     //   'social_post'
        // user
    );
    var $has_many = array(
        'social_post' => array(
            'class' => 'social_post',
            'join_self_as' => 'media',
            'join_other_as' => 'post',
            'join_table' => 'posts_media'
        ),
        'social_post_cron' => array(
            'class' => 'social_post_cron',
            'join_self_as' => 'media',
            'join_other_as' => 'post',
            'join_table' => 'posts_cron_media'
        ),
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

}