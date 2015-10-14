<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_user_dir_notify extends CI_Migration {

    private $_table = 'directories_users';

    public function up() {
        $fields = array(
            'notify' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

}