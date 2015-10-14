<?php

/**
 * Class Social_post
 *
 * @property integer    $id
 * @property string     $description
 * @property string     $posting_type
 * @property integer    $user_id
 * @property string     $url
 * @property integer    $schedule_date
 * @property integer    $category_id
 * @property string     $timezone
 * @property string     $title
 * @property string     $post_to_groups
 * @property string     $post_to_socials
 * @property integer    $profile_id
 * @property integer    $post_cron_id
 */
class Social_post extends DataMapper {

    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $error_prefix = '<span class="message-error">';
    var $error_suffix = '</span>';
    
    public static $socials = array(
        'twitter',
        'facebook',
        'linkedin',
//        'google'
    );
    
    var $validation = array(
        'description' => array(
            'label' => 'Description',
            'rules' => array('required', 'trim'),
        ),
        'posting_type' => array(
            'label' => 'Posting type',
            'rules' => array('required')
        )
    );

    var $table = 'social_posts';

    var $has_one = array(
        'social_posts_category' => array(
            'model' => 'social_posts_category',
            'join_other_as' => 'category',
        ),
        'social_post_cron' => array(
            'model' => 'social_post_cron',
            'join_other_as' => 'post_cron',
        )
    );

    var $has_many = array(
        'media' => array(
            'class' => 'media',
            'join_self_as' => 'post',
            'join_other_as' => 'media',
            'join_table' => 'posts_media'
        ),
    );


