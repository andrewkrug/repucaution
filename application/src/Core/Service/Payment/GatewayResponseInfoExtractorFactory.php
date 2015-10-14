<?php
/**
 * User: alkuk
 * Date: 03.06.14
 * Time: 18:18
 */

namespace Core\Service\Payment;

use RuntimeException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Stripe\Message\Response as StripeResponse;
use Omnipay\PayPal\Message\Response as PayPalResponse;
use Omnipay\AuthorizeNet\Message\SIMCompleteAuthorizeResponse as AuthorizeSIMResponse;

class GatewayResponseInfoExtractorFactory
{
    public function create(ResponseInterface $response)
    {
        switch (true) {
            case $response instanceof StripeResponse:
                return new \Core\Utils\Omnipay\StripeInfoExtractor($response);
            case $response instanceof PayPalResponse:
                return new \Core\Utils\Omnipay\PayPalInfoExtractor($response);
            case $response instanceof AuthorizeSIMResponse:
                return new \Core\Utils\Omnipay\AuthorizeSIMInfoExtractor($response);
        }

        throw new RuntimeException(sprintf('Unsupported response type %s', get_class($response)));
    }
}
