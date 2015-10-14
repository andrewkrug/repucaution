<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_221Create_cron_days extends CI_Migration {

    private $_table = 'cron_days';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'day' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $data = array(
            array(
                'day' => 'Monday'
            ),
            array(
                'day' => 'Tuesday'
            ),
            array(
                'day' => 'Wednesday'
            ),
            array(
                'day' => 'Thursday'
            ),
            array(
                'day' => 'Friday'
            ),
            array(
                'day' => 'Saturday'
            ),
            array(
                'day' => 'Sunday'
            ),
        );

        $this->db->insert_batch($this->_table, $data);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}