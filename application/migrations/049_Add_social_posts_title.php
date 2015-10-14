<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_title extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'title' => array(
                'type' => "VARCHAR",
                'constraint' => 255,
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}