<?php

class Social_value extends DataMapper {

    // type  - "facebook", "twitter"

    var $table = 'social_values';    
    
    var $has_one = array(
    );

    var $has_many = array();

    var $validation = array();

    private static $_user_values;

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }


    public function set_values($user_id, $profile_id, $post) {

        $where = array(
            'user_id' => $user_id,
            'profile_id' => $profile_id
        );

        if(isset($post['from'])) {
            $where['date >='] = $post['from'];
        }
        if(isset($post['to'])) {
            $where['date <='] = $post['to'];
        }
        if(isset($post['type'])) {
            $where['type'] = $post['type'];
        }

        $all_data = $this->where($where)->order_by('date')->get()->all_to_array();

        $values = array('facebook' => array(), 'twitter' => array(), 'linkedin' => array(), 'google'=> array(), 'likes_count' => 0, 'followers_count' => 0, 'conns_count' => 0, 'friends_count' => 0);
        foreach ($all_data as $_data) {
            //$_data['type'] == 'facebook' ? $values['likes_count'] += (int)$_data['value'] : $values['followers_count'] += (int)$_data['value'];
            $values[$_data['type']][$_data['date']] = (int)$_data['value'];
        }

        $values['followers_count'] = end($values['twitter']);
        $values['likes_count'] = end($values['facebook']);
        $values['conns_count'] = end($values['linkedin']);
        $values['friends_count'] = end($values['google']);
        
        $values['facebook'] = count($values['facebook']) > 0 ? $values['facebook'] : array('' => 0);
        $values['twitter'] = count($values['twitter']) > 0 ? $values['twitter'] : array('' => 0);
        $values['linkedin'] = count($values['linkedin']) > 0 ? $values['linkedin'] : array('' => 0);
        $values['google'] = count($values['google']) > 0 ? $values['google'] : array('' => 0);
        self::$_user_values = $values;
    }

    public function get_data( ) {
        return self::$_user_values;
    }


}