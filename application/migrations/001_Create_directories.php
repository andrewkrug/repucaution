<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_directories  extends CI_Migration {

    private $_table = 'directories';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',    
                'constraint' => '100',
                'null' => TRUE,
            ),
            'weight' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX directories_name_UNIQUE ON " . $this->db->dbprefix . $this->_table . "(name ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX directories_name_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}