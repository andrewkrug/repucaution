<?php

/**
 * Class User_language
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $notification
 * @property bool   $show
 */
class User_notification extends DataMapper {

    const BULK_UPLOAD = 'bulk_upload';
    const WELCOME = 'welcome';

    var $table = 'user_notifications';

    var $has_one = array(
        // user
    );
    var $has_many = array();

    var $validation = array();

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    /**
     * @param $user_id
     * @param $notification_name
     *
     * @return bool
     */
    static public function needShowNotification($user_id, $notification_name) {
        $notification = new self();
        $notification->where([
            'user_id' => $user_id,
            'notification' => $notification_name
        ])->get(1);
        return $notification->exists() ? $notification->show : true;
    }

    static public function setNotification($user_id, $notification_name, $show = true) {
        $notification = new self();
        $notification->where([
            'user_id' => $user_id,
            'notification' => $notification_name
        ])->get(1);
        $notification->user_id = $user_id;
        $notification->notification = $notification_name;
        $notification->show = $show;
        return $notification->save();
    }
}