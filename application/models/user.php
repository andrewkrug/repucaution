<?php if (!defined('BASEPATH'))
    dir('No direct cript access allowed');

/**
 * User model
 *
 * @property Twitter_follower $twitter_follower
 * @property Number_of_added_users_twitter $number_of_added_users_twitter
 */
class User extends DataMapper
{

    const STATUS_INVITE = 2;

    var $has_one = array();
    var $has_many = array(
        'mention',
        'mention_keyword',
        'crm_directory',
        'subscription',
        'user'         => array(
            'class'       => 'user',
            'other_field' => 'manager_user',
            'reciprocal'  => true
        ),
        'manager_user' => array(
            'class'       => 'user',
            'other_field' => 'user'
        ),
        'group'        => array(
            'join_table' => 'users_groups'
        ),
        'payment_transaction',
        'user_config' => array(
            'class' => 'user_config',
            'other_field' => 'user',
            'join_table' => 'user_configs'
        ),
        'twitter_follower',
        'instagram_follower',
        'user_search_keyword',
        'number_of_added_users_twitter',
        'social_group',
        'access_token',
        'rss_feed'       => array(
            'join_table' => 'rss_feeds_users'
        ),
        'rss_feeds_users'
    );

    var $validation = array();

    var $table = 'users';

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
     * Find user by email
     *
     * @param $email
     *
     * @return bool|User
     */
    static public function findByEmail($email)
    {
        $user = new User();
        $user->get_by_email($email);

        if ($user->exists()) {
            return $user;
        }

        return false;
    }

    static public function withActiveSubscription() {
        $time = date('Y-m-d', time());
        $user = new User();
        $users = $user
            ->where_related('subscription', 'start_date <=', $time)
            ->where_related('subscription', 'end_date >=', $time)
            ->where_related('subscription', 'status', Subscription::STATUS_ACTIVE)->get();
        return $users;
    }

    /**
     * Check user have active subscription
     *
     * @return bool
     */
    public function hasActiveSubscription()
    {
        $time = date('Y-m-d', time());
        $result = (bool)$this->subscription->where('start_date <=', $time)
                                           ->where('end_date >=', $time)
                                           ->where('status', Subscription::STATUS_ACTIVE)->count();

        return $result;
    }

    /**
     * Get last active user's subscription
     *
     * @return object Payment | null
     */
    public function getLastSubscription()
    {
        $subscription = $this->subscription->where('status', Subscription::STATUS_ACTIVE)->order_by('end_date', 'DESC')->get(1);
        $result = ($subscription->exists()) ? $subscription : null;

        return $result;
    }

    /**
     * Search users
     *
     * @param string $text
     * @param int|null $filter
     *
     * @param null $place
     * @param int $limit
     * @param string $offset
     * @param null $ownerId
     * @return DataMapper
     */
    public function search($text, $filter, $place = null, $limit = 1, $offset = '', $ownerId = null)
    {
        $result = $this;
        if (!empty($text)) {
            $result->like('username', $text, $place);
        }

        if (!($filter === '')) {
            $result->where('active', $filter);
        }

        if ($ownerId) {
            $result->join_related('manager_user')->where('manager_user_id', $ownerId);
        }

        return $result->limit($limit, $offset)->get();

    }

    /**
     * Get users of manager
     *
     * @param null $limit
     * @param string $offset
     * @param null $id
     * @return DataMapper
     */
    public function getManagerUsers($limit = null, $offset = '', $id = null)
    {
        if (!$id) {
            $id = $this->id;
        }

        return $this->include_related('user')->where('manager_user_id', $id)->get($limit, $offset);
    }

    /**
     * Get members are not in manager account
     *
     * @param null|int $id manager id
     * @param $group
     *
     * @return mixed
     */
    public function getFreeUsersDropdown($id = null, $group)
    {
        if (!$id) {
            $id = $this->id;
        }
        $sql = "SELECT u.*
                FROM `users` u
                INNER JOIN users_groups ug ON u.id = ug.user_id
                LEFT JOIN `manager_users_users` muu on (u.id = muu.user_id and muu.manager_user_id = ?)
                WHERE muu.manager_user_id IS NULL AND ug.group_id = ?
                ORDER BY `id` ASC";

        $binds = array(
            $id,
            $group
        );

        return $this->query($sql, $binds)->all_to_single_array('username');
    }

    /**
     * Get actual invited user by code
     *
     * @param $code
     * @param $timeLimit
     *
     * @return DataMapper
     */
    public function getInviteUser($code, $timeLimit)
    {
        $result = $this->where('invite_code', md5($code))->where('invite_time >', $timeLimit)->get();

        return $result;

    }

    /**
     * Check if user was invited and time of invite over limit
     *
     * @return bool
     */
    public function isInvitedAndNotActual()
    {
        get_instance()->load->config('manage_users');
        $timelimit = time() - get_instance()->config->config['invite_timelimit'];

        return ($this->active == self::STATUS_INVITE && (int)$this->invite_time < $timelimit);
    }

