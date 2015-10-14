<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_reviews  extends CI_Migration {

    private $_table = 'reviews';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'created' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'posted' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'title' => array(
                'type' => 'VARCHAR',    
                'constraint' => '100',
                'null' => TRUE,
            ),
            'text' => array(
                'type' => 'TEXT',    
                'null' => TRUE,
            ),
            'author' => array(
                'type' => 'VARCHAR',    
                'constraint' => '100',
                'null' => TRUE,
            ),
            'rank' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'directory_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'post_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
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