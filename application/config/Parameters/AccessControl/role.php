<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 0:36
 */

$config['parameters.role.access.control.abilities'] = array(
    'superadmin' => array(
        'manage_admins'
    ),
    'admin' => array(
        'enter_admin_side'
    ),
    'managers' => array(
        'enter_manager_side'
    ),
    'members' => array(
        'enter_customer_side'
    ),
);