<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_149Add_directory_sayso  extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `weight`, `type`, `stars`) VALUES
                    
					('Sayso', 7, 'Sayso', 5);";

        $this->db->query($sql);
    }

    public function down() {

    }

}