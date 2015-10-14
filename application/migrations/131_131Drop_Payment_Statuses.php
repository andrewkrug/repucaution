<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_131Drop_Payment_Statuses extends CI_Migration {

    private $table = 'payment_statuses';

    public function up()
    {
        $this->dbforge->drop_table($this->table);
    }

    public function down(){}

}