<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_96Create_video_category  extends CI_Migration {

    private $_table = 'video_categories';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'parent_category' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
                
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}