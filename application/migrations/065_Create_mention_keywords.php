<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_mention_keywords  extends CI_Migration {

    private $_table = 'mention_keywords';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'keyword' => array(
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'is_deleted' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'exact' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'other_fields' => array(
                'type' => 'TEXT',
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