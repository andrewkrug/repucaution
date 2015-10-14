<?php

class Review extends DataMapper {

    CONST POSTEDFORMAT = 'Y-m-d';

    var $table = 'reviews';    
    
    var $has_one = array(
        'directory' => array(
            'class' => 'DM_Directory',
        )
    );

    public $has_many = array('reviews_notification');

    var $validation = array(
        'review_uniq' => array(
            'rules' => array(
                'trim',
                'xss_clean',
                'required',
                'unique_multi_pair' => array('user_id','directory_id')
            )
        ),
        'user_id' => array(
            'rules' => array(
                'required',
            )
        ),
        'directory_id' => array(
            'rules' => array(
                'required',
            )
        ),
        'text' => array(
            'rules' => array(
                'trim',
                'xss_clean',
                'required',
            )
        ),
    );

    /**
     * Unique Pair (pre-process)
     *
     * Checks if the value of a property, paired with another, is unique.
     * If the properties belongs to this object, we can ignore it.
     *
     * @ignore
     */
    protected function _unique_multi_pair($field, $other_field = array())
    {
       if(empty($this->{$field})){
           return TRUE;
       }

        $where = array($field => $this->{$field});

        foreach($other_field as $_ofield){
           if(empty( $this->{$_ofield} )){
               return TRUE;
           }
            $where[$_ofield] = $this->{$_ofield};
        }


            $query = $this->db->get_where($this->table, $where, 1, 0);

            if ($query->num_rows() > 0)
            {
                $row = $query->row();

                // If unique pair value does not belong to this object
                if ($this->id != $row->id)
                {
                    // Then it is not a unique pair
                    return FALSE;
                }
            }


        // No matches found so is unique
        return TRUE;
    }

    /**
     * Total reviews count by user and directory
     *
     * @param $user_id
     * @param $directory_id
     *
     * @return int
     */
    static public function count_by_user_dir($user_id, $directory_id){
        $obj = new self();
        return (int)$obj->where(array(
            'user_id' => $user_id,
            'directory_id' => $directory_id
        ))->count();
    }

    /**
     * Total reviews count by user, profile and directory
     *
     * @param $user_id
     * @param $directory_id
     * @param $profile_id
     *
     * @return int
     */
    static public function count_by_user_dir_and_profile($user_id, $directory_id, $profile_id){
        $obj = new self();
        return (int)$obj->where(array(
            'user_id' => $user_id,
            'directory_id' => $directory_id,
            'profile_id' => $profile_id
        ))->count();
    }

    /**
     * Help method - set "wheres" to query
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return $this
     */
    protected function generate_wheres($user_id, $profile_id, $directory_id = null, $options = array()){
        $this->where('user_id', $user_id);
        $this->where('profile_id', $profile_id);
        if(!empty($directory_id)){
            $this->where('directory_id',$directory_id );
        }

        if(!empty($options['date_from']) && !empty($options['date_to']) ){
            $this->where_between('posted_date', "'".$options['date_from']."'", "'".$options['date_to']."'");
        }
        if(isset($options['limit'])){
            $this->limit($options['limit']);
        }

        return $this;
    }


    /**
     * Get details by directory
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return array
     */
    public function details($user_id, $profile_id, $directory_id, $options = array()){

        $review = new self();

        $rank_details = $review->rank_details($user_id, $profile_id, $directory_id, $options);

        $review = new self();
        $review_limit = 3;
        $latest_reviews = $review->get_latest($user_id, $profile_id, $directory_id, array_merge($options, array('limit' =>$review_limit)));

        $review = new self();
        $rank = $review->rank($user_id, $profile_id, $directory_id, $options);

        /*$review = new self();
        $monthly_trending = $review->monthly_trending($user_id, $directory_id, $options);*/

        $review = new self();
        $count = $review->period_count($user_id, $profile_id, $directory_id, $options);
        $review = new self();
        $start_date = isset($options['date_from']) ? $options['date_from'] : null;
        $last_count = $review->last_period_count($user_id, $profile_id, $directory_id, $start_date);

        return array(
            'rank_details' => $rank_details,
            'latest_reviews' => $latest_reviews,
            'rank' => round($rank, 1),
            //'monthly_trending' => $monthly_trending,
            'count' => $count,
           // 'diff' => static::calc_difference($count, $last_count)
        );

    }

