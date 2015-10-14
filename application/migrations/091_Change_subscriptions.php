<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_subscriptions extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $add = array(
           'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ),
            'amount' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'type' => array(
                'type' => 'INT',
                'constraint' => '1',
                'null' => FALSE,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => FALSE,
            ),
            'subscription_id' => array(
                'type' => 'INT',
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
            
        );
        
        $this->dbforge->drop_column($this->_table, 'plan_id');
        $this->dbforge->drop_column($this->_table, 'start_date');
        $this->dbforge->drop_column($this->_table, 'end_date');
        $this->dbforge->drop_column($this->_table, 'profile_id');
        $this->dbforge->add_column($this->_table, $add);
        
       
    }

    public function down() {
        $add = array(
           'plan_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'start_date' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'end_date' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'profile_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            )
            
        );
        
        $this->dbforge->drop_column($this->_table, 'name');
        $this->dbforge->drop_column($this->_table, 'amount');
        $this->dbforge->drop_column($this->_table, 'type');
        $this->dbforge->drop_column($this->_table, 'status');
        $this->dbforge->drop_column($this->_table, 'subscription_id');
        $this->dbforge->add_column($this->_table, $add);
    }

}