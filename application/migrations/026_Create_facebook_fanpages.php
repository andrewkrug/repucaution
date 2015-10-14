<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_facebook_fanpages  extends CI_Migration {

    private $_table = 'facebook_fanpages';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            ),
            'fanpage_id' => array(
                'type' => 'INT',
                'null' => TRUE,
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}