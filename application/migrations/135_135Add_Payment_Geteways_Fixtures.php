<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_135Add_Payment_Geteways_Fixtures extends CI_Migration {

    private $table = 'payment_geteways';

    public function up()
    {

        $update_payment_gateways = array(
            'paypal_express' => array(
                'fields' => json_encode(array(
                    'username' => 'API Username',
                    'password' => 'API Password',
                    'signature' => 'Signature',
                )),
            ),
            'authorize.net_sim' => array(
                'fields' => json_encode(array(
                    'apiLoginId' => 'API Login ID',
                    'transactionKey' => 'Transaction Key',
                )),
            ),
        );

        $new_payment_gateways = array(
            array(
                'name' => 'Stripe',
                'slug' => 'stripe',
                'status' => 0,
                'data' => null,
                'created_at' => time(),
                'updated_at' => time(),
                'fields' => json_encode(array(
                    'apiKey' => 'Secret Key',
                    'publishableApiKey' => 'Publishable Key',
                )),
            )
        );

        $this->db->insert_batch($this->table, $new_payment_gateways);

        foreach ($update_payment_gateways as $slug=>$gateway) {
            $this->db->update($this->table, $gateway, array('slug' => $slug));
        }


    }

    public function down(){}

}