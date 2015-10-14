<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');

/**
 * TwitterFollower model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $follower_id
 * @property integer $last_check
 * @property bool $need_message
 * @property bool $need_follow
 * @property integer $unfollow_time
 * @property bool $still_follow
 * @property integer $start_follow_time
 * @property integer $end_follow_time
 * @property integer $access_token_id
 *
 * @property-read  DataMapper $user
 */
class Twitter_follower extends DataMapper
{

    var $has_one = array(
        'user'
    );
    var $has_many = array();

    var $validation = array();

    var $table = 'twitter_followers';

    /**
     * Initialize User model
     *
     * @access public
     *
     * @param $id (int) - user id
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user->get();
    }

    /**
     * @param $user_id
     * @return DataMapper
     */
    public function getByUserId($user_id) {
        return $this->where('user_id', $user_id)->get();
    }

    /**
     * @param int|string $user_id
     */
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    /**
     * @param int|string $follower_id
     */
    public function setFollowerId($follower_id) {
        $this->follower_id = $follower_id;
    }

    /**
     * @param bool $need_message
     */
    public function setNeedMessage($need_message) {
        $this->need_message = $need_message;
    }

    /**
     * @param bool $need_follow
     */
    public function setNeedFollow($need_follow) {
        $this->need_follow = $need_follow;
    }

    /**
     * @param bool $still_follow
     */
    public function setStillFollow($still_follow) {
        $this->still_follow = $still_follow;
    }

    /**
     * @param integer $unfollow_time
     */
    public function setUnfollowTime($unfollow_time) {
        $this->unfollow_time = $unfollow_time;
    }

    /**
     * @param integer $start_follow_time
     */
    public function setStartFollowTime($start_follow_time) {
        $this->start_follow_time = $start_follow_time;
    }

    /**
     * @param integer $end_follow_time
     */
    public function setEndFollowTime($end_follow_time) {
        $this->end_follow_time = $end_follow_time;
    }

    /**
     * @param integer $access_token_id
     */
    public function setAccessTokenId($access_token_id) {
        $this->access_token_id = $access_token_id;
    }

    /**
     * @param string $object
     * @param string $related_field
     * @param bool $set_last_check
     * @return bool
     */
    public function save($object = '', $related_field = '', $set_last_check = true)
    {
        if ($set_last_check) {
            $this->last_check = time();
        }
        $result = parent::save($object, $related_field);
        return $result;
    }
}
