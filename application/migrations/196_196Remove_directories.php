<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_196Remove_directories extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "DELETE FROM `" . $this->db->dbprefix . $this->_table . "` WHERE name IN (
            'Merchant Circle', 'Citysearch', 'Yahoo Local', 'Insider Pages'
        )";

        $this->db->query($sql);
    }

    public function down() {

    }

}