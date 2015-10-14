<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_directory  extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `weight`, `type`, `stars`) VALUES
                    
					('Foursquare', 6, 'Foursquare', 10);";

        $this->db->query($sql);
    }

    public function down() {

    }

}