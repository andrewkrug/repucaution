<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_220Create_social_posts_cron extends CI_Migration {

    private $_table = 'social_posts_cron';

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
            'profile_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'timezone' => array(
                'type' => "VARCHAR",
                'constraint' => 255,
                'default' => 'Europe/London'
            ),
            'post_to_socials' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'time_in_utc' => array(
                'type' => 'BLOB',
                'null' => TRUE,
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