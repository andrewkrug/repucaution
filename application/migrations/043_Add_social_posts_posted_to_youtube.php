<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_posted_to_youtube extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'posted_to_youtube' => array(
                'type' => "bool",
                'default' => 0
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}