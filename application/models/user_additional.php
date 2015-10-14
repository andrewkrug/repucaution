<?php

class User_additional extends DataMapper {

    var $table = 'user_additional';    
    
    var $has_one = array(
        // user
    );

    var $has_many = array();

    var $validation = array(
        'rank_website' => array(
            'label' => 'Website',
            'rules' => array('trim', 'valid_domain', 'max_length' => 250),
        ),
        'address' => array(
            'label' => 'Address',
            'rules' => array('trim', 'max_length' => 250),
        ),
        'address_id' => array(
            'label' => 'Address id',
            'rules' => array('trim', 'max_length' => 250),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    public function get_by_user_and_profile($user_id, $profile_id) {
        return $this->where(array(
            'user_id' => $user_id,
            'profile_id' => $profile_id
        ))->get(1);
    }

    /**
     * Validate address (domain)
     * 
     * @param $address (string) 
     * @return string
     */
    public static function validate_address($post) {

        $inst = new self;
        $inst->address = Arr::get($post, 'address');
        $inst->address_id = Arr::get($post, 'address_id');
        if ($inst->address && ! $inst->address_id) {
            return  "{$inst->validation['address']['label']} should be selected from the autocomplete";
        }
        $inst->validate();

        return $inst->valid ? '' : $inst->error->string;
    }

    /**
     * Save new address for user
     *
     * @param $post
     * @param $user_id (int)
     * @param $profile_id
     *
     * @return User_additional
     */
    public function update_address($post, $user_id, $profile_id) {
        $this->address = Arr::get($post, 'address');
        $this->address_id = Arr::get($post, 'address_id');
        if ( ! $this->address) {
            $this->address = NULL;
            $this->address_id = NULL;
        }
        if ( ! $this->exists()) {
            $this->user_id = $user_id;
            $this->profile_id = $profile_id;
        }
        $this->skip_validation()->save();
        return $this;
    }

    /**
     * Check if domain is valid
     * 
     * @param $field (string) 
     * @return string(if false, error message), bool(true) - if success
     */
    public function _valid_domain($field) {
        $value = $this->{$field};
        if ( ! preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}(:[0-9]{1,5})?(\/.*‌​)?$/ix', $value)) {
            return 'Website "' . $value . '" is not valid.';
        }
        return TRUE;
    }

    /**
     * 
     */
    public function get_value($user_id, $key, $default = NULL) {
        $result = $this->get_by_user_id($user_id, 1);
        if ($result->exists() && isset($result->{$key})) {
            return $result->{$key};
        }
        return $default;
    }

    /**
     * 
     */
    public function set_value($user_id, $key, $value) {
        $this->get_by_user_id($user_id);
        $this->user_id = $user_id;
        $this->{$key} = $value;
        return $this->save();
    }

    /**
     * 
     */
    public function unset_value($user_id, $key) {
        $data = array();
        $data[$key] = NULL;
        return $this->where('user_id', $user_id)
            ->update($data);
    }
}