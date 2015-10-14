<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_179Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
            array(
                'name' => 'Max daily count of automatically followed users by search',
                'key' => 'max_daily_auto_follow_users_by_search',
            ),
            array(
                'name' => 'Automatically follow users by search Twitter',
                'key' => 'auto_follow_users_by_search_twitter',
            ),
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {
        $where = 'key = "max_daily_auto_follow_users_by_search"';

        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_follow_users_by_search_twitter"';

        $this->db->delete($this->_table, $where);
    }

}