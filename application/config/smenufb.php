<?php

$config['smenufb']['dashboard'] = array(

    'dashboard' => array(
        '#title' => 'Dashboard',
        '#weight' => 0,
        '#li_class' => 'dashboard-link',
    ),
    'webradar' => array(
        '#title' => 'Web Radar',
        '#weight' => 2,
        '#icon_class' => 'icon-comment',
        '#class' => array('settings_link'),
        '#open' => true,
    ),
    'webradar/all' => array(
        '#title' => 'All mentions',
        '#weight' => 0,
    ),
    'webradar/twitter' => array(
        '#title' => 'Twitter',
        '#weight' => 2,
    ),
    'webradar/facebook' => array(
        '#title' => 'Facebook',
        '#weight' => 4,
    ),
    'webradar/google' => array(
        '#title' => 'Google',
        '#weight' => 6,
    ),
    'webradar/influencers' => array(
        '#title' => 'Influencers watch',
        '#weight' => 8,
    ),

    'reviews' => array(
        '#title' => 'Reviews',
        '#weight' => 4,
        '#icon_class' => 'fa fa-list',
        '#class' => array('settings_link'),
        '#open' => true,
    ),

    'rank' => array(
        '#title' => 'Google Rank',
        '#weight' => 6,
        '#icon_class' => 'icon-google-plus-sign',
        '#class' => array('settings_link'),
    ),

    'social' => array(
        '#title' => 'Social Media',
        '#weight' => 8,
        '#icon_class' => 'customer_icon-media',
        '#open' => true,
    ),
    'social/create' => array(
        '#title' => 'Create',
        '#weight' => 0,
    ),
    'social/scheduled' => array(
        '#title' => 'Scheduled Posts',
        '#weight' => 3,
    ),
    'social/activity' => array(
        '#title' => 'Social Activity',
        '#weight' => 2,
    ),
    'social/fbbuilder' => array(
        '#title' => 'FB page builder',
        '#weight' => 10,
    ),
    'social/reports' => array(
        '#title' => 'Social Reports',
        '#weight' => 5,
    ),
    'traffic' => array(
        '#title' => 'Website Traffic',
        '#weight' => 10,
        '#icon_class' => 'icon-bar-chart',
        '#class' => array('settings_link'),
    ),
    'videotrainings' => array(
        '#title' => 'Video Trainings',
        '#weight' => 12,
        '#icon_class' => 'icon-play-circle',
        '#class' => array('settings_link'),
    )


);

$config['smenufb']['settings'] = array(

    'settings/personal' => array(
        '#title' => 'Personal Settings',
        '#weight' => 0,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/directories' => array(
        '#title' => 'Directory Settings',
        '#weight' => 2,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/keywords' => array(
        '#title' => 'Keywords',
        '#weight' => 4,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/socialmedia' => array(
        '#title' => 'Social Media',
        '#weight' => 6,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/mention_keywords' => array(
        '#title' => 'Social Keywords',
        '#weight' => 7,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/analytics' => array(
        '#title' => 'Analytics',
        '#weight' => 8,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),

    'settings/rss' => array(
        '#title' => 'Rss',
        '#weight' => 10,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),
    'settings/subscriptions' => array(
        '#title' => 'Subscriptions',
        '#weight' => 12,
        '#ignore_children' => TRUE,
        '#class' => array('settings_link'),
    ),
);