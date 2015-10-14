<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_payments extends CI_Migration {

    private $_table = 'payments';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'payment_id' => array(
                'type' => 'INT',
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
            'start' => array(
                'type' => 'DATE',
                'null' => FALSE,
            ),
            'end' => array(
                'type' => 'DATE',
                'null' => FALSE,
            ),
            'subscription_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
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