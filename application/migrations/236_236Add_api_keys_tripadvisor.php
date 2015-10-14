<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_236Add_api_keys_tripadvisor  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

       $data = array(
            array('social' => 'tripadvisor', 'key' => 'token', 'name' => 'Token'),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->delete($this->_table, array('social' => 'tripadvisor', 'key' => 'token'));
    }

}