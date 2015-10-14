<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_116Create_manager_users_users  extends CI_Migration {

    private $_table = 'manager_users_users';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'manager_user_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
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