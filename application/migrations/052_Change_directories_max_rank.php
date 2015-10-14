<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_directories_max_rank  extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $fields = array(
            'stars' => array(
                'type' => 'int',
                'default' => 5,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
        $this->db->update($this->_table, array('stars' => 5));
    }

    public function down() {

    }

}