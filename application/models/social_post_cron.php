<?php

/**
 * Class Social_post_cron
 *
 * @property integer        $id
 * @property integer        $user_id
 * @property string         $url
 * @property string         $description
 * @property integer        $profile_id
 * @property string         $timezone
 * @property array          $post_to_socials
 * @property array          $time_in_utc
 *
 * @property User          $user
 * @property Media         $media
 * @property Cron_day      $cron_day
 * @property Social_post   $social_post
 */
class Social_post_cron extends DataMapper {

    var $table = 'social_posts_cron';

    var $has_one = array(
        'user'
    );

    var $has_many = array(
        'media' => array(
            'class' => 'media',
            'join_self_as' => 'post',
            'join_other_as' => 'media',
            'join_table' => 'posts_cron_media'
        ),
        'cron_day' => array(
            'class' => 'cron_day',
            'join_other_as' => 'cron_day',
            'join_self_as' => 'social_post_cron',
            'join_table' => 'social_posts_cron_by_days'
        ),
        'social_post' => array(
            'class' => 'social_post',
            'join_self_as' => 'post_cron'
        )
    );


    /**
     * @param null|integer $id
     */
    function __construct($id = NULL) {
        parent::__construct($id);
    }

    /**
     * @param null|integer $id
     *
     * @return Social_post_cron
     */
    public static function inst($id = NULL) {
        return new self($id);
    }


    /**
     * Validate cron values
     * @param $post
     *
     * @return array
     */
    public static function validate_cron($post) {
        $errors = array();

        if(!isset($post['cron_day']) || empty($post['cron_day'])) {
            $errors['message'] = 'You must select at least one day.';
        }

        return $errors;
    }

    /**
     * Insert post data to database
     *
     * @access public
     *
     * @param $feeds
     * @param $user_id
     * @param $profile_id
     */
    public static function add_new_post($feeds, $user_id, $profile_id) {
        $post = isset($feeds['post_id']) ? new self((int)$feeds['post_id']) : new self;
        $post->description = $feeds['description'];
        $post->url = isset($feeds['url']) ? $feeds['url'] : '';

        $post->post_to_socials = serialize($feeds['post_to_socials']);
        $post->setTimeInUtc($feeds['cron_schedule_time'], $feeds['timezone']);
        $post->user_id = $user_id;
        $post->profile_id = $profile_id;
        $post->timezone = $feeds['timezone'];

        $days = Cron_day::inst()->where_in('day', $feeds['cron_day'])->get();

        if(isset($feeds['image_name'])) {
            if(!empty($feeds['image_name'])) {
                self::_save_attachment($post, $feeds, $user_id);
            }
        }

        $post->save($days->all, 'cron_day');
    }

    /**
     * @param $post
     * @param $feeds
     * @param $user_id
     */
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
     * @return string
     */
    public function getSocialsString() {
        $socials = unserialize($this->post_to_socials);
        foreach($socials as &$social) {
            $social = ucfirst($social);
        }
        return implode(', ', $socials);
    }

    /**
     * @return array
     */
    public function getTimeInUtc() {
        return unserialize($this->time_in_utc);
    }

    /**
     * @return array
     */
    public function getTimeInTimezone() {
        $time_array = unserialize($this->time_in_utc);
        $time_in_timezone_array = [];
        $timezone = new DateTimeZone($this->timezone);
        $utc_timezone = new DateTimeZone('UTC');
        foreach($time_array as $time) {
            $_time = new DateTime($time, $utc_timezone);
            $_time->setTimezone($timezone);
            $time_in_timezone_array[] = $_time->format(lang('time_format'));
        }
        return $time_in_timezone_array;
    }

    /**
     * @param array         $time_in_utc
     * @param null|string   $timezone
     */
    public function setTimeInUtc($time_in_utc, $timezone = null) {
        if($timezone) {
            $_timezone = new DateTimeZone($timezone);
            $_utc_timezone = new DateTimeZone('UTC');
            foreach($time_in_utc as &$time) {
                $date = new DateTime($time, $_timezone);
                $date->setTimezone($_utc_timezone);
                $time = $date->format(lang('time_format'));
            }
        }
        $this->time_in_utc = serialize($time_in_utc);
    }

    /**
     * @return array
     */
    public function getDays() {
        $days = $this->cron_day->get();
        $days_array = [];
        foreach($days as $day) {
            $days_array[] = $day->day;
        }
        return $days_array;
    }
}
