<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_225Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
            array(
                'name' => 'Count of days before unfollow',
                'key' => 'days_before_unfollow',
            ),
            array(
                'name' => 'Min favourites count',
                'key' => 'auto_favourite_min_favourites_count',
            ),
            array(
                'name' => 'Max favourites count',
                'key' => 'auto_favourite_max_favourites_count',
            ),
            array(
                'name' => 'Min retweets count',
                'key' => 'auto_favourite_min_retweets_count',
            ),
            array(
                'name' => 'Max retweets count',
                'key' => 'auto_favourite_max_retweets_count',
            ),
            array(
                'name' => 'Min favourites count',
                'key' => 'auto_retweet_min_favourites_count',
            ),
            array(
                'name' => 'Max favourites count',
                'key' => 'auto_retweet_max_favourites_count',
            ),
            array(
                'name' => 'Min retweets count',
                'key' => 'auto_retweet_min_retweets_count',
            ),
            array(
                'name' => 'Max retweets count',
                'key' => 'auto_retweet_max_retweets_count',
            ),
            array(
                'name' => 'Age of account',
                'key' => 'age_of_account',
            ),
            array(
                'name' => 'Number of tweets',
                'key' => 'number_of_tweets',
            ),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $where = 'key = "days_before_unfollow"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_min_favourites_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_max_favourites_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_min_retweets_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_max_retweets_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_retweet_min_favourites_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_retweet_max_favourites_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_retweet_min_retweets_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_retweet_max_retweets_count"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "age_of_account"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "number_of_tweets"';
        $this->db->delete($this->_table, $where);
    }

}