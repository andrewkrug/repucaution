<?php

/**
 * Class Config
 *
 * @author Ajorjik
 */
class Available_config extends DataMapper {

    var $table = 'available_configs';
    var $primary_key = array('type', 'config_id');

    var $has_one = array(
        'config'
    );
    var $has_many = array();

    var $validation =  array ();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function getByType($type) {
        $available_configs = Available_config::create()->where('type', $type)->get();
        $configs = array();
        foreach($available_configs as $available_config) {
            $configs[] =  $available_config->config->get();
        }
        return $configs;
    }

    /**
     * @param string    $type
     * @param array     $exclude
     *
     * @return array
     */
    public static function getByTypeAsArray($type, $exclude = array()) {
        $available_configs = Available_config::create()->where('type', $type)->get();
        $configs = array();
        foreach($available_configs as $available_config) {
            $_config = $available_config->config->get()->to_array();
            if(!in_array($_config['key'], $exclude)) {
                $configs[] =  $_config;
            }
        }
        return $configs;
    }

    /**
     * @param array $keys
     * @param User  $user
     * @param int   $profile_id
     *
     * @return array
     */
    public static function getByKeysAsArray($keys, $user, $profile_id) {
        $config = Config::create()->where('key', $keys[0])->get(1);
        $return = array();
        $available_configs = $config->available_config->get()->all_to_array();
        foreach($available_configs as $available_config) {
            $tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray($available_config['type'], $user->id, $profile_id);
            $config = new Config($available_config['config_id']);
            $config = $config->to_array();
            foreach($tokens as $token) {
                $_element = array(
                    'token' => $token,
                    'config' => $config,
                    'values' => array()
                );
                foreach($keys as $key) {
                    $_element['values'][$key] =
                        $user->ifUserHasConfigValue($key, $token['id']);
                }
                $return[$available_config->type][] = $_element;
            }
        }
        return $return;
    }
}