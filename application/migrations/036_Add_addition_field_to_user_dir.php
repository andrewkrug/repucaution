<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_addition_field_to_user_dir  extends CI_Migration {

    private $_table = 'directories_users';

    public function up() {
        $fields = array(
            'additional' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => null,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}