    /**
     * Get monthly_trending for last 30 days
     *
     * @param $user_id
     *
     * @param $profile_id
     *
     * @return array
     */
    public function last_month_trending($user_id, $profile_id){
        $now = time();

        $options = array(
            'date_to' => date(static::POSTEDFORMAT, $now),
            'date_from' => date(static::POSTEDFORMAT, strtotime('-30 days', $now))
        );

        return $this->monthly_trending($user_id, $profile_id, null, $options);
    }

    /**
     * Get daily reviews count
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return array
     */
    public function monthly_trending($user_id, $profile_id, $directory_id=null, $options = array()){
        $this->generate_wheres($user_id, $profile_id, $directory_id, $options);
        $this->group_by('posted_date')->select(array('posted_date'))->select_func('COUNT', '@id', 'count')->order_by('posted_date', 'ASC');
        $this->get();

        $data = array();

/*        if(isset($options['date_from']) && isset($options['date_to'])){
            $date_to = DateTime::createFromFormat(static::POSTEDFORMAT, $options['date_to']);
            $date_from = DateTime::createFromFormat(static::POSTEDFORMAT, $options['date_from']);
            if($date_to && $date_from){
                $date_to_stamp = $date_to->getTimestamp();
                $date_from_stamp = $date_from->getTimestamp();

                $current_date = $date_from_stamp;

                $plus_days = '+1 days';

                if($date_to_stamp > strtotime($plus_days, $current_date)){

                    for(;$current_date < $date_to_stamp; $current_date = strtotime($plus_days, $current_date) ){
                        $tmp_date = date(static::POSTEDFORMAT, $current_date);
                        $data[$tmp_date] = array($tmp_date,0);
                    }


                }
            }
        }*/


        foreach($this as $_data){
            $data[$_data->posted_date] = array($_data->posted_date, $_data->count);
        }


        return array_values($data);

    }


    /**
     * Array of count different types of rank
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return array
     */
    public function rank_details($user_id, $profile_id, $directory_id, $options = array()){
        return array(
            'positive' => $this->rank_positive($user_id, $profile_id, $directory_id, $options),
            'negative' => $this->rank_negative($user_id, $profile_id, $directory_id, $options),
            'neutral' => $this->rank_neutral($user_id, $profile_id, $directory_id, $options)
        );
    }

    /**
     * Generate query by rank options
     *
     * @param $options
     * @param string $position
     *
     * @return $this
     */
    protected function rankQuery($options, $position = 'neutral'){
        if(!isset($options['rank'])){
            return $this;
        }
        $rank = $options['rank'];

        switch($position){
            case 'positive':
                if(is_array($rank) ){
                    //$this->where('rank >', $rank['max']);
                    $this->where('rank >', $rank['min']);
                }elseif(is_numeric($rank)){
                    $this->where('rank >', $rank);
                }
                break;
            case 'negative':
                if(is_array($rank) ){
                    $this->where('rank <', $rank['min']);
                }elseif(is_numeric($rank)){
                    $this->where('rank <', $rank);
                }
                break;
            default:
                $this->group_start();
                if(is_array($rank) ){
                    //$this->where_between('rank', $rank['min'], $rank['max'] );
                    $this->where('rank', $rank['min']);
                }elseif(is_numeric($rank)){
                    $this->where('rank', $rank);
                }
                $this->or_where('rank', null);
                $this->group_end();
                break;
        }


        return $this;
    }

    /**
     * Get count of negative rank
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return int
     */
    public function rank_negative($user_id, $profile_id, $directory_id, $options = array()){
        return (int)$this->rankQuery($options, 'negative')->generate_wheres($user_id, $profile_id, $directory_id, $options)->count();
    }

