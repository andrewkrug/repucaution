<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_155Move_directory_sayso  extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "DELETE FROM `" . $this->db->dbprefix . $this->_table . "` WHERE type = 'Sayso';";

        $this->db->query($sql);
    }

    public function down() {

    }

}