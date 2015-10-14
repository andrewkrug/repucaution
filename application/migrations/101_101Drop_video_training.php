<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_101Drop_video_training  extends CI_Migration {

    private $_table = 'video_trainings';

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
            'video_category_id' => array(
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