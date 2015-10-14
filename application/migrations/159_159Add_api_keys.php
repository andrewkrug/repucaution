<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_159Add_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`social`, `key`, `name`) VALUES
                    
					('mailchimp', 'api_key', 'Api Key');";

        $this->db->query($sql);
    }

    public function down() {

    }

}