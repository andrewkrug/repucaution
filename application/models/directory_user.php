<?php

class Directory_User extends DataMapper {

    public $table = 'directories_users';

    var $has_one = array(
        'directory' => array(
            'class' => 'DM_Directory',
        )
    );

    var $has_many = array();

    var $validation = array();

    /**
     * Get all active rows
     *
     * @return Directory_User
     */
    static public function get_all() {
        $obj = new self();
        return $obj->where_related('directory', 'status', 1)->get_iterated();
    }

    /**
     * Get one users directory
     *
     * @param $user_id
     * @param $directory_id
     * @param $profile_id
     *
     * @return Directory_User
     */
    static public function get_user_dir($user_id, $directory_id, $profile_id) {
        $obj = new self();
        return $obj
            ->where_related('directory', 'status', 1)
            ->where_related('directory', 'id', $directory_id)
            ->where(array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
            ))->limit(1)
            ->include_related('directory', '*', TRUE, TRUE)
            ->get();
    }

    /**
     * Get all user's directory settings
     *
     * @param $uid
     *
     * @return Directory_User
     */
    static public function get_by_user($uid) {
        $obj = new self();
        return $obj->where('user_id', $uid)->get();
    }

    /**
     * Get all user's directory settings by profile
     *
     * @param $uid
     *
     * @param $profile_id
     *
     * @return Directory_User
     */
    static public function get_by_user_and_profile($uid, $profile_id) {
        $obj = new self();
        return $obj->where(array(
            'user_id' => $uid,
            'profile_id' => $profile_id
        ))->get();
    }

    /**
     * Convert all object to array ( 'directory_id' => model array  )
     *
     * @return array
     */
    public function to_dir_array() {
        $array = array();
        foreach($this as $_model) {
            if(empty($_model->directory_id)) {
                continue;
            }
            $array[$_model->directory_id] = $_model->to_array();
        }

        return $array;
    }

    /**
     * Update user's directories
     *
     * @param       $user_id
     * @param       $profile_id
     * @param       $directories_ids
     * @param array $additions
     *
     * @return array
     */
    static public function update_user_dir($user_id, $profile_id, $directories_ids, $additions = array()) {
        $errors = array();
        $obj = new self();
        $table = $obj->table;

        $obj = new self();

        $old_options = array();

        foreach($obj->where(array(
            'user_id' => $user_id,
            'profile_id' => $profile_id
        ))->get() as $old_option) {
            $old_options[$old_option->directory_id] = $old_option->to_array();
        }

        $sql = "DELETE FROM $table WHERE user_id = $user_id AND profile_id = $profile_id";
        $obj->db->query($sql);

        if(empty($directories_ids)) {
            return;
        }

        $notify = empty($additions['notify']) ? 0 : 1;

        foreach($directories_ids as $dir_id => $link) {

            $directory = new DM_Directory($dir_id);
            if(!$directory->exists()) {
                $errors[] = 'Directory #' . $dir_id . '(' . $link . ') doesn\'t exist';
                continue;
            }

            try {
                $directory_parcer = Directory_Parser::factory($directory->type);
            }
            catch(Exception $e) {
                $errors[] = 'Directory\'s #' . $directory->id . '(' . $directory->name . ') parser doesn\'t configured';
                continue;
            }

            if(!$directory_parcer->valid_url($link)) {
                $errors[] = 'Invalid url for directory ' . $directory->name;
                continue;
            }

            if(empty($old_options[$dir_id]) || ($link !== $old_options[$dir_id]['link'])) {
				
                Review::deleteByUserDirectory($user_id, $dir_id);
            }

            $new_settings = new self();
            $new_settings->from_array(array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'link' => $link,
                'directory_id' => $dir_id,
                'notify' => $notify
            ));

            $new_settings->save();
        }


        return $errors;
    }


    /**
     * Check is any of collection has "notify"
     *
     * @return bool
     */
    public function isNotified() {

        $notify = false;

        foreach($this as $_model) {
            if($_model->notify) {
                $notify = true;
                break;
            }
        }

        return $notify;
    }

    /**
     * Setter for additional field
     *
     * @param $data
     *
     * @return $this
     */
    public function setAdditional($data)
    {
        $this->additional = serialize($data);

        return $this;
    }

}