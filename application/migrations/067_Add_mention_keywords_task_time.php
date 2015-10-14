<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_mention_keywords_task_time  extends CI_Migration {

    private $_table = 'mention_keywords';

    public function up() {

        $fields = array(
            'created_at' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
                'constraint' => 11,
            ),
            'updated_at' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
                'constraint' => 11,
            ),
            'requested_at' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
                'constraint' => 11,
            ),
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
        $columns = array('created_at', 'updated_at', 'requested_at', 'grabbed_at');
        foreach ($columns as $column) {
            $this->dbforge->drop_column($this->_table, $column);
        }
    }

}