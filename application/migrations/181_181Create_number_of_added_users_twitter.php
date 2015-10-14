<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_181Create_number_of_added_users_twitter  extends CI_Migration {

    private $_table = 'number_of_added_users_twitter';

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
                'unsigned' => TRUE,
            ),
            'count' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
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