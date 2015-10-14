<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_130Add_Payment_Geteways_Fixtures extends CI_Migration {

    private $table = 'payment_geteways';

    public function up()
    {

        $data = array(
            array(
                'name' => 'PayPal Express',
                'slug' => 'paypal_express',
                'status' => 0,
                'data' => null,
                'created_at' => time(),
                'updated_at' => time(),
            ),
            array(
                'name' => 'Authorize.Net SIM',
                'slug' => 'authorize.net_sim',
                'status' => 0,
                'data' => null,
                'created_at' => time(),
                'updated_at' => time(),
            ),
        );

        $this->db->insert_batch($this->table, $data);
    }

    public function down(){}

}