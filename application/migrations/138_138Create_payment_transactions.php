<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_138Create_payment_transactions  extends CI_Migration {


    private $_table = 'payment_transactions';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'payment_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ),
            'user_id' =>array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'system' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ),
            'amount' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'created' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),

        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $this->dbforge->drop_table('payments');


    }

    public function down() {


    }

}