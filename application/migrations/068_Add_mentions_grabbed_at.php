<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_mentions_grabbed_at  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {

        $fields = array(
            'grabbed_at' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
                'constraint' => 11,
            ),
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $columns = array('grabbed_at');
        foreach ($columns as $column) {
            $this->dbforge->drop_column($this->_table, $column);
        }
    }

}