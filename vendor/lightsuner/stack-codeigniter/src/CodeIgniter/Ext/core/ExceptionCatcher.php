<?php
/**
 * User: alkuk
 * Date: 22.05.14
 * Time: 15:40
 */

use StackCI\Extension\ExceptionCatcherInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CI_ExceptionCatcher implements ExceptionCatcherInterface
{
    /**
     * @var \Exception
     */
    protected $currentException;

    /**
     * @var string
     */
    protected $environment;
    /**
     * {@inheritdoc}
     */
    public function setException(Exception $e)
    {
        $this->currentException = $e;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpException()
    {
        $code = 500;
        $message = '';

        if (!$this->isProd()) {
            $message = $this->currentException->getMessage() . '<pre>' .
                $this->currentException->getTraceAsString() . '</pre>';
        }

        if ($this->currentException instanceof HttpException) {
            $code = $this->currentException->getStatusCode();
        }

        return new HttpException($code, $message, $this->currentException);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Check if environment is 'production'
     *
     * @return bool
     */
    protected function isProd()
    {
        return $this->environment == 'production';
    }

}
