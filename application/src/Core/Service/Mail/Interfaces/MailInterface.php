<?php
 /**
  * Interface for mail service
  *
  * @author ajorjik
  */

namespace Core\Service\Mail\Interfaces;

interface MailInterface
{
    /**
     * Send mail
     *
     * @param mixed $body
     * @param mixed $to
     * @param mixed $subject
     * @param mixed $from
     */
    public function sendMail($subject, $body, $to, $from = null);

}