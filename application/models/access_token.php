<?php

/**
 * Class Access_token
 * 
 * @property integer $id
 * @property integer $user_id
 * @property string $token1
 * @property string $token2
 * @property string $instance_id
 * @property array $data
 * @property string $type
 * @property string $name
 * @property string $username
 * @property string $image
 *
 * @property Social_analytics $social_analytics
 * @property Social_group $social_group
 */
class Access_token extends DataMapper {

    public static $types = array(
        'facebook',
        'twitter',
        'google',
        'instagram',
        'linkedin',
        'youtube'
    );

    public static $types_with_tools = array(
        'twitter'
    );

    var $table = 'access_tokens';
    
    var $has_one = array(
        'user'
    );
    var $has_many = array(
        'social_group' => array(
            'join_table' => 'social_groups_access_tokens'
        ),
        'social_analytics'
    );

    var $validation = array();

    /**
     * @param null|integer $id
     */
    function __construct($id = NULL) {
        parent::__construct($id);
    }

    /**
     * @param null|integer $id
     * @return Access_token
     */
    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Get access token by passed type
     *
     * @param string        $type - "facebook", "twitter", "youtube", "google", "instagram"
     * @param integer|null  $user_id
     * @param integer|null  $profile_id
     *
     * @return Access_token[]
     */
    public function get_by_type($type, $user_id = null, $profile_id = null) {
        if(!$user_id && !$profile_id) {
            $tokens = $this->where('type', $type)->get();
        } elseif($user_id && !$profile_id) {
            $tokens = $this->where(array(
                'type' => $type,
                'user_id' => $user_id
            ))->get();
        } elseif (!$user_id && $profile_id) {
            $profile = new Social_group($profile_id);
            $tokens = $profile->access_token->where(array(
                'type' => $type
            ))->get();
        } else {
            $profile = new Social_group($profile_id);
            $tokens = $profile->access_token->where(array(
                'type' => $type,
                'user_id' => $user_id
            ))->get();
        }
        return is_array($tokens) ? $tokens : array($tokens);
    }

    /**
     * Get access token by passed type
     *
     * @param string        $type - "facebook", "twitter", "youtube", "google", "instagram"
     * @param integer|null  $user_id
     * @param integer|null  $profile_id
     *
     * @return array
     */
    public function get_array_by_type($type, $user_id = null, $profile_id = null) {
        if(!$user_id && !$profile_id) {
            $tokens = $this->where('type', $type)->get();
        } elseif($user_id && !$profile_id) {
            $tokens = $this->where(array(
                'type' => $type,
                'user_id' => $user_id
            ))->get()->all_to_array();
        } elseif (!$user_id && $profile_id) {
            $profile = new Social_group($profile_id);
            $tokens = $profile->access_token->where(array(
                'type' => $type
            ))->get()->all_to_array();
        } else {
            $profile = new Social_group($profile_id);
            $tokens = $profile->access_token->where(array(
                'type' => $type,
                'user_id' => $user_id
            ))->get()->all_to_array();
        }
        return $tokens;
    }

    /**
     * Get access token by passed type
     *
     * @param string $type - "facebook", "twitter", "youtube", "google", "instagram"
     * @param integer $user_id
     * @return Access_token
     */
    public function get_one_by_type($type, $user_id) {
        return $this->where('type', $type)->where('user_id', $user_id)->get(1);
    }

    /**
     * Get access token by passed type
     *
     * @param string $type - "facebook", "twitter", "youtube", "google", "instagram"
     * @param integer $user_id
     * @return Access_token
     */
    public function get_one_by_type_as_array($type, $user_id) {
        return $this->where('type', $type)->where('user_id', $user_id)->get(1)->to_array();
    }

    /**
     * Get tokens by type and user id
     *
     * @param string $type
     * @param integer $user_id
     *
     * @return Access_token
     */
    static public function getByTypeAndUserId($type, $user_id)
    {
        $access = new Access_token();
        return $access->get_one_by_type($type, $user_id);

    }

    /**
     * Get tokens by type, profile id and user id
     *
     * @param string  $type
     * @param integer $user_id
     * @param integer $profile_id
     *
     * @return Access_token
     */
    static public function getByTypeAndUserIdAndProfileId($type, $user_id, $profile_id)
    {
        $group = new Social_group($profile_id);
        return $group->access_token->get_one_by_type($type, $user_id);

    }

    /**
     * Get tokens by type and user id
     *
     * @param string $type
     * @param integer $user_id
     *
     * @return Access_token
     */
    static public function getAllByTypeAndUserIdAsArray($type, $user_id)
    {
        $access = new Access_token();
        return $access->get_array_by_type($type, $user_id);

    }

    /**
     * Get tokens by type and user id
     *
     * @param string  $type
     * @param integer $user_id
     * @param integer $profile_id
     *
     * @return Access_token
     */
    static public function getAllByTypeAndUserIdAndProfileIdAsArray($type, $user_id, $profile_id)
    {
        $group = new Social_group($profile_id);
        return $group->access_token->get_array_by_type($type, $user_id, $profile_id);

    }

