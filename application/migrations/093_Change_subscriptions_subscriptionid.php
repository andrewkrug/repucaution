<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_subscriptions_subscriptionid extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $add = array(
            'subscription_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ),
             
        );
        
        $this->dbforge->modify_column($this->_table, $add);
    }

    public function down() {
        $add = array(
            'subscription_id' => array(
                'type' => 'INT',
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
            
        );
        
        $this->dbforge->modify_column($this->_table, $add);
    }

}