<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_237Add_directories  extends CI_Migration {

    private $_table = 'directories';

    public function up() {

       $data = array(
            array(
                'name' => 'Tripadvisor',
                'weight' => 7,
                'type' => 'Tripadvisor',
                'stars' => 5,
                'status' => 1
            ),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->db->delete($this->_table, array('name' => 'Tripadvisor'));
    }

}