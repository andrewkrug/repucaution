<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_period_to_plans extends CI_Migration {

    private $_table = 'plans';

    public function up() {
        $add = array(
            'period' => array(
                'type' => 'INT',
                'unsigned' => TRUE,      
                'default' => 1
            )
        );

        $this->dbforge->add_column($this->_table, $add);
       
    }

    public function down() {
		$this->dbforge->drop_column($this->_table, 'period');
		
    }

}