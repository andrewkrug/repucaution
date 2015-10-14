<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_api_keys_linkedin  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`social`, `key`, `name`) VALUES
                    
					('linkedin', 'appKey', 'App Key'),
					('linkedin', 'appSecret', 'App Secret');";

        $this->db->query($sql);
    }

    public function down() {

    }

}