<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_218Update_rss_feeds_users  extends CI_Migration {

    private $_table = 'rss_feeds_users';

    public function up() {
        $fields = array(
            'last_check' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'last_check');
    }

}