    function __construct($id = NULL) {
        parent::__construct($id);
        
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public static function getActiveSocials($profile_id) {
        $_socials = self::$socials;
        foreach($_socials as $key => $_social) {
            if(!Social_group::hasSocialAccountByType($profile_id, $_social)) {
                unset($_socials[$key]);
            }
        }
        return $_socials;
    }

    /**
     * Validate a form
     * Check Description / URL / post type and social networks fields
     *
     * @access public
     * @param $feeds
     * @return array
     */
    public static function validate_post($feeds) {

        $errors = array();
        
        $category_slug = $feeds['attachment_type'];
        $feeds['title'] = 'from '.clear_domain(site_url());
        $post = new self;
        $post->from_array($feeds, array('description', 'url', 'posting_type', 'category_id'));
        if(!$post->posting_type) {
            $post->posting_type = 'immediate';
        }
        $post->validate();
        if ( ! $post->valid) {
            foreach($post->error->all as $err_key => $err_value) {
                $errors[ $err_key ] = $err_value;
            }
        }

        if( isset($feeds['url']) ) {
            if (!empty($feeds['url'])) {
                if (filter_var($feeds['url'], FILTER_VALIDATE_URL) === false ||
                    strstr($feeds['url'], '.') === false
                ) {
                    $errors['url'] = lang('email_error');
                }
            }
        }

        if(!isset($feeds['post_to_socials'])) {
            $errors[ 'post_to_groups[]' ] = lang('socials_error');
        }

        if(isset($feeds['posting_type'])) {
            //validate Schedule date
            if($feeds['posting_type'] != 'immediate') {
                $scheduled_errors = self::_validate_scheduled_data( $feeds );
                $errors = array_merge($errors, $scheduled_errors);
            }
        }

        if( $category_slug == 'photos' ) {
            if( empty($feeds['image_name']) ) {
                $errors['image_name'] = lang('image_error');
            }
        }

        if(!empty($feeds['post_to_socials']) && in_array('twitter', $feeds['post_to_socials'])){
            $twitter_limit = array(
                1 => 140,
                2 => 117,
                3 => 94
            );
            $input_category = 1;

            $file = (!empty($feeds['image_name']) || isset($feeds['file_name']) );
            $link = !empty($feeds['url']);

            if($file && $link){
                $input_category = 3;
            }elseif($file || $link){
                $input_category = 2;
            }
            if(mb_strlen($feeds['description']) > $twitter_limit[$input_category]){
                $errors['description'] = lang('twitter_error');
            }

        }
        if(!empty($feeds['post_to_socials']) && in_array('linkedin', $feeds['post_to_socials'])){
           if(mb_strlen($feeds['description']) > 400){
                $errors['description'] = lang('linkedin_error');
           }
         }
        if( $category_slug == 'videos' ) {
            $video_errors = self::_validate_video( $feeds );
            $errors = array_merge($errors, $video_errors);
        } else {
            if(empty($feeds['url']) && isset($feeds['post_to_socials']) && in_array('linkedin', $feeds['post_to_socials'])){
                $errors['url'] = lang('url_error');
            }
        }
        return $errors;
    }

    private function _validate_video( $feeds ) {

        $errors = array();
        if( empty($feeds['image_name']) ) {
            $errors['image_name'] = lang('video_error');
        }
        
        if(isset($feeds['title'])) {
            if(empty($feeds['title'])) {
                $errors['title'] = lang('video_title_error');
            }
        } else {
            
            $errors['title'] = lang('video_title_error');
        }

        $youtube_token = Access_token::inst()->get_youtube_token($feeds['user_id']);
        foreach($feeds['post_to_groups'] as $group_id) {
            if(Social_group::hasSocialAccountByType($group_id, 'twitter')
                || Social_group::hasSocialAccountByType($group_id, 'linkedin')) {
                if( !$youtube_token->exists() ) {
                    $errors['post_to_groups[]'] = lang('youtube_error');
                    break;
                }
            }
        }

        return $errors;
    }

    private function _validate_scheduled_data ( $feeds ) {
        $errors = array();

        if(isset($feeds['schedule_date'])) {
            if(self::_get_schedule_date($feeds) < strtotime('now')) {
                // $errors['schedule_date'] = '<span class="message-error">Time cant be less current date. </span>';
            }
        } else {
            $errors['schedule_date'] = lang('schedule_error');
        }

        return $errors;
    }

    public static function checkPostByDescription($description) {
        $post = new self;
        $post->where(['description' => $description])->get(1);
        return $post->exists();
    }


    /**
     * Insert post data to database
     *
     * @access public
     * @param $feeds
     * @param $user_id
     */
    public static function add_new_post($feeds, $user_id, $profile_id) {
        $post = isset($feeds['post_id']) ? new self((int)$feeds['post_id']) : new self;
        $post->from_array($feeds, array('description', 'posting_type'));
        $post->url = isset($feeds['url']) ? $feeds['url'] : '';

        $post->post_to_groups = serialize($feeds['post_to_groups']);
        $post->post_to_socials = serialize($feeds['post_to_socials']);
        $post->user_id = $user_id;
        $post->profile_id = $profile_id;
        $post->category_id = isset($feeds['category_id']) ? (int)$feeds['category_id'] : 0;

        if($feeds['posting_type'] != 'schedule') {
            if(isset($feeds['post_id'])) {
                $post->delete();
            }
            self::_send_to_social($feeds, $user_id);
        } else {
            $post->schedule_date = self::_get_schedule_date($feeds);
            $post->timezone = $feeds['timezone'];
        }

        if(isset($feeds['image_name'])) {
            if(!empty($feeds['image_name'])) {
                self::_save_attachment($post, $feeds, $user_id);
            }
        }

        $post->save();
    }

    public function getScheduledDate($format) {
        $date = new DateTime('UTC');
        $date->setTimestamp($this->schedule_date);
        $date->setTimezone(new DateTimeZone($this->timezone));
        return $date->format($format);
    }

    public static function post_video($feeds, $user_id, $profile_id) {

        if($feeds['posting_type'] != 'schedule') {
            if(isset($feeds['post_id'])) {
                $post = Social_post::inst((int)$feeds['post_id']);
                $post->delete();
            }
            self::_send_video_to_socials( $feeds, $user_id );
        } else {
            $post = isset($feeds['post_id']) ? new self((int)$feeds['post_id']) : new self;
            $post->from_array($feeds, array('description', 'posting_type'));

            $post->post_to_groups = serialize($feeds['post_to_groups']);
            $post->post_to_socials = serialize($feeds['post_to_socials']);

            $post->user_id = $user_id;
            $post->profile_id = $profile_id;
            $post->title = 'from '.clear_domain(site_url());;
            $post->category_id = isset($feeds['category_id']) ? (int)$feeds['category_id'] : 0;
            $post->schedule_date = self::_get_schedule_date($feeds);
            $post->timezone = $feeds['timezone'];
            self::_save_attachment($post, $feeds, $user_id);
            $post->save();
        }
    }

    public function get_user_scheduled_posts( $user_id, $profile_id, $page = 1, $offset, $category ) {
        $where = array(
            'user_id' => $user_id,
            'profile_id' => $profile_id,
            'schedule_date !=' => 'null',
            'posting_type' => 'schedule'
        );

        if($category != 'all') {
            $where['category_id'] = $category; //filter by category
        }

        $posts = $this->where($where)
            ->order_by('schedule_date', 'ASC')
            ->get_paged($page, $offset);
        return $posts;
    }

    /**
     * Used to Send data to social
     * Send message and bit.ly-formed URL
     *
     * @access private
     * @param $post
     * @param $user_id
     * @throws Exception
     */
    public function _send_to_social( $post, $user_id ) {
        $post['url'] = isset($post['url']) ? $post['url'] : '';
        $inTwitter = in_array('twitter', $post['post_to_socials']);
        $inFacebook = in_array('facebook', $post['post_to_socials']);
        $inLinkedin = in_array('linkedin', $post['post_to_socials']);
        foreach ($post['post_to_groups'] as $group_id) {
            $group = new Social_group($group_id);
            foreach ($group->access_token->get()->all_to_array() as $access_token) {
                if($access_token['type'] == 'twitter' && $inTwitter) {
                    /* @var Socializer_Twitter $twitter */
                    $twitter = Socializer::factory('Twitter', $user_id, $access_token );
                    $tweet_len = strlen($post['url']) + strlen($post['description']);
                    if($tweet_len > $twitter::MAX_TWEET_LENGTH) {
                        $message = substr($post['description'], 0, $twitter::MAX_TWEET_LENGTH - strlen($post['url']) - 1) . ' ' . $post['url'];
                    } else {
                        $message = $post['description'] . ' ' . $post['url'];
                    }
                    if (empty($post['image_name'])) {
                        $result = $twitter->tweet($message, null);
                    } else {
                        $result = $twitter->tweet_with_image($message, $post['image_name']);
                    }
                    if (!empty($result->errors)) {
                        throw new Exception('Twitter: '.$result->errors[0]->message);
                    }

                }

                if($access_token['type'] == 'facebook' && $inFacebook) {
                    if(isset($post['image_name'])) {
                        if(empty($post['image_name'])) {
                            unset($post['image_name']);
                        }
                    }
                    if(isset($post['url'])) {
                        if(empty($post['url'])) {
                            unset($post['url']);
                        }
                    }

                    self::_send_to_facebook($post, $user_id, $access_token);
                }

                if($access_token['type'] == 'linkedin' && $inLinkedin) {
                    /* @var Socializer_Linkedin $linkedin */
                    $linkedin = Socializer::factory('linkedin', $user_id, $access_token );
                    $linkedint_len = strlen($post['description']);
                    if($linkedint_len > $linkedin::MAX_DESCRIPTION_LENGTH){
                        $post['description'] = substr($post['description'], 0, $linkedin::MAX_DESCRIPTION_LENGTH) ;
                    }

                    $response = $linkedin->createPost($post);

                    if(!$response['success']) {
                        $error = $linkedin->xmlToArray($response['linkedin']);
                        throw new Exception('Linkedin: '.$error['error']['children']['message']['content']);
                    }
                }
            }
        }
        if(isset($post['image_name']) && !$inLinkedin && (!isset($post['post_cron_id']) || !$post['post_cron_id'])) {
            self::drop_attachment(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name']);
        }
    }

    public function _send_video_to_socials( $post, $user_id ) {
        $inTwitter = in_array('twitter', $post['post_to_socials']);
        $inFacebook = in_array('facebook', $post['post_to_socials']);
        $inLinkedin = in_array('linkedin', $post['post_to_socials']);
        foreach ($post['post_to_groups'] as $group_id) {
            $group = new Social_group($group_id);
            $video = '';
            foreach ($group->access_token->get()->all_to_array() as $access_token) {
                if($access_token['type'] == 'twitter' && $inTwitter) {
                    if (!$video) {
                        /* @var Socializer_Google $twitter */
                        $youtube_uploader = Socializer::factory('Google', $user_id);
                        $video = $youtube_uploader->post_video(
                            $post['title'],
                            $post['description'],
                            $post['image_name']
                        );
                    }
                    /* @var Socializer_Twitter $twitter */
                    $twitter = Socializer::factory('Twitter', $user_id, $access_token);
                    $twitter->tweet(
                        $post['description']. ' http://www.youtube.com/watch?v='. $video['id'],
                        null
                    );
                } elseif($access_token['type'] == 'linkenid' && $inLinkedin) {
                    if (!$video) {
                        /* @var Socializer_Google $twitter */
                        $youtube_uploader = Socializer::factory('Google', $user_id);
                        $video = $youtube_uploader->post_video(
                            $post['title'],
                            $post['description'],
                            $post['image_name']
                        );
                    }
                    /* @var Socializer_Linkedin $linkedin */
                    $linkedin = Socializer::factory('Linkedin', $user_id, $access_token);
                    $data = array(
                        'title' => $post['title'],
                        'description' => $post['description'],
                        'url' => ' http://www.youtube.com/watch?v='. $video['id'],
                    );
                    $linkedin->createPost($data);
                } elseif($access_token['type'] == 'facebook' && $inFacebook) {
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $user_id, $access_token);
                    $facebook->post_with_video(
                        $post['title'],
                        $post['description'],
                        $post['image_name']
                    );
                }
            }
        }
        self::drop_attachment(__DIR__.'/../../public/uploads/'.$user_id.'/'.$post['image_name']);
    }

    /**
     * Send post to Facebook
     *
     * @param $post
     * @param $user_id
     * @param $access_token
     */
    private function _send_to_facebook($post, $user_id, $access_token) {
        $facebook = Socializer::factory('Facebook', $user_id, $access_token);
        if(isset($post['url'])) {
            if(isset($post['image_name'])) {
                $description = $post['description'].' '. $post['url'];
                $facebook->post_with_picture($description, $post['image_name'], $post['url']);
            } else {
                $facebook->post($post['description'], $post['url']);
            }
        } else {
            if(isset($post['image_name'])) {
                $facebook->post_with_picture($post['description'], $post['image_name'], null);
            } else {
                $facebook->post($post['description'], null);
            }
        }
    }

    private function _save_attachment($post, $feeds, $user_id) {
        $category_slug = $feeds['attachment_type'];
        $media = new Media();
        $media->path = __DIR__.'/../../public/uploads/'.$user_id.'/'.$feeds['image_name'];
        if($category_slug == 'videos') {
            $media->type = 'video';
        } else {
            $media->type = 'image';
        }
        $media->user_id  = $user_id;
        $media->save();
        $post->save($media, 'media');
    }

    /**
     * Create UTC-formed date from time passed by user
     *
     * @access private
     * @param $post
     * @return int
     */
    private function _get_schedule_date($post) {
        $date = new DateTime($post['schedule_date'].' '.$post['timezone']);
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->getTimestamp();
    }


    public function drop_attachment($path) {
        unlink($path);
    }


    /**
     * Delete scheduled post from list
     *
     * @access public
     *
     * @param $post_id
     * @param $user_id
     *
     * @return bool
     */
    public function delete_scheduled( $post_id, $user_id ) {
        $post = $this->where(array('id' => $post_id, 'user_id' => $user_id, 'posting_type' => 'schedule'))
            ->get();
        if( $post->result_count() > 0 ) {
           return $post->delete();
        } else {
            return true;
        }
    }

    /**
     * Check if post has media
     *
     * @return Media||null
     */
    public function isMediaPost()
    {
        $media = $this->media->get();

        return ($media->id) ? $media : null;
    }

    /**
     * @return array
     */
    public function getPostToGroups() {
        return unserialize($this->post_to_groups);
    }
}
