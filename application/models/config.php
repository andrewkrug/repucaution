<?php

/**
 * Class Config
 *
 * @author Ajorjik
 */
class Config extends DataMapper {

    var $table = 'configs';

    var $has_one = array(
        'user'
    );
    var $has_many = array(
        'available_config',
        'user_config'
    );

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function getConfigType($key) {
        switch($key) {
            case 'max_daily_auto_follow_users_by_search':
                return 'number';
                break;
            case 'welcome_message_text':
                return 'text';
                break;
            default:
                return 'bool';
                break;
        }
    }
}