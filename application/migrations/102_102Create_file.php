<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_102Create_file  extends CI_Migration {

    private $_table = 'file';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'path' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ),
            'fullname' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'ext' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'mime' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'size' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'created' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'updated' => array(
                'type' => 'INT',
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