<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_125Plan_period extends CI_Migration {

    private $table = 'plans_period';

    public function up()
    {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'plan_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'period' => array(
                'type' => 'INT',
                'constraint' => '5',
                'null' => false,
            ),
            'qualifier' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ),
            'price' => array(
                'type' => 'INT',
                'null' => false,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->table, TRUE);

    }

    public function down(){}

}