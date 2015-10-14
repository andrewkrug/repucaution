<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_119Add_superadmin_group  extends CI_Migration {

    private $_table = 'groups';

    public function up() {

        $query = "UPDATE ".$this->db->dbprefix.$this->_table." SET name='superadmin', description='Super Administrator' WHERE name='admin'";
        $this->db->query($query);
        $data = array(
            array(
                'id' => '4',
                'name' => 'admin',
                'description' => 'Administrator'
            )
        );
        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->query("DELETE FROM ".$this->db->dbprefix.$this->_table." WHERE name='admin'");
        $query = "UPDATE ".$this->db->dbprefix.$this->_table." SET name='admin', description='Administrator' WHERE name='superadmin'";
        $this->db->query($query);

    }

}