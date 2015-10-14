<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_148Plan_update_schema  extends CI_Migration {

    private $table = 'plans';

    public function up()
    {
        $add = array(
            'trial' => array(
                'type' => 'INT',
                'null' => true
            )
        );

        $this->dbforge->add_column($this->table, $add);

    }

    public function down(){}

}