<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_137Add_manager_code_users  extends CI_Migration {

    private $_table = 'users';

    public function up() {
        $modify = array(
            'back_code' => array(
                'name' => 'manager_code',
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => TRUE
            ),
        );
        $add = array(
            'manager_login_as' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'null' => TRUE,
            )
        );

        $this->dbforge->modify_column($this->_table, $modify);
        $this->dbforge->add_column($this->_table, $add);

    }

    public function down() {


    }

}