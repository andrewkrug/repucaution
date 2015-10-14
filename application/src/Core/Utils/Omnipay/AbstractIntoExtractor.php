<?php
/**
 * User: alkuk
 * Date: 03.06.14
 * Time: 18:07
 */

namespace Core\Utils\Omnipay;

use Omnipay\Common\Message\ResponseInterface;

abstract class AbstractIntoExtractor implements ResponseInfoExtractorInterface
{
    /**
     * @var \Omnipay\Common\Message\ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $responseData;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->responseData = $this->response->getData();
    }

    /**
     * Get value from responseData
     *
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    protected function getFormResponseData($key, $defaultValue = '')
    {
        if (!isset($this->responseData[$key])) {
            return $defaultValue;
        }

        return $this->responseData[$key];
    }
}
