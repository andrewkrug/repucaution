<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_directories_users  extends CI_Migration {

    private $_table = 'directories_users';

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
            'link' => array(
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),
            'directory_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix
            . $this->_table . "(user_id ASC, directory_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix . 
            $this->_table . ";";
        $this->db->query($sql);
    }

}