<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_facebook_fanpages_profile_id  extends CI_Migration {

    private $_table = 'facebook_fanpages';

    public function up() {
        $fields = array(
            'profile_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => null,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}