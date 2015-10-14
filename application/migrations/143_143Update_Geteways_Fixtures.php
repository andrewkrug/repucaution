<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_143Update_Geteways_Fixtures extends CI_Migration {

    private $table = 'payment_gateways';

    public function up()
    {

        $update_payment_gateways = array(
            'authorize.net_sim' => array(
                'required_fields' => json_encode(array(
                    'apiLoginId' => array(
                        'label' => 'API Login ID',
                        'required' => true,
                    ),
                    'transactionKey' => array(
                        'label' => 'Transaction Key',
                        'required' => true,
                    ),
                    'hashSecret' => array(
                        'label' => 'MD5 Secret',
                        'description' => 'Setup MD5 secret in your Authorize.net account(Authorize.net -> (Account)Settings -> (Security Settings)MD5-Hash) and paste here the same value.'
                    )
                )),
            ),
            'paypal_express' => array(
                'required_fields' => json_encode(array(
                    'username' => array(
                        'label' => 'API Username',
                        'required' => true,
                    ),
                    'password' => array(
                        'label' => 'API Password',
                        'required' => true,
                    ),
                    'signature' => array(
                        'label' => 'Signature',
                        'required' => true,
                    ),
                )),
            ),
            'stripe' => array(
                'required_fields' => json_encode(array(
                    'apiKey' => array(
                        'label' => 'Secret Key',
                        'required' => true,
                    ),
                    'publishableApiKey' => array(
                        'label' => 'Publishable Key',
                        'required' => true,
                    ),
                )),
            ),
        );

        foreach ($update_payment_gateways as $slug=>$gateway) {
            $this->db->update($this->table, $gateway, array('slug' => $slug));
        }


    }

    public function down(){}

}