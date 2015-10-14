<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_95Create_job_queue  extends CI_Migration {

    private $_table = 'job_queue';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'state' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            ),
            'started_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'execute_after' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'closed_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'command' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'collate' => 'utf_general_ci',
                'null' => FALSE,
            ),
            'args' => array(
                'type' => 'LONGTEXT',
                'collation' => 'utf_general_ci',
                'null' => FALSE,
            ),
            'thread' => array(
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' =>FALSE,
            ),
            'max_retries' => array(
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'retries' => array(
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'runtime' => array(
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'memory_usage' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'memory_usage_real' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
                
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}