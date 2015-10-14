<?php

class Facebook_Fanpage extends DataMapper {


    var $table = 'facebook_fanpages';

    var $has_one = array(
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Used to add new record in Facebook fanpages table
     * Fanpage id - selected by user facebook fanpage id
     *
     * @access public
     *
     * @param      $user_id
     * @param      $page_facebook_id
     * @param null $profile_id
     * @param      $access_token_id
     */
    public function save_selected_page($user_id, $page_facebook_id, $profile_id = null, $access_token_id = null) {
        $page = $this->where('user_id', $user_id)
            ->get();
        $page->user_id = $user_id;
        $page->fanpage_id = $page_facebook_id;
        $page->profile_id = $profile_id;
        $page->access_token_id = $access_token_id;
        $page->save();
    }

    /**
     * Used to get current user main facebook fanpage
     *
     * @access public
     * @param $user_id
     * @return DataMapper
     */
    public function get_selected_page($user_id, $access_token_id) {
        $page = $this->where(array(
            'user_id' => $user_id,
            'access_token_id' => $access_token_id
        ))->get();
        return $page;
    }

}