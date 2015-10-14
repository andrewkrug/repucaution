<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_api_key_field  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {
        $fields = array(
            'social' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
    }

}