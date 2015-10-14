<?php
/**
 * User: alkuk
 * Date: 02.06.14
 * Time: 17:12
 */

namespace Core\Service\Payment;

use Core\Service\Payment\Gateway\Factory;
use Payment_gateways;
use Core\Service\Status\SystemStatus;

class PaymentSystemProvider
{
    /**
     * @var \Core\Service\Status\SystemStatus
     */
    protected $systemStatus;

    /**
     * @var \Core\Service\Payment\Gateway\Factory
     */
    protected $gatewayFactory;

    /**
     * @var \Omnipay\Common\GatewayInterface
     */
    protected $gateway;

    public function __construct(SystemStatus $systemStatus, Factory $gatewayFactory)
    {
        $this->systemStatus = $systemStatus;
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * Set payment gateway
     *
     * @param Payment_geteways $paymentGateway
     *
     * @return $this
     */
    public function setGateway(Payment_gateways $paymentGateway)
    {
        $this->gateway = $this->gatewayFactory->createGateway($paymentGateway);
        $parameters = $paymentGateway->getDecodedData();

        $isSandbox = $this->systemStatus->isSandboxPayment();

        $gatewayParams = array();

        switch ($paymentGateway->slug) {
            case 'paypal_express':
                $gatewayParams = array('testMode' => $isSandbox);
                break;
            case 'authorize.net_sim':
                $gatewayParams = array('testMode' => false, 'developerMode' => $isSandbox);
                break;
        }

        $parameters = array_merge($parameters, $gatewayParams);

        $this->gateway->initialize($parameters);

        return $this;
    }

    /**
     * This is a proxy for gateway->purchase
     *
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\Response
     */
    public function purchase(array $parameters = array())
    {
        return $this->gateway->purchase($parameters)->send();
    }

    /**
     * This is a proxy for gateway->completePurchase
     *
     * @param array $parameters
     *
     * @return \Omnipay\Common\Message\Response
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->gateway->completePurchase($parameters)->send();
    }
}
