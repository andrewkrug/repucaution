<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_207Update_mentions  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');
    }

}