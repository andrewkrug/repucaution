<?php
/**
 * Service for generating Swift_Messages
 *
 * @author ajorjik
 */
namespace Core\Service\Mail;

use Swift_Message;

class SwiftMailMessages
{
    /**
     * Create Swift_Message
     *
     * @param null|array $params
     * @return Swift_Message
     */
    public function create($params = null)
    {
        if ($params) {
            extract($params);
        }

        return Swift_Message::newInstance($subject, $body, $contentType, $charset)->setTo($to)->setFrom($from);
    }
}