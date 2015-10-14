<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_120Add_back_code_users  extends CI_Migration {

    private $_table = 'users';

    public function up() {
        $add = array(
            'back_code' => array(
                'type' => 'varchar',
                'constraint' => 40,
                'null' => true
            )
        );

        $this->dbforge->add_column($this->_table, $add);

    }

    public function down() {


    }

}