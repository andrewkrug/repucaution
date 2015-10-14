<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_205Update_number_of_added_users_twitter  extends CI_Migration {

    private $_table = 'number_of_added_users_twitter';

    public function up() {
        $fields = array(
            'token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'token_id');
    }

}