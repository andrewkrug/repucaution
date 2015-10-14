<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_232Remove_directories extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "DELETE FROM `" . $this->db->dbprefix . $this->_table . "` WHERE name = 'Yelp'";

        $this->db->query($sql);
    }

    public function down() {

    }

}