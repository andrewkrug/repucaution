<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_189Update_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $fields = array(
            'name' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => '100',
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => '255',
            ),
            'image' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => '255',
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'name');
        $this->dbforge->drop_column($this->_table, 'username');
        $this->dbforge->drop_column($this->_table, 'image');
    }

}