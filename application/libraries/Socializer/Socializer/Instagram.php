<?php

/**
 * CodeIgniter Instagram Library by Ian Luckraft    http://ianluckraft.co.uk    ian@ianluckraft.co.uk
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Instagram
 * @package    CodeIgniter
 * @subpackage Client
 * @version    1.0
 * @license    http://www.gnu.org/licenses/     GNU General Public License
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Socializer_Instagram {

    /**
     * Variable to hold an insatance of CodeIgniter so we can access CodeIgniter features
     */
    private $_ci;
    private $_config;
    private $_user_id;
    private $instanceId;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    /**
     * Create an array of the urls to be used in api calls
     * The urls contain conversion specifications that will be replaced by sprintf in the functions
     * @var string
     */
    protected $api_urls = array(
        'user'                        => 'https://api.instagram.com/v1/users/%s/?access_token=%s',
        'user_feed'                    => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&count=%s&max_id=%s',
        'user_recent'                => 'https://api.instagram.com/v1/users/%s/media/recent/?access_token=%s&count=%s&max_id=%s&min_id=%s&max_timestamp=%s&min_timestamp=%s',
        'user_search'                => 'https://api.instagram.com/v1/users/search?q=%s&access_token=%s',
        'user_follows'                => 'https://api.instagram.com/v1/users/%s/follows?access_token=%s',
        'user_followed_by'            => 'https://api.instagram.com/v1/users/%s/followed-by?access_token=%s',
        'user_requested_by'            => 'https://api.instagram.com/v1/users/self/requested-by?access_token=%s',
        'user_relationship'            => 'https://api.instagram.com/v1/users/%s/relationship?access_token=%s',
        'modify_user_relationship'    => 'https://api.instagram.com/v1/users/%s/relationship?access_token=%s',
        'media'                        => 'https://api.instagram.com/v1/media/%s?access_token=%s',
        'media_search'                => 'https://api.instagram.com/v1/media/search?lat=%s&lng=%s&max_timestamp=%s&min_timestamp=%s&distance=%s&access_token=%s',
        'media_popular'                => 'https://api.instagram.com/v1/media/popular?access_token=%s',
        'media_comments'            => 'https://api.instagram.com/v1/media/%s/comments?access_token=%s',
        'post_media_comment'        => 'https://api.instagram.com/v1/media/%s/comments?access_token=%s',
        'delete_media_comment'        => 'https://api.instagram.com/v1/media/%s/comments?comment_id=%s&access_token=%s',
        'likes'                        => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'post_like'                    => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'remove_like'                => 'https://api.instagram.com/v1/media/%s/likes?access_token=%s',
        'tags'                        => 'https://api.instagram.com/v1/tags/%s?access_token=%s',
        'tags_recent'                => 'https://api.instagram.com/v1/tags/%s/media/recent?count=%s&max_id=%s&min_id=%s&access_token=%s',
        'tags_search'                => 'https://api.instagram.com/v1/tags/search?q=%s&access_token=%s',
        'locations'                    => 'https://api.instagram.com/v1/locations/%d?access_token=%s',
        'locations_recent'            => 'https://api.instagram.com/v1/locations/%d/media/recent/?max_id=%s&min_id=%s&max_timestamp=%s&min_timestamp=%s&access_token=%s',
        'locations_search'            => 'https://api.instagram.com/v1/locations/search?lat=%s&lng=%s&foursquare_id=%s&distance=%s&access_token=%s'
    );

    /**
     * Construct function
     * Sets the codeigniter instance variable and loads the lang file
     * @param integer|null $user_id
     * @param array|null $token
     */
    function __construct($user_id, $token) {
    
        // Set the CodeIgniter instance variable
        $this->_ci =& get_instance();
        
        // Load the Instagram API language file
        $this->_ci->config->load('social_credentials');
        $this->_config = Api_key::build_config('instagram', $this->_ci->config->item('instagram'));
        //var_dump($this->_config);die;
        $this->_user_id = $user_id;
        $this->_token = $token;
        $this->access_token = $this->_token['token1'];
        $this->instanceId = $this->_token['instance_id'];
    } 
    
    /**
     * Create a variable to hold the Oauth access token
     * @var string
     */
    public $access_token = FALSE;
    
    /**
     * Function to create the login with Instagram link
     * @return string Instagram login url
     */
    function instagramLogin() {
    
        return 'https://instagram.com/oauth/authorize/?client_id=' . $this->_config['in_client_id'] .
                                                           '&redirect_uri=' . $this->_config['redirect_uri'] .
                                                           '&response_type=code&scope=likes+relationships+comments';
    
    }

    /**
     * The api call function is used by all other functions
     * It accepts a parameter of the url to use
     * And an optional string of post parameters
     * @param string api url
     * @param bool $post_parameters
     * @param bool $delete
     * @param bool $headers
     * @return std_class data returned form curl call
     */
    function __apiCall($url, $post_parameters = FALSE, $delete = FALSE, $headers = FALSE) {
        // Initialize the cURL session
        $curl_session = curl_init();
            
        // Set the URL of api call
        curl_setopt($curl_session, CURLOPT_URL, $url);
            
        // If there are post fields add them to the call
        if($post_parameters !== FALSE) {
            curl_setopt ($curl_session, CURLOPT_POSTFIELDS, $post_parameters);
        }

        // if delete method
        if ($delete) {
            curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        if ($headers) {
            $curlHeaders = array();
            foreach ($headers as $header => $value) {
                $curlHeaders[] = $header.': '.$value;
            }

            curl_setopt($curl_session,CURLOPT_HTTPHEADER, $curlHeaders);
        }
            
        // Return the curl results to a variable
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);
        
        // There was issues with some servers not being able to retrieve the data through https
        // The config variable is set to TRUE by default. If you have this problem set the config variable to FALSE
        // See https://github.com/ianckc/CodeIgniter-Instagram-Library/issues/5 for a discussion on this
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, $this->_ci->config->item('instagram_ssl_verify'));

        // Execute the cURL session
        $contents = curl_exec ($curl_session);

        // Close cURL session
        curl_close ($curl_session);

        // Return the response
        return json_decode($contents);
   
    }
    
    /**
     * The authorize function to get the OAuth token
     * Accepts a code that is returned from Instagram to our redirect url
     * @param string code generated by Instagram when the user has been sent to our redirect url
     * @return std_class Instagram OAuth data
     */
    function authorize($code)
    {
    //var_dump($this->_config);die;
        $authorization_url = 'https://api.instagram.com/oauth/access_token';
        
        return $this->__apiCall($authorization_url, "client_id=" . $this->_config['in_client_id'] . "&client_secret=" . $this->_config['in_client_secret'] . "&grant_type=authorization_code&redirect_uri=" . $this->_config['redirect_uri']. "&code=" . $code);
        //return $this->__apiCall($authorization_url, "client_id=" . $this->_config['in_client_id'] . "&client_secret=" . $this->_config['in_client_secret'] . "&grant_type=authorization_code&redirect_uri=" . $this->_config['redirect_uri']);        
        
    }

    /**
     * Used to get Instagram access token
     * After - return redirect url
     *
     * @access public
     *
     * @param $profile_id
     *
     * @return string
     * @throws OAuthException
     */
    public function add_new_account($profile_id) {
        $auth_instagram = $this->authorize($_GET['code']);

        if(isset($auth_instagram->access_token)) {
            $access_token = new Access_token();
            $tokens = array(
                'token' => $auth_instagram->access_token,
                'secret_token' => null,
                'instance_id' => $auth_instagram->user->id,
                'image' => $auth_instagram->user->profile_picture,
                'name' => $auth_instagram->user->full_name,
                'username' => $auth_instagram->user->username
            );
            $token = $access_token->add_token($tokens, 'instagram', $this->_user_id);

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));

            $redirect_url = site_url('settings/socialmedia');
        } else {
            throw new OAuthException(lang('not_connected_error', ['Instagram']));
        }

        return $redirect_url;
    }

    /**
     * Get user nickname from link
     *
     * @param string $link
     * @return mixed
     */
    public function getUserFromLink($link)
    {
        $parts = explode('/', $link);
        $username = $parts[count($parts)-1];
        $userData = $this->userSearch($username);
        $users = $userData->data;

        return  (count($users) && $users[0]->username == $username) ? $users[0]->id : null;
    }

    /**
     * Get user id
     *
     * @return mixed
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }


    /**
     * Get recent activities
     *
     * @param $userId
     * @param $params
     * @return mixed
     */
    public function activities($userId, $params)
    {
        $recentData = $this->getUserRecent($userId, $params['continue_from'], null, $params['until'], $params['since'], $params['limit']);

        return  $recentData->data;
    }

    /**
     * Get a list of what media is most popular at the moment
     * This function only requires your instagram client id and no Oauth token
     * @return std_class current popular media with associated data
     */
    function getPopularMedia()
    {
        
        $popular_media_request_url = 'https://api.instagram.com/v1/media/popular?client_id=' . $this->_ci->config->item('instagram_client_id');
        
        return $this->__apiCall($popular_media_request_url);
        
    }
    
    /**
     * Get an individual user's details
     * Accepts a user id
     * @param int $user_id Instagram user id
     * @return std_class data about the Instagram user
     */
    function getUser($user_id) {
        
        $user_request_url = sprintf($this->api_urls['user'], $user_id, $this->access_token);
        
        return $this->__apiCall($user_request_url);
        
    }

    /**
     * Get an individual user's feed
     * Accepts optional max and min values
     * @param null $count
     * @param null $max
     * @param null $min
     * @return std_class of user's feed
     * @internal param return $int media after max id
     * @internal param return $int media before min id
     */
    function getUserFeed($count = null, $max = null, $min = null) {
        $count = $this->_config['feed_limit'];
        $user_feed_request_url = sprintf($this->api_urls['user_feed'], $this->getAccessToken(), $count, $max);
        //var_dump($user_feed_request_url);die;
        return $this->__apiCall($user_feed_request_url);
        
    }
    
    /**
     * Function to get a users recent published media
     * Accepts a user id and access token and optional max id, min id, max timestamp and min timestamp
     * @param int Instagram user id
     * @param int return media after max id
     * @param int return media before min id
     * @param int return media after this UNIX timestamp
     * @param int return media before this UNIX timestamp
     * @param int return this number of media
     * @return std_class of media found based on parameters given
     */
    function getUserRecent($user_id, $max_id = null, $min_id = null, $max_timestamp = null, $min_timestamp = null, $count = null) {
        
        $user_recent_request_url = sprintf($this->api_urls['user_recent'], $user_id, $this->getAccessToken(), $count, $max_id, $min_id, $max_timestamp, $min_timestamp);
        //var_dump($user_recent_request_url);die;
        return $this->__apiCall($user_recent_request_url);
        
    }
    
    /**
     * Function to search for user
     * Accepts a user name to search for
     * @param string an Instagram user name
     * @return std_class user data
     */
    function userSearch($user_name) {
        
        $user_search_request_url = sprintf($this->api_urls['user_search'], $user_name, $this->access_token);
        
        return $this->__apiCall($user_search_request_url);
        
    }
    
    /**
     * Function to get all users the current user follows
     * Accepts a user id
     * @param int user id
     * @return std_class user's recent feed items
     */
    function userFollows($user_id) {
        
        $user_follows_request_url = sprintf($this->api_urls['user_follows'], $user_id, $this->access_token);
        
        return $this->__apiCall($user_follows_request_url);
        
    }
    
    /**
     * Function to get all users the current user follows
     * Accepts a user id
     * @param int user id
     * @return std_class other users that follow the one passed in
     */
    function userFollowedBy($user_id) {
        
        $user_followed_by_request_url = sprintf($this->api_urls['user_followed_by'], $user_id, $this->access_token);
        
        return $this->__apiCall($user_followed_by_request_url);
        
    }
    
    /**
     * Function to find who a user was requested by
     * Accepts an access token
     * @return std_class users who have requested this user's permission to follow
     */
    function userRequestedBy() {
        
        $user_requested_by_request_url = sprintf($this->api_urls['user_requested_by'], $this->access_token);
        
        return $this->__apiCall($user_requested_by_request_url);
        
    }
    
    /**
     * Function to get information about the current user's relationship (follow/following/etc) to another user
     * @param int user id
     * @return std_class user's relationship to another user
     */
    function userRelationship($user_id) {
        
        $user_relationship_request_url = sprintf($this->api_urls['user_relationship'], $user_id, $this->access_token);
        
        return $this->__apiCall($user_relationship_request_url);
        
    }
    
    /**
     * Function to modify the relationship between the current user and the target user
     * @param int Instagram user id
     * @param string action to effect relatonship (follow/unfollow/block/unblock/approve/deny)
     * @return std_class result of request
     */
    function modifyUserRelationship($user_id, $action) {
        
        $user_modify_relationship_request_url = sprintf($this->api_urls['modify_user_relationship'], $user_id, $this->access_token);
        
        return $this->__apiCall($user_modify_relationship_request_url, array("action" => $action));
        
    }
    
    /**
     * Function to get data about a media id
     * Accepts a media id
     * @param int media id
     * @return std_class data about the media item
     */
    function getMedia($media_id) {
        
        $media_request_url = sprintf($this->api_urls['media'], $media_id, $this->access_token);
        
        return $this->__apiCall($media_request_url);
        
    }
    
    /**
     * Function to search for media
     * Accepts optional parameters
     * @param int latitude
     * @param int longitude
     * @param int max timestamp
     * @param int min timestamp
     * @param int distance
     * @return std_class media items found in search
     */
    function mediaSearch($latitude = null, $longitude = null, $max_timestamp = null, $min_timestamp = null, $distance = null) {
        
        $media_search_request_url = sprintf($this->api_urls['media_search'], $latitude, $longitude, $max_timestamp, $min_timestamp, $distance, $this->access_token);
        
        return $this->__apiCall($media_search_request_url);
        
    }
    
    /**
     * Function to get a list of what media is most popular at the moment
     * @return std_class popular media
     */
    function popularMedia() {
        
        $popular_media_request_url = sprintf($this->api_urls['media_popular'], $this->access_token);
        
        return $this->__apiCall($popular_media_request_url);
        
    }
    
    /**
     * Function to gget a full list of comments on a media
     * @param int media id
     * @return std_class media comments
     */
    function mediaComments($media_id) {
    
        $media_comments_request_url = sprintf($this->api_urls['media_comments'], $media_id, $this->access_token);
        
        return $this->__apiCall($media_comments_request_url);
    
    }
    
    /**
     * Function to post a media comment
     * @param int media id
     * @param string comment on the media
     * @return std_class response to request
     */
    function postMediaComment($media_id, $comment) {

        $post_media_comment_url = sprintf($this->api_urls['post_media_comment'], $media_id, $this->access_token);
        $header = null;//$this->getXheader();
        
        return $this->__apiCall($post_media_comment_url, array('text' => $comment), false, $header);
    
    }
    
    /**
     * Function to delete a media comment
     * @param int media id
     * @param int comment id
     * @return std_class response to request
     */
    function deleteMediaComment($media_id, $comment_id) {
    
        $delete_media_comment_url = sprintf($this->api_urls['delete_media_comment'],
                                            $media_id,
                                            $comment_id,
                                            $this->access_token);
        
        return $this->__apiCall($delete_media_comment_url, false, true);
    
    }
    
    /**
     * Function to get a list of users who have liked this media
     * @param int media id
     * @return std_class list of users
     */
    function mediaLikes($media_id) {
    
        $media_likes_request_url = sprintf($this->api_urls['likes'], $media_id, $this->access_token);
        
        return $this->__apiCall($media_likes_request_url);
    
    }
    
    /**
     * Function to post a like for a media item
     * @param int media id
     * @return std_class response to request
     */
    function postLike($media_id) {
    
        $post_media_like_request_url = sprintf($this->api_urls['post_like'], $media_id, $this->access_token);
        $header = null;//$this->getXheader();
        
        return $this->__apiCall($post_media_like_request_url, TRUE, FALSE, $header);
    
    }

    /**
     * Function to remove a like for a media item
     * @param int media id
     * @return std_class response to request
     */
    function removeLike($media_id) {
    
        $remove_like_request_url = sprintf($this->api_urls['remove_like'], $media_id, $this->access_token);
        $header = null;//$this->getXheader();
        
        return $this->__apiCall($remove_like_request_url, false, true, $header);
        
    }
    
    /**
     * Function to get information about a tag object
     * @param string tag
     * @return std_class of data about the tag
     */
    function getTags($tag) {
    
        $tags_request_url = sprintf($this->api_urls['tags'], $tag, $this->access_token);
        
        return $this->__apiCall($tags_request_url);
    
    }

    /**
     * Function to get a list of recently tagged media
     *
     * @param string        $tag
     * @param integer       $count
     * @param string|int    $max_id
     * @param string|int    $min_id
     *
     * @return std_class recently tagged media
     */
    function tagsRecent($tag, $count, $max_id = '', $min_id = '') {
    
        $tags_recent_request_url = sprintf($this->api_urls['tags_recent'], $tag, $count, $max_id, $min_id, $this->access_token);

        return $this->__apiCall($tags_recent_request_url);
    
    }

    /**
     * Function to search for tagged media
     * @param string valid tag name without a leading #. (eg. snow, nofilter)
     * @return std_class tags by name - results are ordered first as an exact match, then by popularity
     */
    function tagsSearch($tag) {
    
        $tags_search_request_url = sprintf($this->api_urls['tags_search'], $tag, $this->access_token);
        
        return $this->__apiCall($tags_search_request_url);
    
    }
    
    /**
     * Function to get information about a location. 
     * @param int location id
     * @return std_class data about the location
     */
    function getLocation($location) {
    
        $location_request_url = sprintf($this->api_urls['locations'], $location, $this->access_token);
        
        return $this->__apiCall($location_request_url);
    
    }
    
    /**
     * Function to get a list of recent media objects from a given location.
     * @param int location id
     * @param int return media after this max_id
     * @param int return media before this min_id
     * @param int return media after this UNIX timestamp
     * @param int return media before this UNIX timestamp
     * @return std_class recent media objects from a location
     */
    function locationRecent($location, $max_id = null, $min_id = null, $max_timestamp = null, $min_timestamp = null) {
    
        $location_recent_request_url = sprintf($this->api_urls['locations_recent'], $location, $max_id, $min_id, $max_timestamp, $min_timestamp, $this->access_token);
        
        return $this->__apiCall($location_recent_request_url);
    
    }
    
    /**
     * Function to search for locations used in Instagram
     * @param int latitude of the center search coordinate. If used, longitude is required
     * @param int longitude of the center search coordinate. If used, latitude is required
     * @param int Foursquare id. Returns a location mapped off of a foursquare v1 api location id. If used, you are not required to use lat and lng. Note that this method will be deprecated over time and transitioned to new foursquare IDs with V2 of their API.
     * @param int distance. Default is 1000m (distance=1000), max distance is 5000
     * @return std_class location data
     */
    function locationSearch($latitude = null, $longitude = null, $foursquare_id = null, $distance = null) {
    
        $location_search_request_url = sprintf($this->api_urls['locations_search'], $latitude, $longitude, $foursquare_id, $distance, $this->access_token);
        
        return $this->__apiCall($location_search_request_url);
    
    }
    
    private function getAccessToken(){
        
        return $this->_token['token1'];
    }
    
    public function getUserId(){
        
        return $this->_token['instance_id'];
    }

    /**
     * X-header for signing request
     *
     * @return array
     */
    protected function getXheader()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $signature = (hash_hmac('sha256', $ip, $this->_config['in_client_secret'], false));

        return array('X-Insta-Forwarded-For' => join('|', array($ip, $signature)));
    }

}
