<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_directories extends CI_Migration {

    private $_table = 'directories';

    public function up() {

        $fields = array(
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}