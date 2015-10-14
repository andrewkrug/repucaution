<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_104drop_video_video_files  extends CI_Migration {

    private $_table = 'video_video_files';

    public function up() {

        $this->dbforge->drop_table($this->_table);

    }

    public function down() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'video_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'file_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

}