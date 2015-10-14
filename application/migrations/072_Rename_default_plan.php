<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Rename_default_plan extends CI_Migration {

    private $_table = 'plans';

    public function up() {
        $sql = "UPDATE `" . $this->db->dbprefix . $this->_table . "`
                SET `name` = 'Subscription Plan'
                WHERE `id` = 1
                ";

        $this->db->query($sql);
    }

    public function down() {
        $sql = "UPDATE `" . $this->db->dbprefix . $this->_table . "`
                SET `name` = 'Standart Plan'
                WHERE `id` = 1
                ";

        $this->db->query($sql);
    }

}