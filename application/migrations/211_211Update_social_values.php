<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_211Update_social_values extends CI_Migration {

    private $_table = 'social_values';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);

        $sql = "DROP INDEX social_values_user_id_date_type_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);

        $sql = "CREATE UNIQUE INDEX social_values_user_id_date_type_UNIQUE ON "
            . $this->db->dbprefix . $this->_table . "(user_id ASC, date ASC, type ASC, profile_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');

        $sql = "DROP INDEX social_values_user_id_date_type_UNIQUE ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);

        $sql = "CREATE UNIQUE INDEX social_values_user_id_date_type_UNIQUE ON "
            . $this->db->dbprefix . $this->_table . "(user_id ASC, date ASC, type ASC);";
        $this->db->query($sql);
    }

}