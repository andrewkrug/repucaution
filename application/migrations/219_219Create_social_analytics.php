<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_219Create_social_analytics extends CI_Migration {

    private $_table = 'social_analytics';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'access_token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'value' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50'
            ),
            'date' => array(
                'type' => 'DATE'
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