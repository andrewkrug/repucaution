<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

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
            'token1' => array(
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),
            'token2' => array(
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),
            'instance_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'data' => array(
                'type' => 'BLOB',    
                'null' => TRUE,
            ),              
            'type' => array(
                'type' => 'ENUM("facebook", "twitter", "youtube", "google")',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX access_tokens_type_user_id ON " . $this->db->dbprefix 
            . $this->_table . "(type ASC, user_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX access_tokens_type_user_id ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}