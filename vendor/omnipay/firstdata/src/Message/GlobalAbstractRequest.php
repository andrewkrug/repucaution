<?php

namespace Omnipay\FirstData\Message;

/**
 * First Data Abstract Request
 */
abstract class GlobalAbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = 'v11';

    protected $liveEndpoint = 'https://api.globalgatewaye4.firstdata.com/transaction/';
    protected $testEndpoint = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/';

    /**
     * @var int - api transaction type
     */
    protected $transactionType = '00';
    /**
     * Transaction types
     */
    const TRAN_PURCHASE = '00';
    const TRAN_PREAUTH = '01';
    const TRAN_PREAUTHCOMPLETE = '02';
    const TRAN_FORCEDPOST = '03';
    const TRAN_REFUND = '04';
    const TRAN_PREAUTHONLY = '05';
    const TRAN_PAYPALORDER = '07';
    const TRAN_VOID = '13';
    const TRAN_TAGGEDPREAUTHCOMPLETE = '32';
    const TRAN_TAGGEDVOID = '33';
    const TRAN_TAGGEDREFUND = '34';
    const TRAN_CASHOUT = '83';
    const TRAN_ACTIVATION = '85';
    const TRAN_BALANCEINQUIRY = '86';
    const TRAN_RELOAD = '88';
    const TRAN_DEACTIVATION = '89';

    protected static $cardTypes = array(
        'visa' => 'Visa',
        'mastercard' => 'Mastercard',
        'discover' => 'Discover',
        'amex' => 'American Express',
        'diners_club' => 'Diners Club',
        'jcb' => 'JCB',
    );

    public function getGatewayid()
    {
        return $this->getParameter('gatewayid');
    }

    public function setGatewayID($value)
    {
        return $this->setParameter('gatewayid', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Set transaction type
     * @param int $transactionType
     * @return object
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }
    /**
     * Return transaction type
     * @return int
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    protected function getBaseData($method)
    {
        $data = array();
        $data['gateway_id'] = $this->getGatewayID();
        $data['password'] = $this->getPassword();
        $data['transaction_type'] = $this->getTransactionType();

        return $data;
    }

    protected function getHeaders()
    {
        return array(
            'Content-Type: application/json; charset=UTF-8;',
            'Accept: application/json'
        );
    }

    public function getAVSHash()
    {
        $parts = array();
        $parts[] = $this->getCard()->getAddress1();
        $parts[] = $this->getCard()->getPostcode();
        $parts[] = $this->getCard()->getCity();
        $parts[] = $this->getCard()->getState();
        $parts[] = $this->getCard()->getCountry();
        return implode('|', $parts);
    }

    public function getData()
    {
        $this->setTransactionType($this->action);
        $data = $this->getBaseData('DoDirectPayment');

        $this->validate('amount', 'card');

        $data['amount'] = $this->getAmount();
        $data['currency_code'] = $this->getCurrency();
        $data['reference_no'] = $this->getTransactionId();

        // add credit card details
        $data['credit_card_type'] = self::getCardType($this->getCard()->getBrand());
        $data['cc_number'] = $this->getCard()->getNumber();
        $data['cardholder_name'] = $this->getCard()->getName();
        $data['cc_expiry'] = $this->getCard()->getExpiryDate('my');
        $data['cc_verification_str2'] = $this->getCard()->getCvv();
        $data['cc_verification_str1'] = $this->getAVSHash();
        $data['cvd_presence_ind'] = 1;
        $data['cvd_code'] = $this->getCard()->getCvv();

        $data['client_ip'] = $this->getClientIp();
        $data['client_email'] = $this->getCard()->getEmail();
        $data['language'] = strtoupper($this->getCard()->getCountry());
        return $data;
    }

    public function sendData($data)
    {
        $client = $this->httpClient->post(
            $this->getEndpoint(),
            $this->getHeaders(),
            $data
        );
        $client->getCurlOptions()->set(CURLOPT_PORT, 443);
        $httpResponse = $client->send();
        return $this->createResponse($httpResponse->getBody());
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint.self::API_VERSION : $this->liveEndpoint.self::API_VERSION;
    }

    protected function createResponse($data)
    {
        return $this->response = new GlobalResponse($this, $data);
    }

    public static function getCardType($type)
    {
        if (isset(self::$cardTypes[$type])) {
            return self::$cardTypes[$type];
        }
        return $type;
    }
}
