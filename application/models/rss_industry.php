<?php

class Rss_industry extends DataMapper {

    var $table = 'rss_industries';    
    
    var $has_one = array(
        // user
    );

    var $has_many = array(
        'rss_feed' => array(
            'join_self_as' => 'industry',
        ),
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public function inst($id = NULL) {
        return new self($id);
    }

    public static function for_dropdown($message = 'Select industry ...') {
        $industries = self::inst()
            ->where_related('rss_feed', 'id IS NOT NULL')
            ->order_by('name ASC')
            ->get()
            ->all_to_single_array('name');
        return array('0' => $message) + $industries;
    }

}