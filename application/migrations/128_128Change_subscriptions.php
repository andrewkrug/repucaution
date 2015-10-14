<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_128Change_subscriptions extends CI_Migration {

    private $table = 'subscriptions';

    public function up()
    {

        $fields = array(
            'amount' => array(
                'type' => 'FLOAT',
                'null' => false,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->modify_column($this->table, $fields);

    }

    public function down(){}

}