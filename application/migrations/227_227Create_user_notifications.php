<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_227Create_user_notifications extends CI_Migration {

    private $_table = 'user_notifications';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'notification' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'en'
            ),
            'show' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}