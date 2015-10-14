<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_185Update_user_configs extends CI_Migration {

    private $_table = 'user_configs';

    public function up() {
        $fields = array(
            'access_token_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'access_token_id');
    }

}