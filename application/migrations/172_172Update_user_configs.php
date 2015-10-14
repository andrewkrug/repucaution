<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_172Update_user_configs extends CI_Migration {

    private $_table = 'user_configs';

    public function up() {
        $fields = array(
            'value' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => TRUE
            )
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
        $fields = array(
            'value' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            )
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

}