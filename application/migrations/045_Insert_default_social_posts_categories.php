<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Insert_default_social_posts_categories extends CI_Migration {

    private $_table = 'social_posts_categories';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `slug`) VALUES
                    ('Links', 'links'),
                    ('Questions', 'questions'),
                    ('Status Updates', 'status_updates'),
                    ('Quotes', 'quotes'),
                    ('Photos', 'photos'),
                    ('Videos', 'videos'),
                    ('Coupons / Promotions', 'promotions');";

        $this->db->query($sql);
    }

    public function down() {
        $this->db->query('TRUNCATE TABLE  `" . $this->db->dbprefix . $this->_table . "`');
    }

}