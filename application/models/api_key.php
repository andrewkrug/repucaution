<?php

class Api_key extends DataMapper {

    var $table = 'api_keys';

    var $has_one = array();
    var $has_many = array();
    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public static function value($social, $key = NULL) {
        $rows = self::inst()->where('social', $social);
        if ($key) {
            return $rows->where('key', $key)->get(1)->value;
        }
        $result = array();
        foreach ($rows->get() as $row) {
            $result[$row->key] = $row->value;
        }
        return $result;
    } 

    public static function build_config($social, $from_file = array()) {
        $from_database = self::value($social);
        return array_merge($from_file, $from_database);
    }

    public static function has_empty() {
        return self::inst()
           ->where('value IS NULL')
           ->count() > 0;
    }

}