<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_api_keys_bitly  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $fields = array(
            'social' => array(
                'type' => 'ENUM("facebook", "twitter", "youtube", "google", "bitly")',
                'null' => TRUE,
            ),
        );

        $this->dbforge->modify_column($this->_table, $fields);

        $data = array(
            array('social' => 'bitly', 'key' => 'username', 'name' => 'Username'),
            array('social' => 'bitly', 'key' => 'apikey', 'name' => 'Api key'),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->delete($this->_table, array('social' => 'bitly'));
    }

}