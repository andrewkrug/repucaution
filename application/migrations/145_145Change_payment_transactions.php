<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_145Change_payment_transactions extends CI_Migration
{

    private $table = 'payment_transactions';

    public function up()
    {

        $fields = array(
            'system' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => 255,
            )
        );


        $this->dbforge->modify_column($this->table, $fields);
    }

    public function down()
    {
    }

}