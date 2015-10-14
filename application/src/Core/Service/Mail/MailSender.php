<?php
/**
 * Service for send diffrent mails
 *
 * @author ajorjik
 */
namespace Core\Service\Mail;

use Core\Service\Mail\Interfaces\MailInterface;

class MailSender
{

    protected $mailer;

    public function __construct(MailInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send mail to new registered user
     *
     * @param $params
     */
    public function sendRegistrationMail($params)
    {
        $user = $params['user'];
        if ($user->exists()) {

            return $this->mailer->sendMail(
                                           'Registration',
                                           array(
                                               'template' => 'registration',
                                               'content_type' =>'text/html',
                                               'data' => array('user' => $user),
                                           ),
                                           array($user->email => $user->username)
                                        );
        }
    }

    /**
     * Send mail when user status was changed by admin
     *
     * @param $params
     */
    public function sendAdminBlockMail($params)
    {
        $user = $params['user'];
        if ($user->exists()) {

            return $this->mailer->sendMail(
                                           'Change status',
                                           array(
                                               'template' => 'user_blocked',
                                               'content_type' =>'text/html',
                                               'data' => array('user' => $user),
                                           ),
                                           array($user->email => $user->username)
                                        );
        }

    }

    /**
     * Send mail when user's account was deleted by admin
     *
     * @param $params
     */
    public function sendUserDeleteMail($params)
    {
        if (!empty($params['user'])) {
            $user = $params['user'];
            return $this->mailer->sendMail(
                'Delete account',
                array(
                    'template' => 'user_deleted',
                    'content_type' =>'text/html',
                    'data' => array('user' => $user),
                ),
                array($user->email => $user->username)
            );
        }

    }

    /**
     * Send mail for change password
     *
     * @param $params
     */
    public function sendForgotPasswordMail($params)
    {
        if (!empty($params)) {
            extract($params);

            return $this->mailer->sendMail($subject, $body, $to);
        }
    }

    /**
     * Send invite for user
     *
     * @param $params
     * @return mixed
     */
    public function sendInviteMail($params)
    {
        if (!empty($params)) {
            extract($params);
            $subject = 'Invite';
            $body = array('template' => 'invite',
                          'content_type' => 'text/html',
                          'data' => $data,
                    );

            return $this->mailer->sendMail($subject, $body, $to);
        }
    }

    /**
     * Send invite for collaborator
     *
     * @param $params
     * @return mixed
     */
    public function sendInviteCollaboratorMail($params)
    {
        if (!empty($params)) {
            extract($params);
            $subject = 'Invite you to join my project';
            $body = array('template' => 'invite_collaborator',
                'content_type' => 'text/html',
                'data' => $data,
            );

            return $this->mailer->sendMail($subject, $body, $to);
        }
    }

    /**
     * Send notify email about succesfully payment
     *
     * @param $params
     * @return mixed
     */
    public function sendPaymentMail($params)
    {
        $subject = 'Succesfully payment';
        $body = array('template' => 'payment',
                      'content_type' => 'text/html',
                      'data' => $params );
        $to = $params['user']->email;

        return $this->mailer->sendMail($subject, $body, $to);
    }

    /**
     * Send special plan invite to user
     *
     * @param $params
     * @return mixed
     */
    public function sendSpecialInviteMail($params)
    {
        if (!empty($params)) {
            extract($params);
            $subject = 'Special subscription plan invitation';
            $body = array('template' => 'special_invite',
                'content_type' => 'text/html',
                'data' => $data,
            );

            return $this->mailer->sendMail($subject, $body, $to);
        }
    }
}