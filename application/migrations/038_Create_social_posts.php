<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_social_posts extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'url' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'posting_type' => array(
                'type' => 'ENUM("immediate", "schedule")',
                'null' => TRUE
            ),
            'posted_to_facebook' => array(
                'type' => "bool",
                'default' => 0
            ),
            'posted_to_twitter' => array(
                'type' => "bool",
                'default' => 0
            ),
            'schedule_date' => array(
                'type' => 'INT',
                'null' => TRUE,
                'constraint' => 11
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