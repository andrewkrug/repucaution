<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_123Plan_update_schema  extends CI_Migration {

    private $table = 'plans';

    public function up()
    {


        $this->dbforge->drop_table($this->table);

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ),
            'weight' => array(
                'type' => 'INT',
                'constraint' => '5',
            ),
            'deleted' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->table, TRUE);

    }

    public function down(){}

}