<?php
/**
 * Mailer based on swiftmailer
 *
 * @author ajorjik
 */
namespace Core\Service\Mail;

use Core\Service\Mail\Interfaces\MailInterface;
use Swift_Mailer;
use Template;

class MailerSwiftInflect implements MailInterface
{
    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $mailConfig;

    /**
     * @var SwiftMailMessages
     */
    protected $swiftMailMessages;


    public function __construct(Swift_Mailer $mailer, $mailConfig, Template $template, SwiftMailMessages $swiftMailMessages)
    {
        $this->mailer = $mailer;
        $this->mailConfig = $mailConfig;
        $this->template = $template;
        $this->swiftMailMessages = $swiftMailMessages;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function sendMail($subject, $body, $to, $from = null)
    {
        if (!$from) {
            $from = array($this->mailConfig['from']['email'] => $this->mailConfig['from']['name']);
        }
        $contentType = (!empty($body['content_type'])) ? $body['content_type'] : null;
        $charset = (!empty($body['charset'])) ? $body['charset'] : null;
        $message = $this->swiftMailMessages->create(array(
                                                          'subject' => $subject,
                                                          'body' => $this->renderBody($body),
                                                          'contentType' => $contentType,
                                                          'charset' => $charset,
                                                          'to' => $to,
                                                          'from' => $from));

        return $this->mailer->send($message);
    }

    /**
     * Render body
     *
     * @param array $body
     */
    protected function renderBody($body)
    {
        $templatesPath = $this->mailConfig['templates_config']['path'];
        $template = (!empty($body['template'])) ?
                    $this->template->block('body', $templatesPath.'/'.$body['template'].'.php', $body['data']) :
                    $body['body'];

        return $this->template->block(
                                      'layout',
                                      $templatesPath.'/'.$this->mailConfig['templates_config']['layout'].'.php',
                                      array('content' => $template)
                                      );
    }
}