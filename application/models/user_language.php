<?php

/**
 * Class User_language
 */
class User_language extends DataMapper {

    var $table = 'user_languages';


    var $has_one = array(
        // user
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }


    /**
     * @access public
     *
     * @param $user_id
     * @param $language
     *
     * @return bool
     */
    public static function set_user_language($user_id, $language) {
        $user_language = new User_language();
        $user_language = $user_language->where('user_id', $user_id)
            ->get();
        $user_language->user_id = $user_id;
        $user_language->language = $language;
        return $user_language->save();
    }

    /**
     * @access public
     *
     * @param        $user_id
     * @param string $default
     *
     * @return string
     */
    public static function get_user_language($user_id, $default = '') {
        $user_language = new User_language();
        $user_language = $user_language->where('user_id', $user_id)->get();
        if(!$default) $default = 'en';
        return $user_language->exists() ? $user_language->language : $default;
    }
}