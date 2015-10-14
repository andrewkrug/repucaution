<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_timezone extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'timezone' => array(
                'type' => "VARCHAR",
                'constraint' => 255,
                'default' => 'Europe/London'
            )
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}