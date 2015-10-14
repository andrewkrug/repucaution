<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_106change_video_file  extends CI_Migration {

    private $_table = 'video_file';

    public function up() {
        $this->dbforge->drop_column($this->_table, 'original_path');
    }

    public function down() {
        $fields = array(
            'original_path' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
        );
        $this->dbforge->add_column($this->_table, $fileds);
    }

}