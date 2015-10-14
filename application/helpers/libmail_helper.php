<?php
/**
 * User: Dred
 * Date: 14.03.13
 * Time: 12:33
 */

/**
 * Send an Email
 *
 * @param $to
 * @param $subject
 * @param $body
 * @param string $from
 */
function libmail_send($to, $subject, $body, $from = null){
    $CI = get_instance();

    $CI->config->load('email', true);
    $email_config = $CI->config->item('email');

    $options = array();
    if(!empty($email_config['options'])){
        $options = $email_config['options'];
    }
    $CI->load->library('email', $options);

    if(empty($from) && !empty($email_config['from'])){
        $from =  $email_config['from'];
    }

    if(!empty($from)){
        if(is_string($from)){
            $CI->email->from($from);
        }elseif(is_array($from)){
            $CI->email->from($from['email'], $from['name']);
        }
    }

    if(!empty($options['mailtype']) && $options['mailtype'] = 'html'){
        $body = nl2br($body);
    }

    $CI->email->to($to);
    $CI->email->subject($subject);
    $CI->email->message($body);
    $CI->email->send();

}