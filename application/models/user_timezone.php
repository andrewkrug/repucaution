<?php

/**
 * Class User_timezone
 *
 * Set timezone - using in settings/socialmedia
 * Get timezone - using in social scheduled posts to set post timezone (time preferences)
 */
class User_timezone extends DataMapper {

    var $table = 'user_timezones';


    var $has_one = array(
        // user
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }


    /**
     * Set user timezone
     *
     * @access public
     * @param $user_id
     * @param $timezone
     */
    public static function save_timezone($user_id, $timezone) {
        $zone = new User_timezone();
        $zone = $zone->where('user_id', $user_id)
            ->get();
        $zone->user_id = $user_id;
        $zone->timezone = $timezone;
        $zone->save();
    }

    /**
     * Get user selected timezone
     *
     * Timezone string in database now consists of timezone name
     * and exact city, separated by underscore, because dropdown was not
     * working correctly with timezones only (there were multiple cities for one timezone)
     *
     * @access public
     * @param $user_id
     * @param bool $dont_cut_exact_city
     * @return mixed
     */
    public static function get_user_timezone($user_id, $dont_cut_exact_city = FALSE) {
        $zone = new User_timezone();
        $zone = $zone->where('user_id', $user_id)->get();
        $timezone = $zone->timezone;
        if ($dont_cut_exact_city === FALSE) {
            $parts = explode('^', $timezone);
            $timezone = $parts[0];
        }
        return $timezone;
    }

    /**
     * Check - have user timezone or not
     *
     * @param $user_id
     * @return bool
     */
    public static function is_user_set_timezone( $user_id ) {
        $zone = new User_timezone();
        $zone = $zone->where('user_id', $user_id)
            ->get();
        return $zone->id ? true : false;
    }
}