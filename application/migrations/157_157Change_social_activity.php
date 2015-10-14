<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_157Change_social_activity  extends CI_Migration {

    private $_table = 'social_activity';

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