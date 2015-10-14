<?php

namespace Omnipay\FirstData\Message;

class GlobalPurchaseRequest extends GlobalAbstractRequest
{
    protected $action = self::TRAN_PURCHASE;
}
