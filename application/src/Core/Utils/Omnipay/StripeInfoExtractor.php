<?php
/**
 * User: alkuk
 * Date: 03.06.14
 * Time: 18:14
 */

namespace Core\Utils\Omnipay;

class StripeInfoExtractor extends AbstractIntoExtractor
{
    /**
     * {@inheritdoc}
     */
    public function getPaymentId()
    {
        return $this->response->getTransactionReference();
    }

    /**
     * {@inheritdoc}
     */
    public function isTestMode()
    {
        return !$this->getFormResponseData('livemode', false);
    }
}
