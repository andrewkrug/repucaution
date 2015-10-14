<?php
/**
 * User: alkuk
 * Date: 22.05.14
 * Time: 16:11
 */

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Core\Exception\PlanAccessDeniedException;

class MY_ExceptionCatcher extends CI_ExceptionCatcher
{

    /**
     * {@inheritdoc}
     */
    public function getHttpException()
    {
        if ($this->currentException instanceof HttpException && $this->currentException->getStatusCode() == 404
        ) {
            return $this->currentException;
        }

        $code = 500;


        $message = 'Exception: ' . get_class($this->currentException) . '. Line: ' .
            $this->currentException->getLine() . '. File: ' . $this->currentException->getFile();

        $message .= $this->currentException->getMessage() .
            '<pre>' . $this->currentException->getTraceAsString() . '</pre>';


        log_message('SITE_ERROR', $message);

        if ($this->isProd()) {
            $message = '';
        }

        if ($this->currentException instanceof HttpException) {
            $code = $this->currentException->getStatusCode();
        }

        if ($this->currentException instanceof PlanAccessDeniedException) {
            $message = $this->renderTemplate('plan_error_403', array(
                'message' => $message,
                'heading' => 'Access denied! Upgrade your plan.'
            ));
        } elseif ($this->currentException instanceof AccessDeniedHttpException ||
            $this->currentException instanceof AccessDeniedException
        ) {
            $message = $this->renderTemplate('error_403', array(
                'message' => $message,
                'heading' => 'Access denied!'
            ));
        }

        return new HttpException($code, $message, $this->currentException);
    }

    /**
     * Render error template
     *
     * @param $template
     * @param array $params
     *
     * @return string
     */
    protected function renderTemplate($template, array $params = array())
    {
        $params = array_merge(array(
            'heading' => '',
            'message' => '',
        ), $params);

        ob_start();
        extract($params);
        include(APPPATH . 'errors/' . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

}
