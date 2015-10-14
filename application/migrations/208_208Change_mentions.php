<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_208Change_mentions  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {
        $fields = array(
            'social' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}