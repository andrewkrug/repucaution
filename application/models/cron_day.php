<?php

/**
 * Class Cron_day
 *
 * @property integer    $id
 * @property string     $days
 *
 * @property Social_post_cron $social_post_cron
 */
class Cron_day extends DataMapper {

    var $table = 'cron_days';

    var $has_one = array(
    );

    var $has_many = array(
        'social_post_cron' => array(
            'class' => 'social_post_cron',
            'join_self_as' => 'cron_day',
            'join_other_as' => 'social_post_cron',
            'join_table' => 'social_posts_cron_by_days'
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
     * @return Cron_day
     */
    public static function inst($id = NULL) {
        return new self($id);
    }
}
