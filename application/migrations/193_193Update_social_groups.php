<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_193Update_social_groups extends CI_Migration {

    private $_table = 'social_groups';

    public function up() {
        $fields = array(
            'user_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'user_id');
    }

}