    /**
     * Get tokens by type and user id
     *
     * @param string  $type
     * @param integer $user_id
     * @param integer $profile_id
     *
     * @return Access_token
     */
    static public function getOneByTypeAndUserIdAndProfileIdAsArray($type, $user_id, $profile_id)
    {
        $group = new Social_group($profile_id);
        return $group->access_token->get_one_by_type_as_array($type, $user_id);

    }

    /**
     * Get tokens by type and user id
     *
     * @param string $type
     *
     * @return Access_token
     */
    static public function getAllByType($type)
    {
        $access = new Access_token();
        return $access->get_by_type($type);
    }

    /**
     * Get tokens by type and user id
     *
     * @param string $type
     *
     * @return Access_token
     */
    static public function getAllByTypeAsArray($type)
    {
        $access = new Access_token();
        return $access->get_array_by_type($type);
    }

    /**
     * Get account info stored serialized
     * 
     * @return array
     */
    public function account_info() {
        
        $result = unserialize($this->data);
        
        return $result ? $result : array();
    }

    /**
     * Writing token to database
     *
     * @access public
     * @param array $tokens
     * @param string $type
     * @param integer $user_id
     * @return Access_token
     */
    public function add_token( $tokens = array(), $type, $user_id ) {
        $params = ($tokens['username']) ?
            array(
                'user_id' => $user_id,
                'type' => $type,
                'username' => $tokens['username']
            ) :
            array(
                'user_id' => $user_id,
                'type' => $type,
                'token1' => $tokens['token'],
                'token2' => $tokens['secret_token'],
                'data' => isset($tokens['data']) ? $tokens['data'] : null,
                'instance_id' => (isset($tokens['instance_id'])) ? $tokens['instance_id'] : null
            );
        /* @var Access_token $current_token */
        $current_token = $this->where($params)->get();
        $current_token->token1 = $tokens['token'];
        $current_token->token2 = $tokens['secret_token'];
        $current_token->type = $type;
        $current_token->user_id = $user_id;
        $current_token->name = $tokens['name'];
        $current_token->username = $tokens['username'];
        $current_token->image = $tokens['image'];
        if(isset($tokens['instance_id'])){
            $current_token->instance_id = $tokens['instance_id'];
        }
        $current_token->data = isset($tokens['data']) ? $tokens['data'] : null;
        $current_token->save();
        return $current_token;
    }

    /**
     * @param integer $user_id
     * @param array $allowed_types
     * @return array
     */
    public static function getTokensArray($user_id, $allowed_types = array()) {
        $access = new Access_token();
        $tokens = array();
        foreach (self::$types as $type) {
            if(empty($allowed_types) || in_array($type, $allowed_types)) {
                $tokens_array = $access->get_array_by_type($type, $user_id);
                if (!empty($tokens_array)) {
                    $tokens[$type] = $tokens_array;
                } else {
                    $tokens[$type] = array();
                }
            }
        }
        return $tokens;
    }

    /**
     * @param integer $user_id
     * @param null    $group_id
     * @param array   $exclude types
     *
     * @return array
     */
    public static function getTokensArrayForGroup($user_id, $group_id = null, $exclude = array()) {
        $access = new Access_token();
        $tokens = array();
        $group = new Social_group($group_id);
        foreach (self::$types as $type) {
            if(in_array($type, $exclude)) {
                continue;
            }
            $tokens_array = $access->get_array_by_type($type, $user_id);
            if (!empty($tokens_array)) {
                if ($group->exists()) {
                    foreach ($tokens_array as &$token) {
                        if ($group->access_token->where('id', $token['id'])->get()->exists()) {
                            $token['in_group'] = true;
                        } else {
                            $token['in_group'] = false;
                        }
                    }
                }
                $tokens[$type] = $tokens_array;
            } else {
                $tokens[$type] = array();
            }
        }
        return $tokens;
    }

    /**
     * @param integer $group_id
     * @param array   $exclude types
     * @param bool    $has_twitter_marketing_tools
     *
     * @return array
     */
    public static function getGroupTokensArray($group_id, $exclude = array(), $has_twitter_marketing_tools = false) {
        $tokens = array();
        foreach (self::$types as $type) {
            if(in_array($type, $exclude)) {
                continue;
            }
            $has_configs = (count(Available_config::getByTypeAsArray($type)) || $type == 'facebook')
                ? true
                : false;
            if($has_configs && $type == 'twitter') {
                $has_configs = $has_twitter_marketing_tools;
            }
            $tokens_array = Social_group::getAccountByTypeAsArray($group_id, $type);
            foreach($tokens_array as &$token) {
                $token['has_configs'] = $has_configs;
            }
            if (!empty($tokens_array)) {
                $tokens[$type] = $tokens_array;
            } else {
                $tokens[$type] = array();
            }
        }
        return $tokens;
    }

