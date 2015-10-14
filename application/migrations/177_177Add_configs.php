<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_177Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
            array(
                'name' => 'Automatically unfollow those who unsubscribed from your account',
                'key' => 'auto_unfollow_twitter',
            ),
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {
        $where = 'key = "auto_unfollow_twitter"';

        $this->db->delete($this->_table, $where);
    }

}