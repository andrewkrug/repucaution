<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_222Create_social_posts_cron_by_days extends CI_Migration {

    private $_table = 'social_posts_cron_by_days';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'cron_day_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'social_post_cron_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
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