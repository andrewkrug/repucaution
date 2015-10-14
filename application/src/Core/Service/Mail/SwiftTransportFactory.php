<?php
/**
 * Swift transport factory
 *
 * @author ajorjik
 */
namespace Core\Service\Mail;

use Swift_MailTransport;
use Swift_SmtpTransport;

class SwiftTransportFactory
{
    public static function create($mailConfig)
    {
        $mailTransportConfig = $mailConfig['mail_transport'];
        switch ($mailTransportConfig['type']) {
            case 'smtp':
                $config = $mailTransportConfig['smtp_config'];
                $transport = Swift_SmtpTransport::newInstance($config['host'], $config['port'])
                                                                        ->setUsername($config['username'])
                                                                        ->setPassword($config['password']);
                break;

            default:
                $transport = Swift_MailTransport::newInstance();

        }

        return $transport;
    }


}