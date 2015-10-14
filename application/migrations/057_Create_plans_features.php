<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_plans_features extends CI_Migration {

    private $_table = 'plans_features';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'plan_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'feature_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'count' => array(
                'type' => 'INT',
                'constraint' => 11,
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