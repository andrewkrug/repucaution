<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_213Update_crm_directories extends CI_Migration {

    private $_table = 'crm_directories';

    public function up() {
        $sql = "UPDATE `{$this->db->dbprefix}{$this->_table}` SET `facebook_link`='' WHERE 1";
        $this->db->query($sql);
    }

    public function down() {

    }

}