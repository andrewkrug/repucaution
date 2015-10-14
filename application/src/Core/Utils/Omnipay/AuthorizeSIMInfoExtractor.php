<?php
/**
 * User: alkuk
 * Date: 04.06.14
 * Time: 18:41
 */

namespace Core\Utils\Omnipay;


class AuthorizeSIMInfoExtractor extends AbstractIntoExtractor
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
        return $this->response->getRequest()->getDeveloperMode();
    }
}
