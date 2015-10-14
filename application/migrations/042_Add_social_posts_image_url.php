<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_image_url  extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'image_url' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => null,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}