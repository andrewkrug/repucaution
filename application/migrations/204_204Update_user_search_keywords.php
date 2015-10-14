<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_204Update_user_search_keywords extends CI_Migration {

    private $_table = 'user_search_keywords';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');
    }

}