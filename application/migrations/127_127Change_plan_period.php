<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_127Change_plan_period extends CI_Migration {

    private $table = 'plans_period';

    public function up()
    {

        $fields = array(
            'price' => array(
                'type' => 'FLOAT',
                'null' => false,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->modify_column($this->table, $fields);

    }

    public function down(){}

}