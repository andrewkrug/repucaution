<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_category_id extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'category_id' => array(
                'type' => "INT",
                'constraint' => 11,
                'default' => 1,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}