    /**
     * Get count of positive rank
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return int
     */
    public function rank_positive($user_id, $profile_id, $directory_id, $options = array()){
        return (int)$this->rankQuery($options, 'positive')->generate_wheres($user_id, $profile_id, $directory_id, $options)->count();
    }

    /**
     * Get count of neutral rank
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return int
     */
    public function rank_neutral($user_id, $profile_id, $directory_id, $options = array()){
        return (int)$this->rankQuery($options, 'neutral')->generate_wheres($user_id, $profile_id, $directory_id, $options)->count();
    }

    /**
     * @param $user_id
     * @param $profile_id
     * @param $directory_id
     * @param $options
     *
     * @return mixed
     */
    public function get_latest($user_id, $profile_id, $directory_id, $options){
        return $this->generate_wheres($user_id, $profile_id, $directory_id, $options)->order_by('posted','DESC')->get();
    }

    /**
     * Get sum of rank
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return float
     */
    public function rank($user_id, $profile_id, $directory_id, $options = array()){
        return (float)$this->generate_wheres($user_id, $profile_id, $directory_id, $options)->select_avg('rank')->get()->rank;
    }

    /**
     * @param       $user_id
     * @param       $profile_id
     * @param       $directory_id
     * @param array $options
     *
     * @return int
     */
    public function period_count($user_id, $profile_id, $directory_id = null, $options = array()){
        return (int)$this->generate_wheres($user_id, $profile_id, $directory_id, $options)->count();
    }


    /**
     * Get rank from last 30 days
     *
     * @param      $user_id
     * @param      $profile_id
     * @param      $directory_id
     * @param null $to_date
     *
     * @return int
     */
    public function last_period_count($user_id, $profile_id, $directory_id = null, $to_date = null){
        if(empty($to_date)){
            $to_date = date(static::POSTEDFORMAT);
        }

        $from_date = DateTime::createFromFormat(static::POSTEDFORMAT, $to_date);
        $from_date->setTimestamp( strtotime('-30 days', $from_date->getTimestamp()) );

        $options = array(
            'date_from' => $from_date->format(static::POSTEDFORMAT),
            'date_to' => $to_date
        );

        return $this->period_count($user_id, $profile_id, $directory_id, $options);

    }

    /**
     * Calculate difference between data
     *
     * @param $now
     * @param $than
     *
     * @return float|int
     */
    static public function calc_difference($now, $than){
        if(!$now || !$than){
            return 100;
        }
        $diff = abs($now - $than);
        $percent = $diff/$now * 100;
        return round($percent, 2);
    }

    /**
     * Get paged data
     *
     * @param $directory_id
     * @param $user_id
     * @param $page
     * @param int $per_page
     */
    public function latest_reviews_paged($directory_id, $user_id, $page, $per_page = 10){
        if(!$page){
            $page = 1;
        }
        $this->where('directory_id', $directory_id)->where('user_id', $user_id)->order_by('posted', 'DESC')->get_paged($page,$per_page);
    }

    /**
     * Delete all reviews by user and directory
     *
     * @param $user_id
     * @param $directory_id
     */
    static public function deleteByUserDirectory($user_id, $directory_id) {
        $obj = new self();
        $sql = 'DELETE FROM '.$obj->table.' WHERE user_id = '.$user_id.' AND directory_id = '.$directory_id;
        $obj->db->query($sql);
    }

    /**
     * Generate query by stars
     *
     * @param int $stars
     *
     * @return array|int
     */
    static public function generateRantQueryByStars($stars){
        $neutral = $stars * 0.6;
        $rank_query = array(
            'min' => floor($neutral),
            'max' => ceil($neutral)
        );
        if(floatval( intval($neutral) ) === $neutral){
            $rank_query = intval($neutral);
        }
        return $rank_query;
    }

    /**
     * Get filtered reviews
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function getByFilters($filters, $limit, $offset){

        return $this->where($filters)->order_by('posted', 'DESC')->limit($limit, $offset)->get();

    }


}