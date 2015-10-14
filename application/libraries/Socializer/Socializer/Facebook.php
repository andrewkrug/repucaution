<?php defined('BASEPATH') or die('No direct script access.');

require_once dirname(dirname(__FILE__)).'/vendors/facebook-sdk/src/facebook.php';

class Socializer_Facebook
{
    private $_facebook;
    private $_ci;
    private $_config;
    private $_user_id;
    private $_profile_id;
    private $_fanpage_id;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    /**
     * Construct function
     * Sets the codeigniter instance variable and loads the lang file
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');
        $this->_config = Api_key::build_config('facebook', $this->_ci->config->item('facebook'));
        $this->_user_id = $user_id;
        $this->_facebook = new Facebook($this->_config);

        $this->_token = $token;

        if(isset($this->_token['id'])) {
            $this->_facebook->destroySession();
            $this->_facebook->setAccessToken($this->_token['token1']);
            $page = Facebook_Fanpage::inst()->get_selected_page($this->_user_id, $this->_token['id']);
            if($page->id) {
                $this->_fanpage_id = $page->fanpage_id;
                $this->_profile_id = $page->profile_id;
            }
        }

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
     * Get posts by user id
     *
     * @param $userId
     * @return mixed
     */
    public function getUserPosts($userId)
    {
        return $this->_facebook->api('/'.$userId.'/posts');
    }

    /**
     * Called in methods where fan page id is used
     * @throws Exception
     */
    protected function check_fanpage() {
        if ( ! $this->_fanpage_id) {
            $message = 'Facebook fan page not selected.';
            if( ! $this->_ci->input->is_cli_request()) {
                $message .= '<a class="configure-fblink" href="' 
                    . site_url('settings/socialmedia/edit_account/'.$this->_token['id']) . '">Do it now</a>.';
            }
            throw new Exception($message, Socializer::FBERRCODE);
        }
    }

    /**
     * Used to get facebook access token
     * Use Facebook SDK API library
     * After - return redirect url
     *
     * @access public
     *
     * @param $profile_id
     *
     * @return string
     */
    public function add_new_account($profile_id) {
        $login_url = $this->_facebook->getLoginUrl(array(
            'scope' => 'read_stream, manage_pages, user_videos, user_likes, publish_actions, publish_pages'
        ));
        if($this->_facebook->getUser()) {
            $profile = $this->get_profile();
            $picture = $this->get_profile_picture($profile['id']);
            $access_token = new Access_token();
            $tokens = array(
                'token' => $this->_facebook->getAccessToken(),
                'secret_token' => null,
                'image' => $picture,
                'name' => $profile['name'],
                'username' => $profile['id']
            );
            $token = $access_token->add_token($tokens, 'facebook', $this->_user_id);

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));

