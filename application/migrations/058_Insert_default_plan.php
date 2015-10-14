<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Insert_default_plan extends CI_Migration {

    private $_table = 'plans';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `cost`) VALUES
                    ('Standard Plan', 25);";

        $this->db->query($sql);
    }

    public function down() {

    }

}