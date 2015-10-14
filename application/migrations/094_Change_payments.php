<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_payments extends CI_Migration {

    private $_table = 'payments';

    public function up() {
        $add = array(
            'payment_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ),
            
        );
        
        $this->dbforge->modify_column($this->_table, $add);
    }

    public function down() {
        $add = array(
            'payment_id' => array(
                'type' => 'INT',
                'null' => FALSE,
                'unsigned' => TRUE,
            ),
            
        );
        
        $this->dbforge->modify_column($this->_table, $add);
    }

}