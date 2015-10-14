<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends MY_Controller {

    protected $website_part = 'dashboard';

    protected $activity_tabs = array(
        'facebook' => array(
            'title' => 'Facebook',
            'icon_class' => 'fb',
        ),
        'twitter' => array(
            'title' => 'Twitter',
            'icon_class' => 'tw',
        ),
//        'instagram' => array(
//            'title' => 'Instagram',
//            'icon_class' => 'fa fa-instagram-square',
//        ),
        'google' => array(
            'title' => 'Google',
            'icon_class' => 'fa fa-google-plus-square',
        ),
//        'linkedin' => array(
//            'title' => 'Linkedin',
//            'icon_class' => 'fa fa-linkedin-square',
//        ),
    );

    /**
     * @var /Core/Service/Radar/Radar
     */
    protected $radar;

    public function __construct() {
        parent::__construct();
        $this->lang->load('social_activity', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_activity', $this->language)
        ]);
        $this->template->set('activity_tabs', $this->activity_tabs);
        $this->radar = $this->get('core.radar');

    }

    public function index() {
        redirect('social/activity/facebook');
    }

    /**
     * Load facebook activity page
     * Load last posts and attach JS / CSS
     *
     * @access public
     * @return void
     */
    public function facebook() {
        $accounts = array();
        if(!$this->_check_access('facebook')) {
            $accounts[] = array(
                'socializer_error' => 'Facebook not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.'
            );
            $this->template->set('accounts', $accounts);
            $this->template->render();
            return;
        }

        $this->load->library('Socializer/socializer');
        $this->load->helper('clicable_links');
        $this->template->set('radar', $this->radar);

        $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray('facebook', $this->c_user->id, $this->profile->id);
        if(isset($_GET['token_id'])) {
            $token = new Access_token($_GET['token_id']);
            $token = $token->to_array();
        } else {
            $token = $access_tokens[0];
        }
        $this->template->set('token', $token);
        $this->template->set('access_tokens', $access_tokens);

        CssJs::getInst()->c_js('social/activity', 'facebook');

        $this->template->set('c_user_id', $this->c_user->id);
        $this->template->render();
    }

    /**
     * Used to add new comment for facebook post
     * Get comment text from $_POST and send to Facebook Socializer Library
     * After - return created comment html
     *
     * @access public
     * @param null $post_id
     */
    public function facebook_comment( $post_id = null, $type = null) {

        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            if(isset($post['message']) && $post_id != null) {
                try {
                    $this->load->library('Socializer/socializer');
                    $facebook = Socializer::factory('Facebook', $this->c_user->id);
                    $comment = $facebook->comment( $post_id, $post['message'] );

                    if ($comment) {
                        if ($type == 'crm') {
                            Crm_directory_activity::inst()->update_other_field($post_id, 'comments', 'inc');
                        } else {
                            Mention::inst()->update_other_field($post_id, 'comments', 'inc');
                        }

                    }
                    $result['success'] = true;
                    $result['html'] = $this->template->block(
                        '_comment',
                        'social/activity/blocks/_one_facebook_comment',
                        array('_comment' =>  $comment, 'socializer' => $facebook, 'radar' => $this->radar)
                    );
                } catch(Exception $e) {
                    $result = array();
                    $result['success'] = false;
                    $result['error'] = $e->getMessage();
                }
                if (!empty($result['error'])) {
                    $result['error'] = $facebook->facebookErrorTransformer($result['error']);
                }
                
                echo json_encode($result);
            }
        }
    }

    /**
     * Like selected facebook post
     *
     * @access public
     * @param null $post_id
     * @return void
     */
    public function facebook_like(  $post_id = null , $type = null) {

        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('facebook')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
            } else {
                $this->load->library('Socializer/socializer');
                $facebook = Socializer::factory('Facebook', $this->c_user->id);
                if( $post_id != null) {
                    try {
                        $like = $facebook->like( $post_id );

                        if ($like) {
                            if ($type == 'crm') {
                                Crm_directory_activity::inst()->update_other_field($post_id, 'i_like', 'inc');
                            } else {
                                Mention::inst()->update_other_field($post_id, 'i_like', 'inc');
                            }
                        }

                        $result = array();
                        $result['success'] = $like;
                    } catch (Exception $e) {
                        $result = array();
                        $result['success'] = false;
                        $result['error'] = $e->getMessage();
                    }
                   
                }
            }
            if (!empty($result['error'])) {
                $result['error'] = $facebook->facebookErrorTransformer($result['error']);
            }
            echo json_encode($result);
        }
    }

    /**
     * Unlike selected facebook post
     *
     * @access public
     * @param null $post_id
     * @return void
     */
    public function facebook_dislike( $post_id = null, $type = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('facebook')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $this->load->library('Socializer/socializer');
                $facebook = Socializer::factory('Facebook', $this->c_user->id);
                if( $post_id != null) {
                    try {
                        $like = $facebook->like( $post_id );
                        if ($like) {
                            if ($type == 'crm') {
                                Crm_directory_activity::inst()->update_other_field($post_id, 'i_like', 'dec');
                            } else {
                                Mention::inst()->update_other_field($post_id, 'i_like', 'dec');
                            }
                        }

                        $result = array();
                        $result['success'] = $like;
                    } catch (Exception $e) {
                        $result = array();
                        $result['success'] = false;
                        $result['error'] = $e->getMessage();
                    }
                }
            }
            if (!empty($result['error'])) {
                $result['error'] = $facebook->facebookErrorTransformer($result['error']);
            }
            echo json_encode($result);
        }
    }

    /**
     * Get all comments for selected facebook post
     *
     * @access public
     * @param null $post_id
     * @return void
     */
    public function facebook_get_comments( $post_id ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('facebook')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $html = '';
                if( $post_id != null) {
                    $this->load->library('Socializer/socializer');
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $this->c_user->id);
                    $comments = $facebook->get_post_feed($post_id);
                    $radar = $this->get('core.radar');
                    if( isset($comments['data']) ) {
                        foreach ( $comments['data'] as $_comment ) {
                            $html .= $this->template->block(
                                '_comment',
                                'social/activity/blocks/_one_facebook_comment',
                                array('_comment' => $_comment, 'socializer' => $facebook, 'radar' => $radar)
                            );
                        }
                    }
                    
                }
                $result['success'] = true;
                $result['html'] = $html;
            }
            
            echo json_encode($result);;    
        }
    }

    /**
     * Remove facebook feed comment
     *
     * @access public
     * @param $comment_id
     */
    public function facebook_remove_comment( $comment_id = null, $post_id = null ) {
        if( $this->template->is_ajax() ) {
            echo $this->removeComment('facebook', $post_id, $comment_id);
        }
    }

    /**
     * Load more facebook page posts
     * (Pagination)
     *
     * @access public
     * @return void
     */
    public function load_facebook_feed() {
        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            if(isset($post['graph_url']) && isset($post['token_id'])) {
                try {
                    if (!$post['graph_url']) {
                        $url = null;
                    } else {
                        $url = str_replace('https://graph.facebook.com', '', $post['graph_url']);
                    }
                    $this->load->helper('clicable_links');
                    $this->load->library('Socializer/socializer');
                    $access_token = Access_token::create()->where('id', $post['token_id'])->get()->to_array();
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $this->c_user->id, $access_token);
                    $page_feed = $facebook->get_page_posts( $url );
                    $fanpicture = $facebook->get_profile_picture($facebook->getFanpageId());
                    $html = '';
                    $paging = array('next' => '', 'previous' => '');
                    if(count($page_feed['feed']['data']) > 0) {
                        $picture = $facebook->get_profile_picture();
                        $html = $this->template->block('_facebook_feed', 'social/activity/blocks/_facebook_feed',
                            array(
                                'feed' => $page_feed['feed']['data'],
                                'picture' => $picture,
                                'socializer' => $facebook,
                                'fanpicture' => $fanpicture,
                                'radar' => $this->radar
                            ));
                    }
                    if(count($page_feed['feed']['data']) > 0) {
                        $paging['next'] = $page_feed['feed']['paging']['next'];
                        $paging['previous'] = $page_feed['feed']['paging']['previous'];
                    }
                    $response = array('html' => $html, 'paging' => $paging);
                    echo json_encode($response);
                } catch(Exception $e) {
                    echo json_encode(array(
                        'error' => $e->getMessage()
                    ));
                }
            } else {
                echo json_encode(array(
                    'error' => 'Can\'t load feed.'
                ));
            }
        }
    }

    /**
     * Load Twitter activity tab
     *
     * @access public
     * @return void
     */
    public function twitter()  {

        if( ! $this->_check_access('twitter')) {
            $this->template->set('socializer_error', 'Twitter not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.');
            $this->template->render();
        } else {
            $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray('twitter', $this->c_user->id, $this->profile->id);
            if(isset($_GET['token_id'])) {
                $token = new Access_token($_GET['token_id']);
                $token = $token->to_array();
            } else {
                $token = $access_tokens[0];
            }
            $this->template->set('token', $token);
            $this->template->set('access_tokens', $access_tokens);

            CssJs::getInst()->c_js('social/activity', 'twitter');

            $this->template->render();
        }

    }

    /**
     * Used to send new page tweets html to ajax function(load new page)
     *
     * @access public
     * @param $page_number
     */
    public function load_tweets( $page_number = 1 ) {
        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            $type = isset($post['type']) ? $post['type'] : 'feed';
            $token_id = isset($post['token_id']) ? $post['token_id'] : null;
            $html = $this->_get_tweets_html( $type, $page_number, $token_id );
            echo $html;
        }
    }

    /**
     * Used to call socializer Twitter library and send a new tweet in Twitter
     *
     * @access public
     * @param null $in_reply_to_id
     */
    public function tweet( $in_reply_to_id = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('twitter')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $post = $this->input->post();
                if( isset($post['tweet_text']) ) {
                    $this->load->library('Socializer/socializer');
                    $twitter = Socializer::factory('Twitter', $this->c_user->id);
                    $tweet = $twitter->tweet( $post['tweet_text'], $in_reply_to_id );

                    if (!$tweet->errors) {
                        $result['success'] = true;
                    } else {
                        $result['success'] = false;
                        $result['error'] = $tweet->errors[0]->message;
                    }
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * Used to call Socializer Twitter library and add some tweet to favorite through Twitter API
     *
     * @access public
     * @param $tweet_id
     */
    public function favorite( $tweet_id, $type = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('twitter')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
            } else {
                $this->load->library('Socializer/socializer');
                $twitter = Socializer::factory('Twitter', $this->c_user->id);
                $favorite = $twitter->favorite( $tweet_id );

                if (!$favorite->errors) {
                    if ($type == 'crm') {
                        Crm_directory_activity::inst()->update_other_field($tweet_id, 'favorited', 'true');
                    } else {
                        Mention::inst()->update_other_field($tweet_id, 'favorited', 'true');
                    }
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                    $result['error'] = $favorite->errors[0]->message;
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * Used to call Socializer Twitter library and remove some tweet from favorite through Twitter API
     *
     * @access public
     * @param $tweet_id
     */
    public function unfavorite( $tweet_id, $type = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('twitter')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $this->load->library('Socializer/socializer');
                $twitter = Socializer::factory('Twitter', $this->c_user->id);
                $favorite = $twitter->undo_favorite( $tweet_id );
                if (!$favorite->errors) {
                    if ($type == 'crm') {
                        Crm_directory_activity::inst()->update_other_field($tweet_id, 'favorited', 'false');
                    } else {
                        Mention::inst()->update_other_field($tweet_id, 'favorited', 'false');
                    }
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                    $result['error'] = $favorite->errors[0]->message;
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * Used to call Socializer Twitter library and retweet some tweet through Twitter API
     *
     * @access public
     * @param $tweet_id
     */
    public function retweet( $tweet_id, $type = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('twitter')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $this->load->library('Socializer/socializer');
                $twitter = Socializer::factory('Twitter', $this->c_user->id);
                $retweet = $twitter->retweet( $tweet_id );
                if (!$retweet->errors) {
                    if ($type == 'crm') {
                        Crm_directory_activity::inst()->update_other_field($tweet_id, 'retweeted', 'true');
                    } else {
                        Mention::inst()->update_other_field($tweet_id, 'retweeted', 'true');
                    }
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                    $result['error'] = $retweet->errors[0]->message;
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * Used to call Socializer Twitter library and undo-retweet some tweet through Twitter API
     *
     * @access public
     * @param $tweet_id
     */
    public function unretweet( $tweet_id, $type = null ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('twitter')) {
                $result['success'] = false;
                $result['error'] = 'You don\'t have access to this social';
                
            } else {
                $this->load->library('Socializer/socializer');
                $twitter = Socializer::factory('Twitter', $this->c_user->id);
                $retweet = $twitter->undo_retweet( $tweet_id );
                if (!$retweet->errors) {
                    if ($type == 'crm') {
                        Crm_directory_activity::inst()->update_other_field($tweet_id, 'retweeted', 'false');
                    } else {
                        Mention::inst()->update_other_field($tweet_id, 'retweeted', 'false');
                    }
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                    $result['error'] = $retweet->errors[0]->message;
                }
            }
            echo json_encode($result);
        }
    }

    /**
     * Used to call Socializer Twitter library and remove checked tweet through Twitter API
     *
     * @access public
     * @param $tweet_id
     */
    public function remove_tweet( $tweet_id ) {
        if( $this->template->is_ajax() ) {
            $this->load->library('Socializer/socializer');
            $twitter = Socializer::factory('Twitter', $this->c_user->id);
            $twitter->remove_tweet( $tweet_id );
        }
    }

    /**
     * Used to call Socializer Twitter library and get user feed through Twitter API
     *
     * @access public
     * @param $type
     * @param $page_number
     * @return string
     */
    private function _get_tweets_html( $type,  $page_number, $access_token_id ) {
        if (!$access_token_id) {
            $errorMessage = 'Can\'t load tweets.';
            return json_encode(array(
                'error' => $errorMessage
            ));
        }
        $this->load->library('Socializer/socializer');
        $user_tweets = array();
        $errorMessage = '';
        $access_token = Access_token::create()->where('id', $access_token_id)->get()->to_array();
        /* @var Socializer_Twitter $twitter */
        $twitter = Socializer::factory('Twitter', $this->c_user->id, $access_token);
        $this->load->config('twitter_settings');

        $twitter_posts_limit = $this->config->item('twitter_posts_limit');
        switch ($type) {
            case 'feed':
                $user_tweets = $twitter->get_user_feed($twitter_posts_limit, $page_number);
                break;
            case 'mentions':
                $user_tweets = $twitter->get_user_mentions($twitter_posts_limit, $page_number);
                break;
            case 'my_tweets':
                $user_tweets = $twitter->get_user_tweets($twitter_posts_limit, $page_number);
                break;
        }
        if (isset($user_tweets->errors)) {
            $errorMessage = 'Can\'t load tweets. Try to reconnect your '.$access_token['username'].' account.';
        }

        if(is_array($user_tweets) && empty($errorMessage)) {
            $block_data['tweets'] = $user_tweets;
            if($type == 'my_tweets') {
                $block_data['is_user_tweets'] = true;
            }
            $block_data['radar'] = $this->radar;
            $tweets_html = $this->load->view('social/activity/blocks/_tweets_feed', $block_data, true);
        } else {
            $tweets_html = json_encode(array(
                'error' => $errorMessage
            ));
        }

        return $tweets_html;
    }
    
    
    /**
     * Load facebook activity page
     * Load last posts and attach JS / CSS
     *
     * @access public
     * @return void
     */
    public function instagram() {
        $accounts = array();
        if(!$this->_check_access('instagram')) {
            $accounts[] = array(
                'socializer_error' => 'Instagram not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.'
            );
            $this->template->set('accounts', $accounts);
            $this->template->render();
            return;
        }

        $this->load->library('Socializer/socializer');
        $this->load->helper('clicable_links');
        $this->template->set('radar', $this->radar);
        $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray('instagram', $this->c_user->id, $this->profile->id);
        foreach($access_tokens as $access_token) {
            $account = array();
            try {
                /* @var Socializer_Instagram $instagram */
                $instagram = Socializer::factory('Instagram', $this->c_user->id, $access_token);
                $user_id = $instagram->getUserId();
                if(isset($_GET['page'])){
                    $page = $_GET['page'];
                }else{
                    $page =null;
                }
                $instagram_html = $this->getDataActivity($user_id, $page);
                if($this->template->is_ajax()){
                    echo $instagram_html; die;
                }
                $account['instagram_html'] = $instagram_html;
                $account['socializer'] = $instagram;
            } catch (Exception $e) {
                $account['socializer_error'] = $e->getMessage();
            }
            $accounts[] = $account;
        }

        CssJs::getInst()->c_js('social/activity', 'instagram');

        $this->template->set('accounts', $accounts);
        $this->template->set('c_user_id', $this->c_user->id);
        $this->template->render();
    }

    /**
     *
     *Instagram post like
     *
     * @access public
     * @return void
     */
    public function instagramLike($mediaId, $type = null)
    {

        $this->load->library('Socializer/socializer');
        $instagram = Socializer::factory('Instagram', $this->c_user->id);

        $response = $instagram->postLike($mediaId);
        if (!$response->data) {
            $result['success'] = true;
            if ($type == 'crm') {
                Crm_directory_activity::inst()->update_other_field($mediaId, 'i_like', 'true');
            } else {
                Mention::inst()->update_other_field($mediaId, 'i_like', 'true');
            }

        } else {
            $result['success'] = false;
            $result['error'] = $response->error_message;
        }

        echo json_encode($result);
    }

    /**
     *
     *Instagram remove like
     *
     * @access public
     * @return void
     */
    public function instagramDislike($mediaId, $type = null)
    {

        $this->load->library('Socializer/socializer');
        $instagram = Socializer::factory('Instagram', $this->c_user->id);

        $response = $instagram->removeLike($mediaId);
        if (!$response->data) {
            $result['success'] = true;
            if ($type == 'crm') {
                Crm_directory_activity::inst()->update_other_field($mediaId, 'i_like', 'false');
            } else {
                Mention::inst()->update_other_field($mediaId, 'i_like', 'false');
            }
        } else {
            $result['success'] = false;
            $result['error'] = $response->error_message;
        }

        echo json_encode($result);
    }

    /**
     * Get all comments for selected instagram media
     *
     * @access public
     * @param null $mediaId
     * @return void
     */
    public function instagram_get_comments( $mediaId ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('instagram')) {
                $result['success'] = false;
                $result['error'] = "You don't have access to this social";

            } else {
                if( $mediaId != null) {
                    $this->load->library('Socializer/socializer');
                    $instagram = Socializer::factory('instagram', $this->c_user->id);
                    $comments = $instagram->mediaComments($mediaId);
                    $html = '';
                    if(!empty($comments->data)) {
                            foreach ( $comments->data as $_comment ) {
                                $html .= $this->template->block(
                                    '_comment',
                                    'social/activity/blocks/_one_instagram_comment',
                                    array('comment' => $_comment, 'socializer' => $instagram, 'radar' => $this->radar)
                                );
                            }
                    }

                }
                $result['success'] = true;
                $result['html'] = $html;
            }

            echo json_encode($result);;
        }
    }

    /**
     * Remove instagram remove comment
     *
     * @access public
     * @param null $commentId
     * @param null $mediaId
     * @internal param $comment_id
     */
    public function instagram_remove_comment( $commentId = null, $mediaId = null ) {
        if( $this->template->is_ajax() ) {
            echo $this->removeComment('instagram', $mediaId, $commentId);
        }
    }

    /**
     * Remove comment from social media
     *
     * @param $social
     * @param $mediaId
     * @param $commentId
     * @return string
     */
    protected function removeComment($social, $mediaId, $commentId)
    {
        if( $this->template->is_ajax() ) {
            if ( $commentId != null ) {
                try {
                    $this->load->library('Socializer/socializer');
                    $socializer = Socializer::factory(ucfirst($social), $this->c_user->id);

                    switch ($social) {
                        case 'facebook':
                            $remove = $socializer->remove_comment( $commentId );
                            if ($mediaId && $remove) {
                                Mention::inst()->update_other_field($mediaId, 'comments', 'dec');
                            }
                            break;

                        case 'instagram':
                            $remove  = $socializer->deleteMediaComment($mediaId, $commentId);
                            if ($mediaId && $remove) {
                                Crm_directory_activity::inst()->update_other_field($mediaId, 'comments', 'dec');
                            }

                    }
                    $result = array('success' => $remove);

                } catch (Exception $e) {
                    $result = array();
                    $result['success'] = false;
                    $result['error'] = $e->getMessage();
                }
                if ($social == 'facebook' && !empty($result['error'])) {
                    $result['error'] = $socializer->facebookErrorTransformer($result['error']);
                }
                return json_encode($result);
            }
        }
    }

    /**
     * Used to add new comment for instagram media
     *
     * @access public
     * @param null $mediaId
     * @param null $type
     */
    public function instagram_comment( $mediaId = null, $type = null) {

        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            if(isset($post['message']) && $mediaId != null) {
                try {
                    $this->load->library('Socializer/socializer');
                    $instagram = Socializer::factory('Instagram', $this->c_user->id);
                    $comment = $instagram->postMediaComment($mediaId, $post['message'] );

                    if (empty($comment->meta->error_message)) {
                        if ($type == 'crm') {
                            Crm_directory_activity::inst()->update_other_field($mediaId, 'comments', 'inc');
                        } else {
                            Mention::inst()->update_other_field($mediaId, 'comments', 'inc');
                        }

                    } else {
                        throw new Exception($comment->meta->error_message);
                    }
                    $result['success'] = true;
                    $result['html'] = $this->template->block(
                        '_comment',
                        'social/activity/blocks/_one_instagram_comment',
                        array('comment' =>  $comment, 'socializer' => $instagram, 'radar' => $this->radar)
                    );
                } catch(Exception $e) {
                    $result = array();
                    $result['success'] = false;
                    $result['error'] = $e->getMessage();
                }

                echo json_encode($result);
            }
        }
    }

    private function getDataActivity($user_id, $page = null){
        $this->load->config('instagram_settings');
        $limit = $this->config->item('instagram_limit');
        if(isset($page) && $page>1){
            $offset = ($page-1)*$limit;
        }else{
            $offset = null;
        }
        
        $social_activity = new Social_activity();
        $userRecent = $social_activity
            ->where(array('user_id'=> $user_id, 'social'=>'instagram'))
            ->order_by('created_at')
            ->get($limit, $offset);
        $instagram_html = $this->_get_instagram_html($userRecent->all);
        return $instagram_html;
    }


    /**
     * Used to call Socializer Instagram library and get user feed through Twitter API
     *
     * @access public
     * @param $data
     * @return
     * @internal param $type
     * @internal param $page_number
     */
    private function _get_instagram_html($data) {
        
        $user_images = $data;
        
        if(count($user_images) > 0 && !isset($user_images->errors)) {
            
            $block_data['data'] = $user_images;
            $instagram_html = $this->load->view('social/activity/blocks/_instagram_feed', $block_data, true);
        } else {
            $this->addFlash('Can\'t load images. Try to reconnect your account.');
            $instagram_html = $this->load->view('blocks/alert', array(), true);
        }

        return $instagram_html;
    }

    public function google() {
        if(!$this->_check_access('google')) {
            $this->template->set('socializer_error', 'Google not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.');
            return $this->template->render();
        }

        $this->load->library('Socializer/socializer');
        $this->load->helper('clicable_links');

        $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray('google', $this->c_user->id, $this->profile->id);

        if(isset($_GET['token_id'])) {
            $token = new Access_token($_GET['token_id']);
            $token = $token->to_array();
        } else {
            $token = $access_tokens[0];
        }

        if(isset($_GET['page'])){
            $page = $_GET['page'];
        }else{
            $page = null;
        }

        try {
            /* @var Socializer_Google $google */
            $google = Socializer::factory('Google', $this->c_user->id, $token);

            if($this->template->is_ajax()){
                $this->load->config('google_settings');
                $limit = $this->config->item('google_plus_feed_posts_limit');

                $googleActivities = $google->getUserActivities(array(
                    'maxResults' => $limit,
                    'pageToken' => $page
                ));

                $nextPageToken = $googleActivities['nextPageToken'];
                $items = $googleActivities['items'];

                $googleHtml = $this->googleActivitiesWrapHtml($items);
                echo json_encode(array(
                    'nextPageToken' => $nextPageToken,
                    'html' => $googleHtml
                ));
                exit;
            }

            $this->template->set('socializer', $google);
            $this->template->set('nextPageToken', '');
        } catch (Exception $e) {
            $this->template->set('socializer_error', $e->getMessage());
        }

        JsSettings::instance()->add('pageToken', $page);

        CssJs::getInst()->c_js('social/activity', 'google');

        $this->template->set('c_user_id', $this->c_user->id);
        $this->template->set('token', $token);
        $this->template->set('access_tokens', $access_tokens);
        $this->template->render();
    }


    /**
     * Wrap activity items from google+ to html
     *
     * @param $items
     *
     * @return mixed
     */
    protected function googleActivitiesWrapHtml($items)
    {
        if(!count($items)) {
            $this->addFlash('The Data doesn\'t exists.');
            return $this->load->view('blocks/alert', array(), true);
        }

        return  $this->load->view(
            'social/activity/blocks/_google_feed', array(
                'activities' => $items,
                'radar' => $this->radar
            ),
            true
        );
    }

    /**
     * Get all comments for selected google post
     *
     * @access public
     * @param $activityId
     * @internal param null $post_id
     */
    public function google_get_comments( $activityId ) {
        if( $this->template->is_ajax() ) {
            if( ! $this->_check_access('google')) {
                $result['success'] = false;
                $result['error'] = 'You do not have access to this social';
                
            } else {
                if( $activityId != null) {
                    $this->load->library('Socializer/socializer');
                    $google = Socializer::factory('Google', $this->c_user->id);
                    $comments = $google->getComments($activityId);
                    $html = '';
                    if( isset($comments['items']) ) {
                        
                            foreach ( $comments['items']as $_comment ) {
                                $html .= $this->template->block(
                                    '_comment',
                                    'social/activity/blocks/_one_google_comment',
                                    array('_comment' => $_comment, 'socializer' => $google)
                                );
                            }
                       
                    }
                    $result['success'] = true;
                    $result['html'] = $html;
                }
            }
            echo json_encode($result);    
        }
    }
    
    /**
     * Load Linkedin updates tab
     *
     * @access public
     * @return void
     */
    public function linkedin()  {

        if( ! $this->_check_access('linkedin')) {
            $this->template->set('socializer_error', 'Linkedin not connected. <a class="configure-fblink" href="' . site_url('settings/socialmedia') . '">Do it now</a>.');
            $this->template->render();
            return;
        }

        $linkedin_html = $this->_get_updates_html();
        $this->template->set('linkedin_html', $linkedin_html);

        CssJs::getInst()->c_js('social/activity', 'linkedin');
        $this->template->render();
    }
    
    /**
     * Used to call Socializer Linkedin library and get user feed through Linkedin API
     *
     * @access public
     * @param $page
     * @return html
     */
    private function _get_updates_html($page = 1) {
        $this->load->config('linkedin_settings');
        $limit = $this->config->item('linkedin_limit');
        
        if(isset($page) && $page>1){
            $offset = ($page-1)*$limit;
        }else{
            $offset = null;
        }
        
        $social_activity = new Social_activity();
        $userRecent = $social_activity->where(array('user_id'=> $this->c_user->id, 'social'=>'linkedin'))->order_by('created_at', 'DESC')->get($limit, $offset);
        $linkedin_html = $this->_get_linkedin_html($userRecent->all);
        return $linkedin_html;
    }
        
    /**
     * Return html with feed data of linkedin updates
     *
     * @param $data array of Social_activity elements
     * @return html of updates
     */
    private function _get_linkedin_html($data) {
        
        if(count($data) > 0 && !isset($data->errors)) {
            $block_data['updates'] = $data;
            $block_data['radar'] = $this->radar;
            $linkedin_html = $this->load->view('social/activity/blocks/_linkedin_feed', $block_data, true);
            return $linkedin_html;
        }

        return '';
    }
    
    /**
     * Used to send new page linkedin updates html to ajax function(load new page)
     *
     * @access public
     * @param $page_number
     */
    public function load_updates( $page_number = 1 ) {
        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            //$type = isset($post['type']) ? $post['type'] : 'feed';
            $html = $this->_get_updates_html($page_number);
            echo $html;
        }
    }
    
    /**
     * Linkedin get comments of update by AJAX
     *
     * @access public
     * @return string
     *
     */
    public function linkedin_get_comments(){
        $this->load->library('Socializer/socializer');
        $linkedin = Socializer::factory('Linkedin', $this->c_user->id);
        $comments = $linkedin->getComments($_GET['key']);
        $html = '';
    
        if( is_array($comments) && !empty($comments) ) {
            foreach ( $comments as $comment ) {
               $html .= $this->template->block(
                                            '_comment',
                                            'social/activity/blocks/_one_linkedin_comment',
                                            array('comment' => $comment, 'socializer' => $linkedin)
                                            );
                }
            
        }
        echo $html; 
    }
    
    /**
     *
     * Linkedin post comment
     *
     * @access public
     * @return void
     */
    public function linkedin_comment(){
        
        $this->load->library('Socializer/socializer');
        $linkedin = Socializer::factory('Linkedin', $this->c_user->id);
        
        $linkedin->postComment();
        $_GET['key'] = $_POST['key'];
        $this->linkedin_get_comments();
    }
    /**
     *
     *Linkedin post like
     *
     * @access public
     * @return void
     */
    public function linkedin_like(){
        
        $this->load->library('Socializer/socializer');
        $linkedin = Socializer::factory('Linkedin', $this->c_user->id);

        $response = $linkedin->like();
        if ($response['linkedin'] == '') {
            $result['success'] = true;
            Social_activity::inst()->update_other_field($this->input->post('key'), 'liked', 'true');
        } else {
            $result['success'] = false;
            $result['error'] = simplexml_load_string($response['linkedin'])->message;
        }

        echo json_encode($result);
    }
    
    /**
     * Linkedin post like
     *
     * @access public
     * @return void
     */
    public function linkedin_unlike(){
        
        $this->load->library('Socializer/socializer');
        $linkedin = Socializer::factory('Linkedin', $this->c_user->id);
        
        $response = $linkedin->unlike();
        if ($response['linkedin'] == '') {
            $result['success'] = true;
            Social_activity::inst()->update_other_field($this->input->post('key'), 'liked', 'false');
        } else {
            $result['success'] = false;
            $result['error'] = simplexml_load_string($response['linkedin'])->message;
        }

        echo json_encode($result);
    }
    /**
     * Check access to view the page
     * If user haven't access token for selected social - redirect to Social Settings page
     *
     * @access private
     * @param $type
     * @return bool
     */
    private function _check_access( $type ) {
        $tokens = Access_token::inst()->get_by_type( $type, $this->c_user->id, $this->profile->id );
        if(empty($tokens)) {
            return false;
        } else {
            return true;
        }
    }
}