<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_187Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $where = 'key = "max_daily_auto_follow_users_by_search"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_follow_users_by_search_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_unfollow_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "welcome_message_text_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_follow_instagram"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_send_welcome_message_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_retweet_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_twitter"';
        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_follow_twitter"';
        $this->db->delete($this->_table, $where);

        $data = array(
            array(
                'name' => 'Max daily count of automatically followed users by search',
                'key' => 'max_daily_auto_follow_users_by_search',
            ),
            array(
                'name' => 'Automatically follow users by search',
                'key' => 'auto_follow_users_by_search',
            ),
            array(
                'name' => 'Automatically unfollow those who unsubscribed from your account',
                'key' => 'auto_unfollow',
            ),
            array(
                'name' => 'Auto-send welcome message to new followers',
                'key' => 'auto_send_welcome_message',
            ),
            array(
                'name' => 'Welcome message text',
                'key' => 'welcome_message_text',
            ),
            array(
                'name' => 'Auto-follow new followers',
                'key' => 'auto_follow',
            ),
            array(
                'name' => 'Auto-retweet',
                'key' => 'auto_retweet',
            ),
            array(
                'name' => 'Auto-favourite',
                'key' => 'auto_favourite',
            )
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $data = array(
            array(
                'name' => 'Max daily count of automatically followed users by search',
                'key' => 'max_daily_auto_follow_users_by_search',
            ),
            array(
                'name' => 'Automatically follow users by search Twitter',
                'key' => 'auto_follow_users_by_search_twitter',
            ),
            array(
                'name' => 'Automatically unfollow those who unsubscribed from your account',
                'key' => 'auto_unfollow_twitter',
            ),
            array(
                'name' => 'Auto-send welcome message to new followers for twitter',
                'key' => 'auto_send_welcome_message_twitter',
            ),
            array(
                'name' => 'Welcome message text for twitter',
                'key' => 'welcome_message_text_twitter',
            ),
            array(
                'name' => 'Auto-follow new followers for instagram',
                'key' => 'auto_follow_instagram',
            ),
            array(
                'name' => 'Auto-follow new followers for Twitter',
                'key' => 'auto_follow_twitter',
            ),
            array(
                'name' => 'Auto-retweet for twitter',
                'key' => 'auto_retweet_twitter',
            ),
            array(
                'name' => 'Auto-favourite for twitter',
                'key' => 'auto_favourite_twitter',
            )
        );

        $this->db->insert_batch($this->_table, $data);
    }

}