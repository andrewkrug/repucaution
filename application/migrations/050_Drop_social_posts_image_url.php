<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Drop_social_posts_image_url extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $this->dbforge->drop_column($this->_table, 'image_url');
    }

    public function down() {

    }

}