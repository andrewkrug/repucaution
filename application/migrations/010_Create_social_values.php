<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_social_values  extends CI_Migration {

    private $_table = 'social_values';

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
            'value' => array(
                'type' => 'INT',    
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',    
                'null' => TRUE,
            ),
            'type' => array(
                'type' => 'ENUM("facebook", "twitter", "instagram")',    
                'null' => TRUE,
            ),              
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX social_values_user_id_date_type_UNIQUE ON " 
            . $this->db->dbprefix . $this->_table . "(user_id ASC, date ASC, type ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX social_values_user_id_date_type_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}