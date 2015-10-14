<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_200Clear_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $sql = "DELETE FROM `" . $this->db->dbprefix . $this->_table . "`";
        $this->db->query($sql);
    }

    public function down() {

    }

}