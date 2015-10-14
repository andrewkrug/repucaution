<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_152Add_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`social`, `key`, `name`) VALUES
                    
					('instagram', 'in_client_id', 'Client Id'),
					('instagram', 'in_client_secret', 'Client Secret');";

        $this->db->query($sql);
    }

    public function down() {

    }

}