<?php

/**
 * Class User_search_keyword
 *
 * @property integer $id
 * @property string $keyword
 * @property integer $user_id
 * @property integer $profile_id
 * @property bool $is_deleted
 * @property bool $exact
 * @property array $other_fields
 * @property integer $min_followers
 * @property integer $max_followers
 * @property string $time_start
 * @property string $time_end
 * @property integer $max_id
 */
class User_search_keyword extends DataMapper {

    var $table = 'user_search_keywords';
    
    var $has_one = array('user');
    var $has_many = array();

    var $validation = array(
        'keyword' => array(
            'label' => 'Keyword',
            'rules' => array('always_validate', 'trim', 'max_length' => 100, 'min_length' => 2),
        ),
        'other_fields' => array(
            'label' => 'Include & Exclude fields',
            'rules' => array('trim', 'max_length' => 2000),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * User's not deleted keywords
     *
     * @param int $user_id
     * @param     $profile_id
     *
     * @return Mention_keyword
     */
    public function get_user_keywords($user_id, $profile_id) {
        return $this
            ->where(array(
                'is_deleted' => 0,
                'user_id' => $user_id,
                'profile_id' => $profile_id
            ))
            ->get();
    }

    /**
     * Build model from array
     *
     * @param array    $data    - post data
     * @param int      $user_id - keyword owner id
     * @param int      $profile_id
     * @param int|NULL $id      - keyword id, if already exists
     *
     * @return Mention_keyword
     */
    public function fill_from_array($data, $user_id, $profile_id, $id = NULL) {

        $inst = $this;

        if ($id) {
            $inst->where('id', $id)->get(1);
        }

        $keyword = Arr::get($data, 'keyword');
        $min_followers = Arr::get($data, 'min_followers');
        $max_followers = Arr::get($data, 'max_followers');
        $time_start = $this->prepare_time(
            Arr::get($data, 'hours_start'),
            Arr::get($data, 'minutes_start'),
            Arr::get($data, 'am_pm_start')
        );
        $time_end = $this->prepare_time(
            Arr::get($data, 'hours_end'),
            Arr::get($data, 'minutes_end'),
            Arr::get($data, 'am_pm_end')
        );
        $exact = Arr::get($data, 'exact', FALSE) ? TRUE : FALSE;
        $other_fields = $inst->prepare_other_fields($data);

        // create new if keyword is changed 
        if ($inst->exists()
            && (
                $inst->keyword != $keyword
                || $inst->exact != $exact
                || $inst->other_fields != $other_fields
                || $inst->min_followers != $min_followers
                || $inst->max_followers != $max_followers
                || $inst->time_start != $time_start
                || $inst->time_end != $time_end
            )
        ) {
            $inst = new self;
        }

        $inst->user_id = $user_id;
        $inst->profile_id = $profile_id;
        $inst->keyword = $keyword;
        $inst->exact = $exact;
        $inst->other_fields = $other_fields;
        $inst->min_followers = $min_followers;
        $inst->max_followers = $max_followers;
        $inst->time_start = $time_start;
        $inst->time_end = $time_end;

        return $inst;
    }

    /**
     * @param $hours
     * @param $minutes
     * @param $am_pm
     * @return string
     */
    private function prepare_time($hours, $minutes, $am_pm) {
        if ($am_pm == 'pm') {
            $hours += 12;
        }
        if (strlen((string)$hours) == 1) {
            $hours = '0'.$hours;
        }
        if (strlen((string)$minutes) == 1) {
            $minutes = '0'.$minutes;
        }
        $time = $hours.':'.$minutes;
        return $time;
    }

    /**
     * @param string $time
     * @return array
     */
    private function unserializeTime($time) {
        $result = array();
        $timeArray = preg_split('|:|', $time);
        $hours = (int)$timeArray[0];
        $minutes = (int)$timeArray[1];
        if ($hours > 12) {
            $hours -= 12;
            $am_pm = 'pm';
        } else {
            $am_pm = 'am';
        }
        $result['hours'] = $hours;
        $result['minutes'] = $minutes;
        $result['am_pm'] = $am_pm;
        return $result;
    }

    /**
     * @param string $time
     * @param DateTime $date
     * @return DateTime
     */
    private function addTimeToDate($time, $date) {
        $timeArray = preg_split('|:|', $time);
        $hours = (int)$timeArray[0];
        $minutes = (int)$timeArray[1];
        $date->modify($hours.' hours');
        $date->modify($minutes.' minutes');
        return $date;
    }

    /**
     * @param DateTime $date
     * @return DateTime
     */
    public function getStartDateTime($date) {
        return $this->addTimeToDate($this->time_start, $date);
    }

    /**
     * @param DateTime $date
     * @return DateTime
     */
    public function getEndDateTime($date) {
        return $this->addTimeToDate($this->time_end, $date);
    }

    /**
     * @return array
     */
    public function getStartTime() {
        return $this->unserializeTime($this->time_start);
    }

    /**
     * @return array
     */
    public function getEndTime() {
        return $this->unserializeTime($this->time_end);
    }

    /**
     * Convert input data for other fields to database friendly type
     * 
     * @param array $data - post data 
     * @return string - serialized array
     */
    public function prepare_other_fields($data) {
        $result = array();
        foreach (array('include', 'exclude') as $key) {
            $arr = explode(',', Arr::get($data, $key, array()));    
            $result[$key] = array_filter(array_map('trim', $arr));
        }
        return serialize($result);
    }

    /**
     * Unserialize other fields (include/exclude words), return by key as string or array
     *
     * @param bool|FALSE|string $key 'exclude' or 'include'
     * @param bool $as_string - return data as comma-separated string
     * @return array|string
     */
    public function get_other_fields($key = FALSE, $as_string = FALSE) {
        $data = unserialize($this->other_fields);
        if ($key) {
            $data = $data[$key];
            return $as_string ? implode(', ', $data) : $data;
        }
        return $data;
    }

    /**
     * Set all users's keywords as deleted, except with ids
     *
     * @param int   $user_id
     * @param int   $profile_id
     * @param array $exclude_ids - do not set keywords as deleted with ids
     *
     * @return bool
     */
    public function set_deleted($user_id, $profile_id, $exclude_ids = array(0)) {

        return $this
            ->where(array(
                'user_id' => $user_id,
                'profile_id' => $profile_id
            ))->where_not_in('id', $exclude_ids)
            ->update(array(
                'is_deleted' => 1,
            ));
    }

    /**
     * If user has keywords
     * 
     * @param int $user_id
     * @return boolean
     */
    public function has_keywords($user_id) {
        return $this
            ->where('is_deleted', 0)
            ->where('user_id', $user_id)
            ->count() > 0;
    }

    /**
     * All keywords as array for dropdown
     * 
     * @param int $user_id
     * @return array
     */
    public function dropdown($user_id) {
        $keywords = $this
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('keyword', 'ASC')
            ->get()
            ->all_to_single_array('keyword');
        $initial = array('0' => 'All keywords ...');
        return $initial + $keywords;
    }
}