            $redirect_url = site_url('settings/socialmedia/edit_account/'.$token->id);
        } else {
            $redirect_url = $login_url;
        }

        return $redirect_url;
    }

    /**
     * Used to get facebook access token
     * Use Facebook SDK API library
     * After - return redirect url
     *
     * @access public
     *
     * @return string
     */
    public function sign_up() {
        $login_url = $this->_facebook->getLoginUrl(array(
            'scope' => 'email read_stream, manage_pages, user_videos, user_likes, publish_actions, publish_pages'
        ));
        if($this->_facebook->getUser()) {
            $profile = $this->get_profile();
            return $profile;
        } else {
            redirect($login_url);
        }
    }

    /**
     * Used to get profile for user
     *
     * @access public
     * @return mixed
     */
    public function get_profile() {
        return $this->_facebook->api('/me');
    }

    /**
     * Used to get array of user facebook fanpages
     * Array like : $item['id'] -- fanpage id in facebook
     * AND $item['name'] -- fanpage name at facebook
     *
     * @access public
     * @return array
     */
    public function get_user_pages() {
        $this->_facebook->setAccessToken($this->_token['token1']);
        $fb_pages = $this->_facebook->api('/me/accounts');
        $pages_data = array();
        $pages_counter = 0;
        foreach ($fb_pages['data'] as $_page) {
            $pages_data[$pages_counter]['name'] = $_page['name'];
            $pages_data[$pages_counter]['id'] = $_page['id'];
            $pages_counter++;
        }
        return $pages_data;
    }

    /**
     * Used to get page feed
     * Using Facebook graph API (FB SDK)
     *
     * @access public
     * @param $url
     * @return mixed
     */
    public function get_page_posts( $url = null ) {
        $this->check_fanpage();
        $this->_ci->load->config('facebook_settings');
        $limit = $this->_ci->config->item('facebook_posts_limit');
        if ($this->_fanpage_id) {
            $request_string = $url == null ? '/'.$this->_fanpage_id.'?fields=feed.limit('.$limit.')' : $url;
            $page_feed = $this->_facebook->api( $request_string );
        } else {
            $page_feed = null;
        }
        return $page_feed;
    }

    /**
     * Get fanapge id
     *
     * return string
     */
    public function getFanpageId()
    {
        return $this->_fanpage_id;
    }

    /**
     * Used to get all data for feed post
     * ( I use this to get ALL comments for post
     * Because get_page_posts return only 2 comments for post )
     *
     * @access public
     * @param $post_id
     * @return mixed
     */
    public function get_post_feed( $post_id ) {
        $post_feed = $this->_facebook->api('/'.$post_id.'/comments');
        return $post_feed;
    }

    /**
     * Get avatar to show it in comments-section
     * ( you can use profile ID or send 'me' to $id param )
     *
     * @param string $id 
     * @return string
     */
    public function get_profile_picture($id = null) {
        
        $result = '';
        if (!$id) {
            try {
                $profile = $this->get_profile();
            } catch (Exception $e) {
            }
            if (!empty($profile['id'])) {
                $id = $profile['id'];
            }
            
        }
        
        if ($id) {
            $result = 'http://graph.facebook.com/'.$id.'/picture?type=square';
        }
        
        return $result;
    }

    /**
     * Used to add a new comment for post
     *
     * @access public
     */
    public function comment( $post_id, $message ) {
        $comment = $this->_facebook->api('/'.$post_id.'/comments', 'POST', array('message' => $message));
        return $this->_facebook->api('/'.$comment['id']);
    }

    /**
     * Used to add new post to facebook fanpage
     *
     * @access public
     * @param $message
     * @param $link
     * @return mixed
     */
    public function post($message, $link) {
        $this->check_fanpage();

        $token_request = $this->_facebook->api(
            '/'.$this->_fanpage_id, 
            'GET', 
            array('fields' => 'access_token')
        );
        $token = $token_request['access_token'];
        $this->_facebook->setAccessToken($token);

        $data = array(
            'message' => $message,
        );
        if( $link != null ) {
            $data['link'] = $link;
        }
        $post = $this->_facebook->api('/'.$this->_fanpage_id.'/feed', 'POST', $data);
        return $post;
    }

    /**
     * !!!!!!!!!!
     * @param       $query
     * @param array $params
     *
     * @return mixed
     */
    public function search_posts($query, $params = array()) {

        $params['q'] = $query;
        $params['type'] = 'posts';

        $search_posts = $this->_facebook->api('/search', 'POST', $params);

        return $search_posts;
    }

    /**
     * Used to add a new post to Facebook page
     * With attachment (Picture)
     *
     * @access public
     * @param $message
     * @param $image_name
     * @param $link
     * @return mixed
     */
    public function post_with_picture($message, $image_name, $link = null) {
        $this->check_fanpage();
        $this->_facebook->setFileUploadSupport(true);
        $image_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$image_name;
        $token_request = $this->_facebook->api('/'.$this->_fanpage_id, 'GET', array('fields' => 'access_token'));
        $token = $token_request['access_token'];
        $this->_facebook->setAccessToken($token);
        $api_method = 'feed';

        $data = array(
            'message' => $message,
        );
        if($image_name != null) {
            $api_method = 'photos';
            $data['source'] = '@'.$image_path;
        }

        if($link != null) {
            $data['link'] = $link;
        }

        $post = $this->_facebook->api('/'.$this->_fanpage_id.'/'.$api_method, 'POST', $data);
        return $post;
    }

    public function post_with_video( $name, $description, $video_name ) {
        $this->check_fanpage();

        $this->_facebook->setFileUploadSupport(true);
        $video_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/public/uploads/'.$this->_user_id.'/'.$video_name;

        $token_request = $this->_facebook->api('/'.$this->_fanpage_id, 'GET', array('fields' => 'access_token'));
        $token = $token_request['access_token'];
        $this->_facebook->setAccessToken($token);

        $data = array(
            'description' => $description,
            'source' => '@'.$video_path
        );

        $uploaded  = $this->_facebook->api('/'.$this->_fanpage_id.'/videos', 'POST', $data);

        return $uploaded;
    }

    /**
     * Used to like something on facebook page
     *
     * @access public
     */
    public function like( $post_id ) {
        $like = $this->_facebook->api('/'.$post_id.'/likes', 'POST');
        return $like;
    }

    /**
     * Used to dislike something on facebook page
     *
     * @access public
     */
    public function dislike( $post_id ) {
        $like = $this->_facebook->api('/'.$post_id.'/likes', 'DELETE');
        return $like;
    }

    /**
     * Used to delete some comment
     *
     * @access public
     * @param $comment_id
     * @return mixed
     */
    public function remove_comment( $comment_id ) {
       
            $removed_comment = $this->_facebook->api('/'.$comment_id, 'DELETE');
            
            return $removed_comment;
            
        
        
    }

    /**
     * Get Page likes count
     * Using for Social Reports
     *
     * @access public
     * @return int
     */
    public function get_page_likes_count() {
        $this->check_fanpage();
        $data = $this->_facebook->api('/'.$this->_fanpage_id);
        return isset($data['likes']) ? $data['likes'] : 0;
    }

    /**
     * Check for 'is liked by me' for comments
     * Need to get more data from Facebook graph API
     *
     * @param $post
     * @return bool
     */
    public function is_liked_comment ( $post ) {
        $likes = $this->_facebook->api('/'.$post['id'].'/likes');
        return $this->is_liked_by_me(array('likes' => $likes));
    }

    /**
     * Used to check -- is user already like this post
     *
     * @access public
     * @param $post
     * @return bool
     */
    public function is_liked_by_me( $post ) {
        $this->check_fanpage();
        if(!isset($post['likes'])) {
            return false;
        }
        if(!isset($post['likes']['data'])) {
            return false;
        }
        $likes = $post['likes']['data'];
        foreach($likes as $_like) {
            if($_like['id'] == $this->_profile_id || $_like['id'] == $this->_fanpage_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Facebook comment time beautifier
     * Return comment create-time in format like : '12 hours ago'
     * or '12 of march on Facebook' or '10 minutes ago'
     *
     * @access public
     * @param $comment_time
     * @return string
     */
    public function convert_facebook_time( $comment_time ) {
        $diff = time() - strtotime($comment_time);
        $date = strtotime($comment_time);
        if($diff > 3600){
            if($diff < 86400){
                $diff = $diff/3600;
                return round($diff,0).' hours ago on Facebook';
            } else {
                return date('d F',$date).' on Facebook';
            }
        } else{
            $diff = $diff/60;
            return round($diff,0).' minutes ago on Facebook';
        }
    }
    
    /**
     * Transform message of Facebook API errors
     *
     * @param string $message
     * @return string   
     */
    public function facebookErrorTransformer($message)
    {
        $patternCode = '/^\(\#[0-9]*\)/';
        preg_match($patternCode, $message, $match);
        if (!empty($match[0])) {
            if ($match[0] == '(#210)') {
                $message = 'Wall of the user is closed';    
            } else {
                $message = str_replace($match[0], '', $message);
            }
        }
        
        return $message;
    }

    /**
     * Get friends count of user
     *
     * @param $userId
     *
     * @return int
     * @throws
     */
    public function getUserFriendsCount($userId)
    {
        if (empty($userId)) {
            throw InvalidArgumentException('FB user id can not be empty!');
        }

        $params = array(
            'method' => 'fql.query',
            'query' => "SELECT friend_count FROM user WHERE uid = ".$userId,
        );
        $data = $this->_facebook->api($params);

        return isset($data[0]['friend_count']) ? (int)$data[0]['friend_count'] : 0;
    }

}