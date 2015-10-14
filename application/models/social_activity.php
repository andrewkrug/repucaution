<?php

class Social_activity extends DataMapper {

    var $table = 'social_activity';

    var $created_field = 'grabbed_at';
    
    

    var $validation = array(
        'original_id' => array(
            'label' => 'Original id',
            'rules' => array('trim', 'max_length' => 100, 'unique'),
        ),
        'message' => array(
            'label' => 'Message',
            'rules' => array('trim', 'max_length' => 4000),
        ),
        'social' => array(
            'label' => 'Social network',
            'rules' => array('trim', 'valid_match' => array('twitter', 'facebook', 'instagram', 'linkedin')),
        ),
        'creator_id' => array(
            'label' => 'Creator id',
            'rules' => array('trim', 'max_length' => 100),
        ),
        'likes' => array(
            'label' => 'Likes',
            'rules' => array('trim', 'max_length' => 100),
        ),
        'creator_image_url' => array(
            'label' => 'Creator image url',
            'rules' => array('trim', 'max_length' => 255),
        ),
        'other_fields' => array(
            'label' => 'Other fields',
            'rules' => array('trim', 'max_length' => 2000),
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }
	
	public function add_row($data){
		foreach ($data as $k=>$v){
			$this->$k = $v;
		}
		$this->save();
		
		
		
	}

    /**
     * Get all available mentions by social and user id 
     * 
     * @param int $user_id
     * @param string $social ('facebook', 'twitter')
     * @return Mention
     */
    public function by_social($user_id, $social = 'facebook') {
        $social = ($social === 'twitter') ? $social : 'facebook';
        return $this->where('social', $social)
            ->where('user_id', $user_id)
            ->where_related('mention_keyword', 'is_deleted', 0);
    }

    /**
     * Return other field by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function other_field($key, $default = NULL) {
        $other_fields = unserialize($this->other_fields);
        return isset($other_fields[$key]) ? $other_fields[$key] : $default;
    }

    /**
     * 
     */
    public function parse_message_links() {
        return preg_replace(
            "/\b((http(s?):\/\/)|(www\.))([\w\.]+)([\/\w+\.]+)([\?\w+\.\=]+)([\&\w+\.\=]+)\b/i", 
            "<a href=\"http$3://$4$5$6$7$8\" target=\"_blank\">$2$4$5$6$7$8</a>", 
            $this->message
        );
    }

    public function update_other_field($original_id, $key, $operation = 'inc') {
        $this->where('original_id', $original_id)->get(1);
        if ($this->exists()) {
            $other_field = $this->other_field($key);
            $other_fields = unserialize($this->other_fields);

            switch ($operation) {
                case 'true':
                    $other_fields[$key] = true;
                    break;
                case 'false':
                    $other_fields[$key] = false;
                    break;
                case 'dec':
                    $other_fields[$key] = max(0, $other_field - 1);
                    break;
                case 'inc':
                default:
                    $other_fields[$key] = $other_field + 1;        
                    break;
            }

            $this->other_fields = serialize($other_fields);
            return $this->save();
        }
        return false;
    }

}