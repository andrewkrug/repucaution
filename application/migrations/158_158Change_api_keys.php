<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_158Change_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {

        $fields = array(
            'social' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}