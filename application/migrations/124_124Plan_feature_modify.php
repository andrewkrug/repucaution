<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_124Plan_feature_modify extends CI_Migration {

    private $table = 'plans_features';

    public function up()
    {
        $this->db->empty_table($this->table);

        $fields = array(
            'count' => array(
                'name' => 'value',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ),
        );

        $this->dbforge->modify_column($this->table, $fields);

        $fields = array(
            'weight' => array(
                'type' => 'INT',
                'constraint' => '5',
            ),
        );

        $this->dbforge->add_column($this->table, $fields);

    }

    public function down(){}

}