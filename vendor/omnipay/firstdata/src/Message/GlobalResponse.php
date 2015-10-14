<?php

namespace Omnipay\FirstData\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * First Data Global Response
 */
class GlobalResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        parse_str($data, $this->data);
    }

    public function isSuccessful()
    {
        return ($this->data['transaction_approved'] == '1') ? true : false;
    }

    public function getTransactionReference()
    {
        return $this->data['authorization_num'];
    }

    public function getMessage()
    {
        return $this->data['exact_message'];
    }
    public function getCode()
    {
        return $this->data['exact_resp_code'];
    }
}
