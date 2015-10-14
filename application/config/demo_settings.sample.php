<?php

$config['no_auth'] = true;
$config['disable_config_changes'] = true;
$config['forbidden_parts_of_site'] = array(
    'admin' => array(
        'redirect' => 'admin',
        'allowed_request_methods' => array('GET'),
        'classes' => array(
            'admin_users' => array(
                'sub_paths' => array(
                    'block',
                    'unblock',
                    'password',
                    'delete',
                )
            )
        )
    ),
    'settings' =>array(
        'allowed_request_methods' => array('GET'),
        'classes' => array(
            'socialmedia' => array(
                'redirect' => 'settings/socialmedia',
                'allowed_request_methods' => array('GET'),
                'allowed_methods' => array('index'),
            ),
            'user_search_keywords' => array(
                'redirect' => 'settings/user_search_keywords',
                'allowed_request_methods' => array('GET'),
                'allowed_methods' => array('index'),
            )
        ),
    )
);
