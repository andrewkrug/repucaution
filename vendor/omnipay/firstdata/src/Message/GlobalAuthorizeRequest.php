<?php

namespace Omnipay\FirstData\Message;

class GlobalAuthorizeRequest extends GlobalAbstractRequest
{
    protected $action = self::TRAN_PREAUTH;
}
