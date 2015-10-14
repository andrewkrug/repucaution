<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_facebook_fanpages extends CI_Migration {

    private $_table = 'facebook_fanpages';

    public function up() {

        $fields = array(
            'fanpage_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}