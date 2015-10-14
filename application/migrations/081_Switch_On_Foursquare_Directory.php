<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Switch_On_Foursquare_Directory extends CI_Migration
{

    private $_table = 'directories';

    public function up() {

        $directory = 'Foursquare';
        $newStatus = 1;

        $sql = "UPDATE `" . $this->db->dbprefix . $this->_table ."` SET `status` = ".$newStatus." WHERE `name` = '".$directory."'";

        $this->db->query($sql);
    }

    public function down() {

    }
}
