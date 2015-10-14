<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_social_posts_categories extends CI_Migration {

    private $_table = 'social_posts_categories';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            )
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}