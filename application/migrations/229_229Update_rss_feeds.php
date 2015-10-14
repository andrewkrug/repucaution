<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_229Update_rss_feeds  extends CI_Migration {

    private $_table = 'rss_feeds';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'last_check' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');
        $this->dbforge->drop_column($this->_table, 'last_check');
    }

}