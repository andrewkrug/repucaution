<?php
/**
 * User: Dred
 * Date: 27.02.13
 * Time: 14:13
 */
class Email_task extends CLI_controller{

    /**
     * Send an Email
     *
     * $options = array(
     *    'to' => string,
     *    'subject' => string,
     *    'body' => string,
     *    'from' => null|string|object  ( 'bla@bla.com' => 'Mike' )
     * )
     *
     * @param $options
     */
    public function send($options){

        extract($options);

        if(empty($from)){
            $from = null;
        }

        $sender = $this->get('core.mailer');
        $sender->sendMail($subject, $body, $to, $from);

    }


}