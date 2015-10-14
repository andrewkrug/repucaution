<?php
/**
 * User: alkuk
 * Date: 22.05.14
 * Time: 15:38
 */

namespace StackCI\Extension;

use Exception;

interface ExceptionCatcherInterface
{
    /**
     * @param Exception $e
     */
    public function setException(Exception $e);

    /**
     * Set environment
     *
     * @param string $environment
     */
    public function setEnvironment($environment);

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getHttpException();
}
