<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_access_token_field  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $fields = array(
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
    }

}