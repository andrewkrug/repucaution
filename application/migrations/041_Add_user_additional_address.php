<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_user_additional_address  extends CI_Migration {

    private $_table = 'user_additional';

    public function up() {
        $fields = array(
            'address' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'address_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}