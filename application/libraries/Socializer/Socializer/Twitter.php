<?php defined('BASEPATH') or die('No direct script access.');

require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/twitteroauth/twitteroauth.php';
require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/twitteroauth/OAuth.php';
require_once dirname(dirname(__FILE__)).'/vendors/twitteroauth/oauthdamnit.php';

class Socializer_Twitter
{

    const MAX_TWEET_LENGTH = 140;

    /**
     * CodeIgniter Core instance
     *
     * @var CI_Controller
     */
    private $_ci;

    /**
     * Twitter config from APPATH/config/social_credentials
     *
     * @var
     */
    private $_config;

    /**
     * Current user id
     *
     * @var
     */
    private $_user_id;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;


    /**
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');
        $this->_config = Api_key::build_config('twitter', $this->_ci->config->item('twitter'));
        $this->_user_id = $user_id;
        if (!$token) {
            $this->_token = Access_token::inst()->get_one_by_type('twitter', $this->_user_id)->to_array();
        } else {
            $this->_token = $token;
        }
    }

    /**
     * Used to set temporary credentials in Session
     * Get this credentials from Twitter API library
     *
     * @access public
     * @return string
     */
    public function set_temporary_credentials() {
        $connection = new TwitterOAuth($this->_config['consumer_key'], $this->_config['consumer_secret']);
        $temporary_credentials = $connection->getRequestToken();

        $this->_ci->session->set_userdata( 'oauth_token', $temporary_credentials['oauth_token'] );
        $this->_ci->session->set_userdata( 'oauth_token_secret', $temporary_credentials['oauth_token_secret'] );

        $redirect_url = $connection->getAuthorizeURL($temporary_credentials);
        return $redirect_url;
    }

