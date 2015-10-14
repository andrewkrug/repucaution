<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_user_feeds  extends CI_Migration {

    private $_table = 'user_feeds';

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
            'title' => array(
                'type' => 'VARCHAR',    
                'constraint' => '100',
                'null' => TRUE,
            ),
            'link' => array(
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),              
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX user_feeds_link_user_id_UNIQUE ON " . $this->db->dbprefix . $this->_table 
            . "(link ASC, user_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX user_feeds_link_user_id_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}