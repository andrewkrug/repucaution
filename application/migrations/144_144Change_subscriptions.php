<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_144Change_subscriptions extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $add = array(
            'created' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),

        );

        $this->dbforge->add_column($this->_table, $add);
        
       
    }

    public function down() {

    }

}