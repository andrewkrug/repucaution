<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_201Update_directories_users  extends CI_Migration {

    private $_table = 'directories_users';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);

        $sql = "DROP INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix .
            $this->_table . ";";
        $this->db->query($sql);

        $sql = "CREATE UNIQUE INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix
            . $this->_table . "(user_id ASC, directory_id ASC, profile_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');

        $sql = "DROP INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix .
            $this->_table . ";";
        $this->db->query($sql);

        $sql = "CREATE UNIQUE INDEX directories_users_user_id_directory_id_UNIQUE ON " . $this->db->dbprefix
            . $this->_table . "(user_id ASC, directory_id ASC);";
        $this->db->query($sql);
    }

}