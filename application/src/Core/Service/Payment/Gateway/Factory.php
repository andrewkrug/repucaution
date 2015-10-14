<?php
/**
 * User: alkuk
 * Date: 02.06.14
 * Time: 16:39
 */

namespace Core\Service\Payment\Gateway;

use Omnipay\Omnipay;
use Payment_gateways;
use RuntimeException;

class Factory
{
    /**
     * Create gateway
     *
     * @param Payment_geteways $paymentGateway
     *
     * @return \Omnipay\Common\GatewayInterface
     * @throws \RuntimeException
     */
    public function createGateway(Payment_gateways $paymentGateway)
    {
        if (!$paymentGateway->status) {
            throw new RuntimeException(sprintf('Enable Payment gateway(%s) before use it.', $paymentGateway->name));
        }

        $factorySlug = $this->getFactoryGatewayName($paymentGateway->slug);

        if (!$factorySlug) {
            throw new RuntimeException(sprintf('Payment gateway(%s) is not available.', $paymentGateway->name));
        }

        return Omnipay::create($factorySlug);
    }

    /**
     * @param $slug
     *
     * @return null|string
     */
    protected function getFactoryGatewayName($slug)
    {
        switch ($slug) {
            case 'paypal_express':
                return 'PayPal_Express';
            case 'authorize.net_sim':
                return 'AuthorizeNet_SIM';
            case 'stripe':
                return 'Stripe';
        }

        return null;
    }
}
