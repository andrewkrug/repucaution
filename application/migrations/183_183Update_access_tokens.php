<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_183Update_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $sql = "DROP INDEX access_tokens_type_user_id ON " . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

    public function down() {
        $sql = "CREATE UNIQUE INDEX access_tokens_type_user_id ON " . $this->db->dbprefix
            . $this->_table . "(type ASC, user_id ASC);";
        $this->db->query($sql);
    }

}