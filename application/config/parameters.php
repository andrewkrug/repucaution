<?php

$config['filesystem.base.path'] = FCPATH;

$config['email.config'] = array(
    'from' => array(
        'email' => 'script@example.com',
        'name' => 'Repucaution - Social Media Management Software'
    ),
    'options' => array(

    ),
    'mail_transport' => array(
        'type' => '',
        'smtp_config' => array(
            'host' => '',
            'port' => '',
            'username' => '',
            'password' => ''
        )
    ),

    'templates_config' => array(
        'path' => 'templates/email',
        'layout' => 'layout',
    )
);

