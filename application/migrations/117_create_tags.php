<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_tags  extends CI_Migration
{

    public function up()
    {
        $this->db->query("CREATE TABLE `".$this->db->dbprefix ."tags` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `tag_name` varchar(100) NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `tag_name_UNIQUE` (`tag_name`)
                        )");

        $this->db->query("INSERT INTO `".$this->db->dbprefix ."tags`
                        (`tag_name`)
                        VALUES ('video'),('simple'),('coupon'),('contact'),('popular'),('themed');");
    }

    public function down()
    {
        $this->dbforge->drop_table('tags');
    }

}