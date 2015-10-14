<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_114Change_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $sql = "DELETE FROM ".$this->db->dbprefix .$this->_table." WHERE social IN ('instagram', 'tumblr')";
        $this->db->query($sql);
    }

    public function down() {

    }

}