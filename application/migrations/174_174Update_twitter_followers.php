<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_174Update_twitter_followers extends CI_Migration {

    private $_table = 'twitter_followers';

    public function up() {
        $fields = array(
            'need_follow' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'need_follow');
    }

}