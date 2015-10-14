<?php

class Mention_keyword extends DataMapper {

    var $table = 'mention_keywords';

    var $created_field = 'created_at';
    var $updated_field = 'updated_at';
    
    var $has_one = array('user');
    var $has_many = array('mention');

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
     * @param          $profile_id
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
        $exact = Arr::get($data, 'exact', FALSE) ? TRUE : FALSE;
        $other_fields = $inst->prepare_other_fields($data);

        // create new if keyword is changed 
        if ($inst->exists()
            && (
                $inst->keyword != $keyword
                || $inst->exact != $exact
                || $inst->other_fields != $other_fields
            )
        ) {
            $inst = new self;
        }

        $inst->user_id = $user_id;
        $inst->profile_id = $profile_id;
        $inst->keyword = $keyword;
        $inst->exact = $exact;
        $inst->other_fields = $other_fields;

        return $inst;
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
     * @param string|FALSE $key 'exclude' or 'include'
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
     * @param       $profile_id
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
     * If user has requested keywords
     * 
     * @param int $user_id
     * @return boolean
     */
    public function has_requested($user_id) {
        return $this
            ->where('is_deleted', 0)
            ->where('user_id', $user_id)
            ->where('requested_at IS NOT NULL')
            ->count() > 0;
    }

    /**
     * All keywords as array for dropdown
     * 
     * @param int $user_id
     * @return array
     */
    public function dropdown($user_id, $profile_id) {
        $keywords = $this
            ->where(array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'is_deleted' => 0
            ))
            ->order_by('keyword', 'ASC')
            ->get()
            ->all_to_single_array('keyword');
        $initial = array('0' => lang('all_keywords'));
        return $initial + $keywords;
    }

    /**
     * Get all mention keywords for cron update
     *
     * @return array
     */
    public function get_for_cron_update()
    {
        $today = date('U', strtotime('today'));
        return $this
            ->where('is_deleted', 0)
            ->group_start()
                ->where('requested_at <', $today)
                ->or_where('requested_at IS NULL')
            ->group_end()
            ->get();
    }

    /**
     * Get socials that were already grabbed for keyword
     *
     * @return array
     */
    public function get_grabbed_socials_as_array()
    {
        $grabbed_socials = $this->grabbed_socials 
            ? explode(',', $this->grabbed_socials)
            : array();
        $grabbed_socials = array_filter(array_unique($grabbed_socials));
        return $grabbed_socials;
    }

    /**
     * Get keywords and their options for highlight
     *
     * @return array
     */
    public function get_for_highlight($user_id, $keyword_id = null)
    {
        // get all user keywords as array
        $all_keywords = $this
            ->where('user_id', $user_id)
            ->get()
            ->all_to_array(array('id', 'keyword', 'exact'));

        // if keyword id is passed search for that particular keyword
        // add return only it
        if ( ! is_null($keyword_id)) {
            $keywords = array();
            foreach ($all_keywords as $key => $value) {
                if ($value['id'] == $keyword_id) {
                    $all_keywords = array($value);
                    break;
                }
            }
        }

        return $all_keywords;
    }
}
