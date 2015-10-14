<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_payment_status  extends CI_Migration {

    private $_table = 'payment_statuses';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'enabled' => array(
                'type' => 'TINYINT',
                'default' => 0,
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }
}