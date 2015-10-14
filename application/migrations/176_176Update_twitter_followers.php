<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_176Update_twitter_followers extends CI_Migration {

    private $_table = 'twitter_followers';

    public function up() {
        $fields = array(
            'unfollow_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'still_follow' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'unfollow_time');
        $this->dbforge->drop_column($this->_table, 'still_follow');
    }

}