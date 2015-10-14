<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_post_categories  extends CI_Migration {

    private $_table = 'post_categories';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',    
                'constraint' => '50',
                'null' => TRUE,
            ),              
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX post_categories_name_UNIQUE ON " . $this->db->dbprefix 
            . $this->_table . "(name ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX post_categories_name_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}