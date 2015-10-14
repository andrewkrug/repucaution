<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');

/**
 * TwitterFollower model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $user_id
 * @property bool $is_active
 *
 * @property Access_token   $access_token
 * @property Rss_feed       $rss_feed
 * @property User           $user
 */
class Social_group extends DataMapper
{

    var $has_one = array(
        'user'
    );

    var $has_many = array(
        'access_token' => array(
            'join_table' => 'social_groups_access_tokens'
        ),
    );

    var $validation = array(
        'name' => array(
            'label' => 'Name of the profile',
            'rules' => array(
                'trim',
                'max_length' => 100,
                'required'
            ),
        ),
        'description' => array(
            'label' => 'Description of the profile',
            'rules' => array(
                'trim',
                'required'
            ),
        ),
    );

    var $table = 'social_groups';

    /**
     * Initialize Social_group model
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
     * @param string $object
     * @param string $related_field
     * @return bool
     */
    public function save($object = '', $related_field = '')
    {
        $result = parent::save($object, $related_field);
        return $result;
    }

    public function has_account($id) {
        return $this->access_token->where('id', $id)->get()->exists();
    }

    public static function hasSocialAccountByType($id, $type) {
        $social_group = new Social_group($id);
        return $social_group->access_token->where('type', $type)->get()->exists();
    }

    public static function getAccountByTypeAsArray($id, $type) {
        $social_group = new Social_group($id);
        return $social_group->access_token->where('type', $type)->get()->all_to_array();
    }

    public function getTokenByType($type) {
        return $this->access_token->where('type', $type)->get(1);
    }

    public function getTokenByTypeAsArray($type) {
        return $this->access_token->where('type', $type)->get(1)->to_array();
    }

    /**
     * @param integer $user_id
     *
     * @return Social_group|null
     */
    public static function getActive($user_id) {
        $social_group = Social_group::create()->where(array(
            'is_active' => 1,
            'user_id' => $user_id
        ))->get(1);
        if(!$social_group->exists()) {
            $social_group = Social_group::create()->where(array(
                'user_id' => $user_id
            ))->get(1);
            if($social_group->exists()) {
                $social_group->is_active = true;
                $social_group->save();
            }
        }
        return ($social_group->exists()) ? $social_group : null;
    }
}
