<?php

class Keyword extends DataMapper {

    var $table = 'keywords';
    

    var $has_one = array('user');
    var $has_many = array('keyword_rank');

    var $validation = array(
        'keyword' => array(
            'label' => 'Keyword',
            'rules' => array('trim', 'max_length' => 250),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * User not-deleted keywords
     *
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return Keyword
     */
    public function get_user_keywords($user_id, $profile_id) {
        return $this
            ->where(array(
                'is_deleted' => 0,
                'user_id' => $user_id,
                'profile_id' => $profile_id
            ))->get();
    }

    /**
     * Update user keywords
     *
     * - delete old keywords with rank
     * - add new keywords
     *
     * @param $names   (array) - array with new keywords ('banana', 'apple', 'pear')
     * @param $user_id (id)
     * @param $profile_id
     *
     * @return Keyword with new keywords
     */
    public function update_keywords($names, $user_id, $profile_id) {

        $names = array_filter($names);

        $names_to_add = $names;

        if ($this->exists()) {
            $current_names = $this->all_to_single_array('keyword');

            $names_to_remove = array_diff($current_names, $names);
            $names_to_remove_ids = array_keys($names_to_remove);
            if ( ! empty($names_to_remove_ids)) {
                self::inst()->where_in('id', $names_to_remove_ids)->update(array('is_deleted' => 1));
            }

            $names_to_add = array_diff($names, $current_names);
        }

        if ( ! empty($names_to_add)) {
            foreach($names_to_add as $name) {
                $keyword = new self;
                $keyword->user_id = $user_id;
                $keyword->profile_id = $profile_id;
                $keyword->keyword = substr($name, 0, 250);
                $keyword->save();
            }
        }

        return self::inst()->get_user_keywords($user_id, $profile_id);
    }

    /**
     * Validate post keywords for max length
     * 
     * @param $keywords (array) - ('lemon', 'apple', 'pear')
     * @return array - errors array
     */
    public static function validate_keywords($keywords) {

        $errors = array();

        foreach($keywords as $keyword_key => $keyword_value) {

            $keyword = new self;
            $keyword->keyword = $keyword_value;
            $keyword->validate();

            if ( ! $keyword->valid) {
                foreach($keyword->error->all as $err_key => $err_value) {
                    $errors[ $keyword_key ][ $err_key ] = $err_value; 
                }
            }
        }

        return $errors;
    }


    /**
     * Get keywords current rank and compare with first rank (first in selected period)
     *
     * @param        $user_id (int)
     * @param        $profile_id
     * @param        $date    (string) - date in 'Y-m-d' format
     * @param int    $max_value
     * @param string $max_message
     *
     * @return Keyword model, but with fields presented in SELECT
     */
    public function with_rank($user_id, $profile_id, $date = NULL, $max_value = 50, $max_message = 'n/a') {
        $keywords_table = $this->table;
        $keyword_rank_table = Keyword_rank::inst()->table;

        $sql = 
        "SELECT 
            kw.id AS id, 
            kw.keyword AS keyword, 
            kw.is_deleted AS is_deleted, 
            kw.user_id AS user_id, 
            last_rank.date AS date, 
            IF(
                ABS(last_rank.rank) >= {$max_value},
                '{$max_message}',
                last_rank.rank
            )  AS last_rank,
            first_rank.rank AS first_rank,
            (first_rank.rank - last_rank.rank) AS rank_change
        FROM (
            SELECT `keyword_id`, `rank`, `date` 
            FROM `{$keyword_rank_table}` kw_max_rank
            WHERE `date` = (
                SELECT MAX(`date`) FROM `{$keyword_rank_table}` WHERE `keyword_id` = kw_max_rank.`keyword_id`
            )
            GROUP BY `keyword_id`
        ) AS last_rank 
        INNER JOIN `{$keywords_table}` AS kw ON kw.id = last_rank.keyword_id
        INNER JOIN (
            SELECT `keyword_id`, `rank`, MIN(`date`) AS 'mindate'
            FROM `{$keyword_rank_table}` kw_min_rank
            WHERE `date` = (
                SELECT MIN(`date`) FROM `{$keyword_rank_table}` WHERE `keyword_id` = kw_min_rank.`keyword_id` "
                . ($date ? "AND `date` >= ? " : "") .
            ")
            GROUP BY `keyword_id`
        ) first_rank ON first_rank.keyword_id = kw.id
        WHERE user_id = ?
        AND profile_id = ?
        AND is_deleted = 0
        ORDER BY keyword ASC
        ";

        $binds = array();
        if ( $date ) {
            $binds[] = $date;
        }
        $binds[] = $user_id;
        $binds[] = $profile_id;

        return $this->query($sql, $binds);
    }

    /**
     * Get the date in mysql DATE format ('Y-m-d') of the first rank update
     * 
     * @param $user_id (int)
     * @return string (date as 'Y-m-d') or NULL if there is no any
     */
    public function first_rank_date($user_id, $profile_id) {
        $keyword_rank_table = Keyword_rank::inst()->table;
        return $this
            ->select_min("{$keyword_rank_table}.date")
            ->include_related('keyword_rank', array())
            ->where(array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'is_deleted' => 0,
                ))
            ->get()
            ->date;
    }

    /**
     * Check if user has working keywords
     *
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return bool
     */
    public function has_keywords($user_id, $profile_id) {
        return (bool) $this
            ->where(array(
                'is_deleted' => 0,
                'user_id' => $user_id,
                'profile_id' => $profile_id
            ))
            ->count();
    }

    /**
     * Return keyword average rank for each day (average for all user keywords for 1 day) in range
     * 
     * @param $user_id (int)
     * @param $from (string) - string for strtotime
     * @param $to (string) - string for strtotime
     * @param $group (bool) - if true - return single average result for all date range, if false - return value for each day in range
     * @return array - of arrays, each child array has "value" with average rank and "date" with date
     */
    public static function average_for_range($user_id, $from = '-1 month', $to = 'today', $group = TRUE) {

        $ci  = & get_instance();
        $ci->load->library('gls');
        $max_rank = $ci->gls->max_results();

        $kr = Keyword_rank::inst();
        $kr->select('date, AVG(rank) as value')
            ->where_related('keyword', 'user_id', $user_id)
            ->where_related('keyword', 'is_deleted', 0)
            ->where(array(
                    'date >=' => date('Y-m-d', strtotime($from)),
                    'date <=' => date('Y-m-d', strtotime($to)),
                    'rank < ' => $max_rank,
                ));
        if ( ! $group) {
            $kr->group_by('date')
            ->order_by('date ASC');
        }
        $result = $kr->get()->all_to_array(array('date', 'value'));
        return ( ! $group ) 
            ? $result 
            : ( isset($result[0]['value']) 
                ? $result[0]['value']
                : 0
            );
    }

}