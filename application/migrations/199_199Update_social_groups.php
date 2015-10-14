<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_199Update_social_groups extends CI_Migration {

    private $_table = 'social_groups';

    public function up() {
        $fields = array(
            'is_active' => array(
                'type' => 'bool',
                'null' => FALSE,
                'default' => 0
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'is_active');
    }

}