<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_197Update_social_posts extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'post_to_socials' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            )
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'post_to_groups');
    }

}