<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_217Insert_default_features extends CI_Migration {

    private $_table = 'features';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `slug`, `type`) VALUES
                    ('Twitter marketing tools', 'twitter_marketing_tools', 'bool');";

        $this->db->query($sql);
    }

    public function down() {

    }

}