    /**
     * Used to add new record to Access Tokens Table
     *
     * @access public
     *
     * @param $oauth_verifier - $_REQUEST['code'] from controller
     * @param $profile_id
     *
     * @return string
     * @throws Exception
     */
    public function add_new_account($oauth_verifier, $profile_id) {

        $oauth_token = $this->_ci->session->userdata('oauth_token');
        $oauth_token_secret = $this->_ci->session->userdata('oauth_token_secret');

        $connection = new TwitterOAuth($this->_config['consumer_key'], $this->_config['consumer_secret'], $oauth_token, $oauth_token_secret);
        $token_credentials = $connection->getAccessToken($oauth_verifier);

        $tokens = array(
            'token' => $token_credentials['oauth_token'],
            'secret_token' => $token_credentials['oauth_token_secret']
        );

        try{

            if (empty($this->_user_id)) {
                throw new Exception("There in no active user to connect to twitter.");
            }

            $tokens['username'] = $token_credentials['screen_name'];

            $socialFullInfo = $this->get_user_full_info($tokens['username']);
            if (empty($socialFullInfo->name)) {
                throw new Exception("Invalid twitter's user data. Please try to reconnect.");
            }
            $tokens['name'] = $socialFullInfo->name;
            $tokens['image'] = $socialFullInfo->profile_image_url;

            $access_token = new Access_token();
            $token = $access_token->add_token($tokens, 'twitter', $this->_user_id);

            if (!$token->exists()) {
                throw new Exception("Cant save twitter access data. Please try to reconnect.");
            }

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));

        } catch(Exception $e){
            throw $e;
        }

        $redirect_url = site_url('settings/socialmedia');
        return $redirect_url;
    }

    /**
     * Used to get user full info
     *
     * @access public
     * @param $username
     * @return mixed
     */
    public function get_user_full_info( $username ) {
        $params = array('screen_name' => $username);
        $user_info = $this->_api_call('https://api.twitter.com/1.1/users/show.json', $params);
        return $user_info;
    }

    /**
     * Used to get user Tweets feed
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_feed( $limit, $page ) {
        $feed_params = array('count' => $limit, 'page' => $page);
        $user_feed = $this->_api_call('https://api.twitter.com/1.1/statuses/home_timeline.json', $feed_params);
        return $user_feed;
    }

    /**
     * Used to get mentions to user
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_mentions( $limit, $page ) {
        $feed_params = array('count' => $limit, 'page' => $page);
        $user_feed = $this->_api_call('https://api.twitter.com/1.1/statuses/mentions_timeline.json', $feed_params);
        return $user_feed;
    }

    /**
     * Used to get tweets created by user
     *
     * @access public
     * @param $limit - count of tweets to display
     * @param $page - number of tweets page
     * @return mixed
     */
    public function get_user_tweets( $limit, $page ) {
        $feed_params = array('count' => $limit, 'page' => $page);
        $user_feed = $this->_api_call('https://api.twitter.com/1.1/statuses/user_timeline.json', $feed_params);
        return $user_feed;
    }

    /**
     * Used to get current user followers count
     *
     * @access public
     * @return int
     */
    public function get_followers_count() {
        $profile_data = $this->_api_call('https://api.twitter.com/1.1/users/show.json',
            array(
                'screen_name' => $this->_token['username']
            )
        );
        return isset($profile_data->followers_count) ? $profile_data->followers_count : 0;
    }

    /**
     * Used to get current user followers
     *
     * @access public
     * @return mixed
     */
    public function get_followers() {
        $profile_data = $this->_api_call('https://api.twitter.com/1.1/followers/ids.json',
            array(
                'screen_name' => $this->_token['username']
            )
        );
        return $profile_data;
    }

    /**
     * Used to get current user friends
     *
     * @access public
     * @return mixed
     */
    public function get_friends() {
        $profile_data = $this->_api_call('https://api.twitter.com/1.1/friends/ids.json',
            array(
                'screen_name' => $this->_token['username']
            )
        );
        return $profile_data;
    }

    /**
     * Used to get tweets
     *
     * @access public
     * @param array $args
     *  *'username' => username in twitter
     *  *'user_id' => user_id in twitter
     *  'exclude_replies' => default: none
     *  'trim_user' => default: none
     *  'only_one' => set true if need only one tweet. Default: false
     *  'count' => count of tweets. Default: 5
     *  'criteriaAnd' => array(
     *       array(
     *          'param_name' => retweet_count, favorite_count, favorited, retweeted, created_at (timestamp)
     *          'comparison_sign' => >,<,=,>=,<=,!=, between. Default: =
     *          'value' => value to compare
     *       )
     *  ),
     * 'criteriaOr' => equal to criteriaAnd
     *
     * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
     * @return array|string
     */
    public function get_tweets($args = array()) {
        $argsForCall = array();
        if (isset($args['username'])) {
            $argsForCall['screen_name'] = $args['username'];
        } elseif (isset($args['user_id'])) {
            $argsForCall['user_id'] = $args['user_id'];
        } else {
            $argsForCall['screen_name'] = $this->_token['username'];
        }
        if (isset($args['trim_user'])) {
            $argsForCall['trim_user'] = $args['trim_user'];
        }
        if (isset($args['exclude_replies'])) {
            $argsForCall['exclude_replies'] = $args['exclude_replies'];
        }
        if (isset($args['count'])) {
            $argsForCall['count'] = $args['count'];
        } else {
            $argsForCall['count'] = 5;
        }
        $tweets = $this->_api_call(
            'https://api.twitter.com/1.1/statuses/user_timeline.json',
            $argsForCall
        );
        if ($tweets->errors) {
            return 'Error: ' . $tweets->errors[0]->message . "\n\n";
        }
        $returnedData = array();
        foreach ($tweets as $tweet) {
            $isGood = true;
            foreach($args['criteriaAnd'] as $criteria) {
                if ($criteria['param_name'] != 'created_at') {
                    $param1 = $tweet->$criteria['param_name'];
                } else {
                    $date = new DateTime($tweet->$criteria['param_name']);
                    $param1 = $date->getTimestamp();
                }
                $isGood = $this->compare(
                    $param1,
                    $criteria['comparison_sign'],
                    $criteria['value']
                );
                if(!$isGood) {
                    break;
                }
            }
            if($isGood) {
                foreach ($args['criteriaOr'] as $criteria) {
                    if ($criteria['param_name'] != 'created_at') {
                        $param1 = $tweet->$criteria['param_name'];
                    } else {
                        $date = new DateTime($tweet->$criteria['param_name']);
                        $param1 = $date->getTimestamp();
                    }
                    $isGood = $this->compare(
                            $param1,
                            $criteria['comparison_sign'],
                            $criteria['value']
                    );
                }
                if($isGood) {
                    $returnedData[] = $tweet;
                    if(isset($args['only_one']) && $args['only_one'] == true) {
                        break;
                    }
                }
            }
        }
        return $returnedData;
    }

    /**
     * @param $arg1
     * @param string $comparison_sign >,<,=,>=,<=,!=
     * @param $arg2
     * @return bool
     */
    private function compare($arg1, $comparison_sign, $arg2) {
        switch ($comparison_sign) {
            case '>':
                return $arg1 > $arg2;
                break;
            case '<':
                return $arg1 < $arg2;
                break;
            case '>=':
                return $arg1 >= $arg2;
                break;
            case '<=':
                return $arg1 <= $arg2;
                break;
            case '!=':
                return $arg1 != $arg2;
                break;
            case 'between':
                return  $arg2[0] <= $arg1 && $arg1 <= $arg2[1];
                break;
            default:
                return $arg1 == $arg2;
                break;
        }
    }


    /**
     * Used to get user profile info
     *
     * @access public
     * @return mixed
     */
    private function get_user_info() {
        $user_info = $this->_api_call('https://api.twitter.com/1.1/account/settings.json');
        return $user_info;
    }

    /**
     * Used to create a new tweet
     *
     * @access public
     * @param $tweet_text
     * @param null|string $in_reply_to_status_id
     * @return mixed
     */
    public function tweet( $tweet_text, $in_reply_to_status_id = null ) {
        $params = array( 'status' => $tweet_text, );
        if($in_reply_to_status_id != null) {
            $params['in_reply_to_status_id'] = $in_reply_to_status_id;
        }

        return $this->_api_call_post('https://api.twitter.com/1.1/statuses/update.json', $params);
    }

    /**
     * Used to retweeted some tweet from user feed
     *
     * @access public
     * @param $tweet_id
     * @return mixed
     */
    public function retweet( $tweet_id ) {
        $retweet = $this->_api_call_post('https://api.twitter.com/1.1/statuses/retweet/'.$tweet_id.'.json');
        return $retweet;
    }

    /**
     * Used to undo-retweet some tweet from user feed
     *
     * @access public
     * @param $tweet_id
     * @return mixed
     */
    public function undo_retweet( $tweet_id ) {
        $retweet = $this->_api_call_get('https://api.twitter.com/1.1/statuses/retweets/'.$tweet_id.'.json', array('include_my_retweet' => 1));

        $my_retweet_id = $retweet[0]->id_str;
        return $this->_api_call_post('https://api.twitter.com/1.1/statuses/destroy/'.$my_retweet_id.'.json');
    }

    /**
     * Used to unfollow someone who user follow
     *
     * @access public
     * @param $follower_id
     * @return mixed
     */
    public function unfollow( $follower_id ) {
        return $this->_api_call_post('https://api.twitter.com/1.1/friendships/destroy.json', array(
            'user_id' => $follower_id
        ));
    }

    /**
     * Used to add some tweet from user feed to 'Favorites'
     *
     * @access public
     * @param $tweet_id
     * @return mixed
     */
    public function favorite( $tweet_id ) {
        $favorite = $this->_api_call_post('https://api.twitter.com/1.1/favorites/create.json', array('id' => $tweet_id));
        return $favorite;
    }

    /**
     * Used to undo-Favorite tweet
     *
     * @access public
     * @param $tweet_id
     * @return mixed
     */
    public function undo_favorite( $tweet_id ) {
        $favorite = $this->_api_call_post('https://api.twitter.com/1.1/favorites/destroy.json', array('id' => $tweet_id));
        return $favorite;
    }

    /**
     * Follow user
     *
     * @param array $args
     *  user_id - user id
     *  screen_name - user screen name
     *  text - message text
     *
     * @return mixed
     */
    public function direct_message($args)
    {
        $direct_message = $this->_api_call_post('https://api.twitter.com/1.1/direct_messages/new.json',
            $args);
        return $direct_message;
    }

    /**
     * Follow user
     *
     * @param $userId
     * @return mixed
     */
    public function follow($userId)
    {
        $follow = $this->_api_call_post('https://api.twitter.com/1.1/friendships/create.json',
                                        array('user_id' => $userId, 'follow' => true));
        return $follow;
    }

    /**
     * Used to remove tweet
     *
     * @access public
     * @param $tweet_id
     * @return mixed
     */
    public function remove_tweet( $tweet_id ) {
        $removed = $this->_api_call_post('https://api.twitter.com/1.1/statuses/destroy/'.$tweet_id.'.json');
        return $removed;
    }

    /**
     * Used to upload image into Twitter
     * (attachment to text)
     *
     * @access public
     * @param $image_name
     * @param $status
     * @return mixed
     */
    public function tweet_with_image($status, $image_name) {

        $image_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$image_name;

        $connection = new TwitterOAuth( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        $tweet = $connection->upload('statuses/update_with_media',array(
            'media[]' => '@'.$image_path,
            'status' => $status,
        ));

        return $tweet;
    }

    /**
     * !!!!!!!!!!
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function search_tweets($query, $params = array()) {
        $params['q'] = $query;
        $search_tweets = $this->_api_call('https://api.twitter.com/1.1/search/tweets.json', $params);
        return $search_tweets;
    }

    /**
     * @param $query
     * @param array $args
     * @return array
     */
    public function search_users($query, $args = array()) {
        if (isset($args['min_followers'])) {
            $min_followers = (int)$args['min_followers'];
            unset($args['min_followers']);
        } else {
            $min_followers = 0;
        }
        if (isset($args['max_followers'])) {
            $max_followers = (int)$args['max_followers'];
            unset($args['max_followers']);
        } else {
            $max_followers = 0;
        }
        if (isset($args['age_of_account'])) {
            $age_of_account = (int)$args['age_of_account'];
            unset($args['age_of_account']);
        }
        if (isset($args['tweets_count'])) {
            $tweets_count = (int)$args['tweets_count'];
            unset($args['tweets_count']);
        }
        if(!isset($args['lang'])) {
            $args['lang'] = 'en';
        }
        $return_array = array(
            'users' => array()
        );
        $now = new DateTime();
        $tweets = $this->search_tweets($query, $args);
        foreach($tweets->statuses as $tweet) {
            if ($min_followers > 0) {
                if(!$tweet->user->followers_count > $min_followers) {
                   continue;
                }
            }
            if($max_followers > 0) {
                if(!$tweet->user->followers_count < $max_followers) {
                    continue;
                }
            }
            if(isset($age_of_account)) {
                $created_account_date = new DateTime($tweet->user->created_at);
                $diff = $created_account_date->diff($now);
                if($diff->invert) {
                    continue;
                } else {
                    if(is_array($age_of_account)) {
                        if(count($age_of_account) < 2 ||
                            ($age_of_account[0] > $diff->m || $diff->m > $age_of_account[1])) {

                            continue;
                        }
                    } else {
                        if($age_of_account >= $diff->m) {
                            continue;
                        }
                    }
                }
            }
            if(isset($tweets_count)) {
                if(is_array($tweets_count)) {
                    if(count($tweets_count) < 2 ||
                        ($tweets_count[0] > $tweet->user->statuses_count ||
                            $tweet->user->statuses_count > $tweets_count[1])) {

                        continue;
                    }
                } else {
                    if($tweets_count >= $tweet->user->statuses_count) {
                        continue;
                    }
                }
            }
            $return_array['users'][] = $tweet->user->id;
        }
        $return_array['max_id'] = $this->getMaxIdFromNextResult(
            (isset($tweets->search_metadata->next_results))
                ? $tweets->search_metadata->next_results
                : null
        );
        return $return_array;
    }

    /**
     * @param string $query
     * @param array $include words separated by comma
     * @param array $exclude words separated by comma
     * @param bool $exact
     * @return string
     */
    public function create_query($query, $include, $exclude, $exact) {
        if ($exact) {
            $result_query = '"'.$query.'"';
        } else {
            $result_query = $query;
        }
        foreach($include as $include_element) {
            $result_query .= ' +'.$include_element;
        }
        foreach($exclude as $exclude_element) {
            $result_query .= ' -'.$exclude_element;
        }
        return urlencode($result_query);
    }

    /**
     * Get username from profile link
     *
     * @param $link
     * @return mixed
     */
    public function getUserFromLink($link)
    {
        $parts = explode('/', $link);

        return  $parts[count($parts)-1];
    }

    /**
     * @param null $nextResult
     * @return null
     */
    public function getMaxIdFromNextResult($nextResult = null) {
        if (!$nextResult) {
            return null;
        }
        $result = preg_match('|max_id=(\d+)&*.*$|', $nextResult, $matches);
        if ($result) {
            $max_id = $matches[1];
        } else {
            $max_id = null;
        }
        return $max_id;
    }


    /**
     * Authorize user using Access token from database & app-credentials
     * After - call Twitter API v1.1 (Send GET Request)
     * GET Request used to get some info from twitter
     *
     * @access private
     * @param $url
     * @param array $params
     * @return mixed
     */
    private function _api_call( $url, $params = array() ) {

        $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        $data_json = $connection->get($url, $params);
        $data = json_decode($data_json);
        return $data;
    }

    /**
     * Authorize user using Access token from database & app-credentials
     * After - call Twitter API v1.1 (Send POST Request)
     * POST Request used to create some action (create / destroy post, favorite / retweet and etc)
     *
     * @access private
     * @param $url
     * @param array $params
     * @return mixed
     */
    private function _api_call_post( $url, $params = array() ) {

        $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        $data_json = $connection->post($url, $params);
        $data = json_decode($data_json);
        return $data;
    }

    private function _api_call_get( $url, $params = array() ) {

        $connection = new OAuthDamnit( $this->_config['consumer_key'], $this->_config['consumer_secret'],
            $this->_token['token1'], $this->_token['token2'] );

        $data_json = $connection->get($url, $params);
        $data = json_decode($data_json);
        return $data;
    }

}