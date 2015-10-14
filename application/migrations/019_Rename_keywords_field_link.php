<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Rename_keywords_field_link  extends CI_Migration {

    private $_table = 'keywords';

    public function up() {
        $fields = array(
            'link' => array(
                'name' => 'keyword',
                'type' => 'VARCHAR',    
                'constraint' => '255',
                'null' => TRUE,
            ),
        );

        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {}

}