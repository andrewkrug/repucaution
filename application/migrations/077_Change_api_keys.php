<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {
        $fields = array(
            'social' => array(
                'type' => 'ENUM("facebook", "twitter", "google", "bitly", "foursquare")',
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
        
    }
}