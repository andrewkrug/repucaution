<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_182Update_twitter_followers extends CI_Migration {

    private $_table = 'twitter_followers';

    public function up() {
        $fields = array(
            'start_follow_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'end_follow_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'start_follow_time');
        $this->dbforge->drop_column($this->_table, 'end_follow_time');
    }

}