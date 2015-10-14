<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_keywords_is_deleted  extends CI_Migration {

    private $_table = 'keywords';

    public function up() {
        $fields = array(
            'is_deleted' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}