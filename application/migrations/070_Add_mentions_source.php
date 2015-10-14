<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_mentions_source extends CI_Migration {

    private $_table = 'mentions';

    public function up() {

        $fields = array(
            'source' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => 50,
            ),
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $columns = array('source');
        foreach ($columns as $column) {
            $this->dbforge->drop_column($this->_table, $column);
        }
    }

}