    /**
     * Get user youtube token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_youtube_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id, 
                'type' => 'youtube'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }

    /**
     * Get user youtube token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token
     */
    public function get_youtube_token($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id,
                'type' => 'youtube'
            )
        )->get(1);
        return $token;
    }
    
    /**
     * Get user youtube token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_google_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id, 
                'type' => 'google'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }

    /**
     * Get user facebook token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_facebook_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id,
                'type' => 'facebook'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }
    
    /**
     * Get user linkedin token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_linkedin_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id,
                'type' => 'linkedin'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }
    
    /**
     * Get user twitter token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_twitter_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id,
                'type' => 'twitter'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }
    
    /**
     * Get user instagram token
     *
     * @access public
     * @param integer $user_id
     * @return Access_token[]
     */
    public function get_instagram_tokens($user_id) {
        $token = $this->where(
            array(
                'user_id' => $user_id,
                'type' => 'instagram'
            )
        )->get();
        return is_array($token) ? $token : array($token);
    }

    /**
     * Check if user has connected GA acc
     * 
     * @return bool
     */
    public function connected() {
        return $this->exists()
            && $this->token1
            && $this->token2
            && $this->instance_id;
    }

    /**
     * Get google analytics visits for dashboard
     * @param array $default - array with default values for [0](array) and [1]string
     * @return array with two items
     * [0] - array with chart data
     * [1] - string with value
     */
    public function google_analytics_dashboard_visits($default = array(array(), '')) {
        if ( ! $this->connected()) {
            return $default;
        }

        $ci  = & get_instance();

        try {
            $ga_use = $ci->load->library('google_analytics/ga_use', array('token' => $this->token2));
        
            // get analytics visits chart for date range
            $ga_visits_chart_rows = $ga_use->rows('web', 'chart', $this->instance_id, '-30 days', 'today');
            $default[0] = isset($ga_visits_chart_rows['result']) ? $ga_visits_chart_rows['result'] : array(); // array with dates and values (string as float)    
 
            // get analytics visits count for date range
            $ga_visits_count_rows = $ga_use->rows('web', 'table', $this->instance_id, '-30 days', 'today');
            $default[1] = isset($ga_visits_count_rows['result'][0][0]) ? $ga_visits_count_rows['result'][0][0] : 0; // string as float or zero
 
        } catch (Exception $e) {}

        return $default;

    }

    /**
     * Check - is user have access to post into socials
     * Get Access Tokens for Facebook / Twitter from our database
     * Also need to check - is user select some Facebook fanpage
     *
     * @access public
     * @param $user_id
     * @return array
     */
    public function check_socials_access( $user_id )
    {
        $instagram_token = self::getByTypeAndUserId('instagram', $user_id);
        $linkedin_token = self::getByTypeAndUserId('linkedin', $user_id);
        $twitter_token = self::getByTypeAndUserId('twitter', $user_id);
        $facebook_token = self::getByTypeAndUserId('facebook', $user_id);
        $youtube_token = self::getByTypeAndUserId('youtube', $user_id);
        $fan_page = Facebook_Fanpage::inst()->get_selected_page($user_id, $this->id);
        
        return array(
            'is_authorized_in_facebook' => $facebook_token->exists(),
            'is_authorized_in_twitter' => $twitter_token->exists(),
            'is_have_fan_page' => $fan_page->exists(),
            'is_authorized_in_linkedin' => $linkedin_token->exists(),
            'is_authorized_in_youtube' => $youtube_token->exists(),
            'is_authorized_in_instagram' => $instagram_token->exists(),
        );
    }

    /**
     * Get socials that user has tokens for
     *
     * @param int   $user_id
     * @param       $profile_id
     *
     * @param array $exclude
     *
     * @return array
     */
    public function get_user_socials($user_id, $profile_id, $exclude=[]) {

        $profile = new Social_group($profile_id);
        $tokens = $profile->access_token->where('user_id', $user_id)->get();

        $valid_socials = Mention::$socials;
        $active_socials = array();

        foreach($tokens as $token) {
            if (in_array($token->type, $valid_socials) && !in_array($token->type, $exclude)) {
                $active_socials[] = $token->type;
            }
        }

        return $active_socials;
    }

    /**
     * Get crm socials that user has tokens for
     *
     * @param int   $user_id
     * @param int   $profile_id
     *
     * @param array $exclude
     *
     * @return array
     */
    public function get_crm_user_socials($user_id, $profile_id, $exclude = []) {

        $profile = new Social_group($profile_id);
        $tokens = $profile->access_token->where('user_id', $user_id)->get();

        $valid_socials = Crm_directory_activity::$socials;
        $active_socials = array();

        foreach($tokens as $token) {
            if (in_array($token->type, $valid_socials) && !in_array($token->type, $exclude)) {
                $active_socials[] = $token->type;
            }
        }

        return $active_socials;
    }
}
