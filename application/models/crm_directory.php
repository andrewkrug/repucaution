<?php

class Crm_directory extends DataMapper {

    var $table = 'crm_directories';


    var $has_one = array(
        'user',
        'profile' => array(
            'class' => 'social_group'
        )
    );
    var $has_many = array('crm_directory_activity');

    var $validation = array(
        'firstname' => array(
            'label' => 'Firstname',
            'rules' => array('trim', 'required'),
        ),
        'lastname' => array(
            'label' => 'Lastname',
            'rules' => array('trim', 'required'),
        ),
        'email' => array(
            'label' => 'Email',
            'rules' => array('trim', 'valid_email')
        ),
        'phone' => array(
            'label' => 'Phone',
            'rules' => array('trim', 'numeric'),
        ),
        'website' => array(
            'label' => 'Website',
            'rules' => array('trim', 'website'),
        ),
        'facebook_link' => array(
            'label' => 'Facebook link',
            'rules' => array('trim', 'fblink'),
        ),
        'twitter_link' => array(
            'label' => 'Twitter link',
            'rules' => array('trim', 'twlink'),
        ),
        'instagram_link' => array(
            'label' => 'Instagram link',
            'rules' => array('trim', 'inlink'),
        ),
        'other_fields' => array(
            'label' => 'Include & Exclude fields',
            'rules' => array('trim', 'max_length' => 2000),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);//var_dump(get_included_files());die;
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Validation rule website
     *
     * @param $field
     * @return bool|string
     */
    public function _website($field)
    {
        $pattern = '/www\.[a-zA-Z0-9\.\/\?\-_=#]+\.([a-zA-Z0-9\.\/\?\:@\-_=#])*/';
        preg_match($pattern, $this->{$field}, $match);
        if (!empty($match)) {
            return true;
        }

        return ucfirst($field)." value is not a valid, example www.example.com";

    }

    /**
     * Validation rule facebook link
     *
     * @param $field
     * @return bool|string
     */
    public function _fblink($field)
    {
        $pattern = '/^https:\/\/(www\.)?facebook\.com\/[A-Za-z0-9_\.]*$/';
        preg_match($pattern, $this->{$field}, $match);
        if (!empty($match)) {
            return true;
        }

        return ucfirst($field)." value is not a valid, example https://facebook.com/username";

    }

    /**
     * Validation rule instagram link
     *
     * @param $field
     * @return bool|string
     */
    public function _inlink($field)
    {
        $pattern = '/^https:\/\/(www\.)?instagram\.com\/[A-Za-z0-9_\.]*$/';
        preg_match($pattern, $this->{$field}, $match);
        if (!empty($match)) {
            return true;
        }

        return ucfirst($field)." value is not a valid, example https://instagram.com/username";

    }

    /**
     * Validation rule twitter link
     *
     * @param $field
     * @return bool|string
     */
    public function _twlink($field)
    {
        $pattern = '/^https:\/\/(www\.)?twitter\.com\/[A-Za-z0-9_]*$/';
        preg_match($pattern, $this->{$field}, $match);
        if (!empty($match)) {
            return true;
        }

        return ucfirst($field)." value is not a valid, example https://twitter.com/username";

    }

    /**
     * Get user directories
     *
     * @param $params
     * @param null $limit
     * @param null $offset
     * @return DataMapper
     */
    public function getUserDirectories($params, $limit = null, $offset = null)
    {
        $queryParams = array_merge($params, array('is_deleted' => 0));

        $likesArray = array('username', 'company');
        foreach ($likesArray as $k => $v) {
            if (!empty($queryParams[$v])) {
                $this->like($v, $queryParams[$v]);
                unset($queryParams[$v]);
            }
        }

        return $this->where($queryParams)->order_by('username', 'desc')->get($limit, $offset);
    }

    /**
     * Fill Crm_directory from data array
     *
     * @param $data
     * @return $this
     */
    public function fillFromArray($data)
    {
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
            $this->username = $this->firstname.' '.$this->lastname;
        }

        return $this;
    }

    /**
     * Check if user is owner of directory
     *
     * @param $userId
     * @return bool
     */
    public function isUser($userId)
    {
        return $this->user_id == $userId;
    }


    /**
     * Convert input data for other fields to database friendly type
     * 
     * @param array $data - post data 
     * @return string - serialized array
     */
    public function prepare_other_fields($data) {
        $result = array();
        foreach (array('include', 'exclude') as $key) {
            $arr = explode(',', Arr::get($data, $key, array()));    
            $result[$key] = array_filter(array_map('trim', $arr));
        }
        return serialize($result);
    }

    /**
     * Unserialize other fields (include/exclude words), return by key as string or array
     *
     * @param bool|FALSE|string $key       'exclude' or 'include'
     * @param bool              $as_string - return data as comma-separated string
     *
     * @return array|string
     */
    public function get_other_fields($key = FALSE, $as_string = FALSE) {
        $data = unserialize($this->other_fields);
        if ($key) {
            $data = $data[$key];
            return $as_string ? implode(', ', $data) : $data;
        }
        return $data;
    }

    /**
     * Set all users's keywords as deleted, except with ids
     *
     * @param int   $user_id
     * @param       $profile_id
     * @param array $exclude_ids - do not set keywords as deleted with ids
     *
     * @return bool
     */
    public function setDeleted($user_id, $profile_id, $exclude_ids = array(0)) {

        return $this
            ->where('user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->where_not_in('id', $exclude_ids)
            ->update(array(
                'is_deleted' => 1,
            ));
    }

    /**
     * Check if user has directories
     *
     * @param $user_id
     * @param $profile_id
     *
     * @return bool
     */
    public function hasDirectories($user_id, $profile_id) {
        return $this
            ->where('is_deleted', 0)
            ->where('user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->count() > 0;
    }

    /**
     * If user has requested directories
     *
     * @param int $user_id
     * @param     $profile_id
     *
     * @return bool
     */
    public function hasRequested($user_id, $profile_id) {
        return $this
            ->where('is_deleted', 0)
            ->where('user_id', $user_id)
            ->where('profile_id', $profile_id)
            ->where('requested_at IS NOT NULL')
            ->count() > 0;
    }

    /**
     * Has directory activities of social
     *
     * @param $social
     * @return mixed
     */
    public function hasSocialActivities($social)
    {
        return $this->crm_directory_activity->where('social', $social)->get()->exists();
    }


    /**
     * Get all crm directories for cron update
     *
     * @return array
     */
    public function getForUpdate()
    {
        $today = date('U', strtotime('today'));
        return $this
            ->where('is_deleted', 0)
            ->group_start()
                ->where('requested_at <', $today)
                ->or_where('requested_at IS NULL')
            ->group_end()
            ->get();
    }

    /**
     * Get socials that were already grabbed for crm directory
     *
     * @return array
     */
    public function get_grabbed_socials_as_array()
    {
        $grabbed_socials = $this->grabbed_socials 
            ? explode(',', $this->grabbed_socials)
            : array();
        $grabbed_socials = array_filter(array_unique($grabbed_socials));
        return $grabbed_socials;
    }

}
