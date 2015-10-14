<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_188Create_available_configs  extends CI_Migration {

    private $_table = 'available_configs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ),
            'config_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key(array('type', 'config_id'));
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}