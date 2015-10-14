<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_mqerror  extends CI_Migration {

    private $_table = 'mq_error';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'body' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'errors' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'returns' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'created' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'changed' => array(
                'type' => 'INT',
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