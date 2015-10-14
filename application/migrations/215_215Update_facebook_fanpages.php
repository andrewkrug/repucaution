<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_215Update_facebook_fanpages  extends CI_Migration {

    private $_table = 'facebook_fanpages';

    public function up() {
        $fields = array(
            'access_token_id' => array(
                'type' => 'INT',
                'unsigned' => true
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'profile_id');
    }

}