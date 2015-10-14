<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');

/**
 * TwitterFollower model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $token_id
 * @property integer $count
 * @property string $date
 *
 * @property  DataMapper $user
 */
class Number_of_added_users_twitter extends DataMapper
{

    var $has_one = array(
        'user'
    );
    var $has_many = array();

    var $validation = array();

    var $table = 'number_of_added_users_twitter';

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
     * @param string $object
     * @param string $related_field
     * @return bool
     */
    public function save($object = '', $related_field = '')
    {
        $result = parent::save($object, $related_field);
        return $result;
    }
}
