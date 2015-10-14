<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_170Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
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

    public function down() {
        $where = 'key = "auto_retweet_twitter"';

        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_favourite_twitter"';

        $this->db->delete($this->_table, $where);
    }

}