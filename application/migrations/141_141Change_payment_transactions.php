<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_141Change_payment_transactions extends CI_Migration
{

    private $table = 'payment_transactions';

    public function up()
    {

        $newFields = array(
            'updated' => array(
                'type' => 'INT',
                'null' => true,
                'unsigned' => true,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => false,
                'unsigned' => true,
                'constraint' => 1,
            ),
            'target' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ),
            'test_mode' => array(
                'type' => 'INT',
                'null' => false,
                'unsigned' => true,
                'constraint' => 1,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => false,
            ),
            'user_info' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ),
        );


        $this->dbforge->add_column($this->table, $newFields);
    }

    public function down()
    {
    }

}