<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');

/**
 * TwitterFollower model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $follower_id
 * @property integer $last_check
 * @property bool need_follow
 * @property integer $access_token_id
 *
 * @property-read  DataMapper $user
 */
class Instagram_follower extends DataMapper
{

    var $has_one = array(
        'user'
    );
    var $has_many = array();

    var $validation = array();

    var $table = 'instagram_followers';

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
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
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
     * @param bool $need_follow
     */
    public function setNeedFollow($need_follow) {
        $this->need_follow = $need_follow;
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
     * @return bool
     */
    public function save($object = '', $related_field = '')
    {
        $this->last_check = time();
        $result = parent::save($object, $related_field);
        return $result;
    }
}
