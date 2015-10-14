<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Drop_social_posts_posted_to_youtube extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $this->dbforge->drop_column($this->_table, 'posted_to_youtube');
    }

    public function down() {

    }

}