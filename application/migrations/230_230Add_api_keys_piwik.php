<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_230Add_api_keys_piwik  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

       $data = array(
            array('social' => 'piwik', 'key' => 'domain', 'name' => 'Domain'),
            array('social' => 'piwik', 'key' => 'site_id', 'name' => 'Site id'),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->delete($this->_table, array('social' => 'piwik'));
    }

}