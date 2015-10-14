<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_136Change_Payment_Geteway_Field_Name extends CI_Migration {

    private $table = 'payment_geteways';

    public function up()
    {
        $fields = array(
            'fields' => array(
                'name' => 'required_fields',
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => 500,
            )
        );

        $this->dbforge->modify_column($this->table, $fields);
    }

    public function down(){}

}