<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_134Add_Fields_Field_To_Payment_Geteways extends CI_Migration {

    private $table = 'payment_geteways';

    public function up()
    {

        $fields = array(
            'fields' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => 500,
            )
        );

        $this->dbforge->add_column($this->table, $fields);

    }

    public function down(){}

}