<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_139Change_subscriptions extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $add = array(
            'plan_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'payment_transaction_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'start_date' => array(
                'type' => 'DATE',
                'null' => FALSE,
            ),
            'end_date' => array(
                'type' => 'DATE',
                'null' => FALSE,
            ),
            'additional' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            
        );
        
        $this->dbforge->drop_column($this->_table, 'name');
        $this->dbforge->drop_column($this->_table, 'amount');
        $this->dbforge->drop_column($this->_table, 'type');
        $this->dbforge->drop_column($this->_table, 'subscription_id');
        $this->dbforge->add_column($this->_table, $add);
        
       
    }

    public function down() {

    }

}