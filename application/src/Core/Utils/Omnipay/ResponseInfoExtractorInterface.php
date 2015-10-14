<?php
/**
 * User: alkuk
 * Date: 03.06.14
 * Time: 18:02
 */

namespace Core\Utils\Omnipay;

use Omnipay\Common\Message\ResponseInterface;

interface ResponseInfoExtractorInterface
{
    public function __construct(ResponseInterface $response);

    /**
     * Get payment id from response
     *
     * @return string
     */
    public function getPaymentId();

    /**
     * Check is payment system in test mode
     *
     * @return bool
     */
    public function isTestMode();
}
