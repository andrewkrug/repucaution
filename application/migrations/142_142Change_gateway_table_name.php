<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_142Change_gateway_table_name extends CI_Migration
{

    public function up()
    {

        $this->dbforge->rename_table('payment_geteways', 'payment_gateways');
    }

    public function down()
    {
    }

}