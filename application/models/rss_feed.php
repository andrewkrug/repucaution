<?php

class Rss_feed extends DataMapper {

    var $table = 'rss_feeds';    
    
    var $has_one = array(
//        'rss_industry' => array(
//            'join_other_as' => 'industry',
//        ),
        'user' => array(
            'join_table' => 'rss_feeds_users'
        )
    );
        
    var $has_many = array(
        'rss_feeds_users',
        // user - through rss_feeds_users
    );

    var $error_prefix = '<span class="message-error">';
    var $error_suffix = '</span>';

    var $validation = array(
        'title' => array(
            'label' => 'Title',
            'rules' => array('required', 'trim', 'max_length' => 100),
        ),
        'link' => array(
            'label' => 'Link URL',
            'rules' => array('required', 'trim', 'max_length' => 250),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Get the only related industry feed for user
     *
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return Rss_feed
     */
    public function user_industry_feed($user_id, $profile_id) {
        $feed = $this
            ->where('industry_id IS NOT NULL')
            ->where_related('rss_feeds_users', 'user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->get(1);

        return $this->where('industry_id', $feed->industry_id)->get(); //fix for situation then industries was added after user save his industry

    }

    /**
     * Get all user custom feeds
     *
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return Rss_feed
     */
    public function user_custom_feeds($user_id, $profile_id) {
        return $this
            ->where('industry_id IS NULL')
            ->where('profile_id', $profile_id)
            ->where_related('rss_feeds_users', 'user_id', $user_id)
            ->order_by('title ASC')
            ->get();
    }

    /**
     * Get all user's feeds
     *
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return Rss_feed
     */
    public function user_feeds($user_id, $profile_id) {
        return $this
            ->where_related('rss_feeds_users', 'user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->order_by('title ASC')
            ->get()
            ->all_to_single_array('title');
    }

    /**
     * Save rss industry for user
     *
     * @param $industry_id (int)
     * @param $user_id     (int)
     * @param $profile_id
     */
    public static function update_rss_industry($industry_id, $user_id, $profile_id) {
        // get feed for industry
        $feeds = Rss_feed::inst()->where('industry_id', $industry_id)->get();

        $rss_feeds_users = Rss_feeds_users::inst();

        if ($feeds->result_count() > 0) {
            // clear all existing rss indurstries
            $rss_feeds_users
                ->where(array(
                    'user_id' => $user_id
                ))
                ->where_related('rss_feed', 'industry_id IS NOT NULL')
                ->where_related('rss_feed', 'profile_id', $profile_id)
                ->get()
                ->delete_all();

            foreach ($feeds as $_feed) {
                $sql = "INSERT IGNORE INTO `{$rss_feeds_users->table}` SET `user_id` = ?, `rss_feed_id` = ?";
                $_feed->db->query($sql, array(
                    $user_id,
                    $_feed->id
                ));
            }
            // save new industry


        } else {
            // clear all existing rss indurstries
            $rss_feeds_users
                ->where(array(
                    'user_id' => $user_id
                ))
                ->where_related('rss_feed', 'industry_id IS NOT NULL')
                ->where_related('rss_feed', 'profile_id', $profile_id)
                ->get()
                ->delete_all();
        }
    }

    /**
     * Run validation rules on each passed custom form
     * return each model in array and errors if exist
     * 
     * @param $feeds (array) - key - form block id, value - array('title' => 'new title', 'link' => 'new link')
     * @return array (errors (array), models(array))
     */
    public static function validate_rss_custom_pack($feeds) {

        $errors = array();
        $models = array();

        foreach($feeds as $feed_key => $feed_value) {

            $rss_feed = new self;
            $rss_feed->from_array($feed_value, array('title', 'link'));
            $rss_feed->validate();

            if ( ! $rss_feed->valid) {
                foreach($rss_feed->error->all as $err_key => $err_value) {
                    $errors[ $feed_key ][ $err_key ] = $err_value; 
                }
            }

            $models[] = $rss_feed;
        }

        return array($errors, $models);
    }

    /**
     * Save each rss custom feed from $models array after validation
     *
     * @param $models (array of Rss_feed)
     * @param $user_id
     * @param $profile_id
     */
    public static function save_rss_custom_pack($models, $user_id, $profile_id) {
        foreach($models as $model) {
            $model->profile_id = $profile_id;
            $model->save();
            $rss_feeds_users = new Rss_feeds_users;
            $rss_feeds_users->user_id = $user_id;
            $rss_feeds_users->rss_feed_id = $model->id;
            $rss_feeds_users->save();
        }
    }

    public static function remove($id, $user_id, $profile_id) {
        $feed = self::inst()
            ->where('id', $id)
            ->where_related('rss_feeds_users', 'user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->get();

        if ($feed->exists()) {

            return Rss_feeds_users::inst()
                ->where(array(
                    'rss_feed_id' => $feed->id,
                    'user_id' => $user_id
                ))
                ->get()
                ->delete() 
                &&
                $feed->delete();

        } else {
            return FALSE;
        }
    }

}