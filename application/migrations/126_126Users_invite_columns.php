<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_126Users_invite_columns extends CI_Migration {

    private $_table = 'users';

    public function up()
    {

        $fields = array(

            'invite_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
            'invite_time' => array(
                'type' => 'INT',
                'null' => true,
                'unsigned' => TRUE,
            ),
        );


        $this->dbforge->add_column($this->_table, $fields);

    }

    public function down(){}

}