    /**
     * Get active plan of user
     *
     * @return mixed
     */
    public function getActivePlan()
    {
        $time = date('Y-m-d', time());
        $activeSubscription = $this->subscription->where('start_date <=', $time)
            ->where('end_date >=', $time)
            ->where('status', Subscription::STATUS_ACTIVE)->get();

        return ($activeSubscription->exists()) ? $activeSubscription->plan->get() : null;
    }

    /**
     * @param int $limitDays
     * @return bool
     */
    public function isTrialPlanEnds($limitDays = 3) {
        $time = date('Y-m-d', time());
        $activeSubscription = $this->subscription->where('start_date <=', $time)
            ->where('end_date >=', $time)
            ->where('end_date <= \''. $time . '\' + INTERVAL '.$limitDays.' DAY')
            ->where('status', Subscription::STATUS_ACTIVE)->get();

        return ($activeSubscription->exists()) ? $activeSubscription->plan->get()->isTrial() : false;
    }

    /**
     * Get array of roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->group->get()->all_to_single_array('name');
    }


    /**
     * Get count if current mention keywords
     *
     * @return int
     */
    public function getMentionKeywordsCount()
    {
        return (int)$this->mention_keyword->where('is_deleted', 0)->count();
    }

    /**
     * Get count if current crm directories
     *
     * @return int
     */
    public function getCrmDirectoriesCount()
    {
        return (int)$this->crm_directory->where('is_deleted', 0)->count();
    }

    /**
     * Get count of current collaborators
     *
     * @return int
     */
    public function getCollaboratorsCount()
    {
        return (int)$this->user->where('manager_user_id', $this->id)->count();
    }

    /**
     * Check if this user is manager of user with userId
     *
     * @param $userId
     * @return mixed
     */
    public function isManager($userId)
    {
        return $this->user->where('user_id', $userId)->get();
    }

    /**
     * Get users`s emails by groups
     *
     * @param array $groups
     * @return array
     */
    public function getEmailsByGroups($groups)
    {
        return $this->where_in_related('group', 'id', $groups)->get()->all_to_array('email');
    }

    /**
     * Get active major user in group
     *
     * @param $groupId
     * @return mixed
     */
    public function getMajorUserInGroup($groupId)
    {
        return $this->manager_users->where_related('group', 'id', $groupId)->where('active', true)->get();
    }

    /**
     * Check if user has config value
     *
     * @param $key
     * @param null|integer $access_token_id
     * @return User_config|bool
     */
    public function ifUserHasConfig($key, $access_token_id = null)
    {
        $userConfig = $this->user_config;
        if ($access_token_id) {
            $userConfig = $userConfig->
                where('access_token_id', $access_token_id);
        }
        $userConfig = $userConfig
            ->where_related('config', 'key', $key)
            ->get(1);

        return ($userConfig->id) ? $userConfig : false;
    }

    /**
     * Check if user has config
     *
     * @param string $key
     * @param null|integer $access_token_id
     * @return bool
     */
    public function ifUserHasConfigValue($key, $access_token_id = null)
    {
        return ($userConfig = $this->ifUserHasConfig($key, $access_token_id)) ? $userConfig->value : false;
    }

    /**
     * Set user config
     *
     * @param $key
     * @param $value
     * @param null $access_token_id
     * @return bool|User_config
     * @throws Exception
     */
    public function setConfig($key, $value, $access_token_id = null)
    {
        $config = Config::get_by_key($key);
        if (!$config->id) {
            throw new Exception('There is no config with this key');
        }

        if (!$userConfig = $this->ifUserHasConfig($key, $access_token_id)) {
            $userConfig = new User_config();
        }
        $userConfig->value = $value;
        $userConfig->access_token_id = $access_token_id;
        $userConfig->save(array('user' => $this, 'config' => $config));

        return $userConfig;
    }

    /**
     * Return true if user have twitter follower with this id.
     *
     * @param $follower_id
     * @param $access_token_id
     *
     * @return bool
     */
    public function isUserHasTwitterFollower($follower_id, $access_token_id) {
        /* @var Twitter_follower $twitterFollower */
        $twitterFollower = $this
            ->twitter_follower
            ->where('follower_id', $follower_id)
            ->where('access_token_id', $access_token_id)
            ->get(1);
        return $twitterFollower->exists();
    }

    /**
     * @param $profile_id
     *
     * @return User_search_keyword[]
     */
    public function getUserSearchKeywords($profile_id) {
       $user_search_keyword = $this->user_search_keyword
            ->where(array(
                'is_deleted' => 0,
                'profile_id' => $profile_id
            ))
            ->get();
       return $user_search_keyword;
    }

    /**
     * @param $access_token_id
     *
     * @return Number_of_added_users_twitter
     */
    public function getDateToAddUserTwitter($access_token_id) {
        return $this->number_of_added_users_twitter
            ->where(array(
                'token_id' => $access_token_id
            ))->order_by('date', 'desc')
            ->get(1);
    }
}
