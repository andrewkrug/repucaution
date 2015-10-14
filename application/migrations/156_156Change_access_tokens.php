<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_156Change_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $fields = array(
            'type' => array(
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