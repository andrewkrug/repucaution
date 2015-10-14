<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Fill_directories extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `weight`, `type`) VALUES
                    ('Google Places', 0, 'Google_Places'),
                    ('Yelp', 1, 'Yelp'),
                    ('Merchant Circle', 2, 'Merchant_Circle'),
                    ('Citysearch', 3, 'Citysearch'),
                    ('Yahoo Local', 4, 'Yahoo_Local'),
                    ('Insider Pages', 5, 'Insider_Pages');";

        $this->db->query($sql);
    }

    public function down() {

    }

}