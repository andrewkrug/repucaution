<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_226Create_user_languages extends CI_Migration {

    private $_table = 'user_languages';

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
            'language' => array(
                'type' => 'VARCHAR',
                'constraint' => 2,
                'default' => 'en'
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}