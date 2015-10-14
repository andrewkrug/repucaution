<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_directories_max_rank  extends CI_Migration {

    private $_table = 'directories';

    public function up() {
        $fields = array(
            'stars' => array(
                'type' => 'int',
                'default' => 0,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}