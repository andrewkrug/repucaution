<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_subscriptions extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'plan_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'start_date' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'end_date' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'profile_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
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