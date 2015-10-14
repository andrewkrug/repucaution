<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_228Update_rss_feeds_users  extends CI_Migration {

    private $_table = 'rss_feeds_users';

    public function up() {
        $this->dbforge->drop_column($this->_table, 'profile_id');
        $this->dbforge->drop_column($this->_table, 'last_check');
    }

    public function down() {
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

}