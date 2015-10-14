<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_146Drop_video_tables extends CI_Migration
{


    public function up()
    {

        $this->dbforge->drop_table('video');
        $this->dbforge->drop_table('video_categories');
        $this->dbforge->drop_table('video_file');
        $this->dbforge->drop_table('video_video_categories');
    }

    public function down()
    {
    }
}
