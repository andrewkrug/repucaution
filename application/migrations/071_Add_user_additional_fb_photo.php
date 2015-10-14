<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_user_additional_fb_photo extends CI_Migration {

    private $_table = 'user_additional';

    public function up() {

        $fields = array(
            'facebook_profile_photo' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => 256,
            ),
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $columns = array('facebook_profile_photo');
        foreach ($columns as $column) {
            $this->dbforge->drop_column($this->_table, $column);
        }
    }

}