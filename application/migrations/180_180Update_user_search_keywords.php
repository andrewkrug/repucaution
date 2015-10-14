<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_180Update_user_search_keywords extends CI_Migration {

    private $_table = 'user_search_keywords';

    public function up() {
        $fields = array(
            'max_id' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => '100',
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'max_id');
    }

}