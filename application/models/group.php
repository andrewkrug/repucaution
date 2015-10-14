<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 14:57
 */

class Group extends DataMapper
{
    var $has_many = array(
        'user' => array(
            'join_table' => 'users_groups'
        ),
    );
}
 