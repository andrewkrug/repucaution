<?php if ( ! defined('BASEPATH')) die('No direct script access alllowed');

class Migration_166Plan_update_schema  extends CI_Migration {

    private $table = 'plans';

    public function up()
    {
        $add = array(
            'special' => array(
                'type' => 'bool',
                'null' => FALSE,
                'default' => 0
            )
        );

        $this->dbforge->add_column($this->table, $add);

        $modify = array(
            'trial' => array(
                'name' => 'trial',
                'type' => 'bool',
                'null' => FALSE,
                'default' => 0
            )
        );

        $this->dbforge->modify_column($this->table, $modify);

    }

    public function down(){}

}