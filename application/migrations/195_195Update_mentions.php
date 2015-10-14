<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_195Update_mentions  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {
        $fields = array(
            'access_token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'access_token_id');
    }

}