<?php

/**
 * Class Social_analytics
 *
 * @property integer $id
 * @property integer $access_token_id
 * @property string $type
 * @property string $date - date formatted in 'Y-m-d' format.
 * @property integer $value
 *
 * @property Access_token $access_token
 */
class Social_analytics extends DataMapper {

    const RETWEETS_ANALYTICS_TYPE = 'retweets_count';
    const FAVOURITES_ANALYTICS_TYPE = 'favourites_count';
    const NEW_FOLLOWING_ANALYTICS_TYPE = 'new_following_count';
    const NEW_UNFOLLOWERS_ANALYTICS_TYPE = 'new_unfollowers_count';
    const NEW_UNFOLLOWING_ANALYTICS_TYPE = 'new_unfollowing_count';
    const NEW_FOLLOWING_BY_SEARCH_ANALYTICS_TYPE = 'new_following_by_search_count';

    var $table = 'social_analytics';
    
    var $has_one = array(
        'access_token'
    );

    var $has_many = array();

    var $validation = array();

    /**
     * @param null $id
     */
    function __construct($id = NULL) {
        parent::__construct($id);
    }

    /**
     * @param null $id
     *
     * @return Social_analytics
     */
    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * @param $access_token_id
     * @param $type
     * @param $date - date formatted in 'Y-m-d' format.
     *
     * @return Social_analytics
     */
    public static function getAnalytics($access_token_id, $type, $date) {
        $social_analytics = new self();
        $social_analytics->where(array(
            'access_token_id' => $access_token_id,
            'type' => $type,
            'date' => $date
        ))->get(1);
        return $social_analytics;
    }

    /**
     * @param integer       $access_token_id
     * @param string        $type
     * @param integer       $value
     * @param null|string   $date - date formatted in 'Y-m-d' format.
     */
    public static function updateAnalytics($access_token_id, $type, $value, $date = null) {
        if(!$date) {
            $now = new DateTime('UTC');
            $date = $now->format('Y-m-d');
        }
        $social_analytics = Social_analytics::getAnalytics(
            $access_token_id,
            $type,
            $date
        );
        if(!$social_analytics->value) {
            $social_analytics->value = 0;
        }
        $social_analytics->value += (int) $value;
        $social_analytics->access_token_id = $access_token_id;
        $social_analytics->type = $type;
        $social_analytics->date = $date;
        $social_analytics->save();
    }

    /**
     * @param string $from - date formatted in 'Y-m-d' format.
     * @param string $to - date formatted in 'Y-m-d' format.
     *
     * @return Social_analytics
     */
    public function get_by_period($from, $to) {
        return $this
            ->by_period($from, $to)
            ->get();
    }

    /**
     * @param string $from - date formatted in 'Y-m-d' format.
     * @param string $to - date formatted in 'Y-m-d' format.
     * @param string $type
     *
     * @return Social_analytics
     */
    public function get_by_period_and_type($from, $to, $type) {
        return $this
            ->by_period($from, $to)
            ->by_type($type)
            ->get();
    }

    //Scopes
    /**
     * @param string $from - date formatted in 'Y-m-d' format.
     * @param string $to - date formatted in 'Y-m-d' format.
     *
     * @return Social_analytics
     */
    public function by_period($from, $to) {
        return $this->where(array(
            'date >= ' => $from,
            'date <= ' => $to
        ))->order_by('date', 'ASC');
    }

    /**
     * @param string $type
     *
     * @return Social_analytics
     */
    public function by_type($type) {
        return $this->where(array(
            'type' => $type
        ));
    }
}