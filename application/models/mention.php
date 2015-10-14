<?php

class Mention extends DataMapper {

    public static $socials = array(
        'facebook',
        'twitter',
        'google',
        'instagram'
    );

    var $table = 'mentions';

    var $created_field = 'grabbed_at';
    
    var $has_one = array(
        'user',
        'mention_keyword',
        'mention_twitter',
        'mention_facebook',
        'mention_google',
        'mention_instagram'
    );
    var $has_many = array();

    var $validation = array(
        'original_id' => array(
            'label' => 'Original id',
            'rules' => array('trim', 'unique', 'max_length' => 100),
        ),
        'message' => array(
            'label' => 'Message',
            'rules' => array('trim', 'max_length' => 4000),
        ),
        'social' => array(
            'label' => 'Social network',
            'rules' => array('trim'),
            // !!! social rules are added in __consturct
        ),
        'creator_id' => array(
            'label' => 'Creator id',
            'rules' => array('trim', 'max_length' => 100),
        ),
        'creator_name' => array(
            'label' => 'Creator name',
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
        // add socials to validation
        $this->validation['social']['rules']['valid_match'] = self::$socials;

        parent::__construct($id);
    }

    public static function inst($id = NULL) {
        return new self($id);
    }

    /**
     * Get all available mentions by social and user id 
     * 
     * @param int $user_id
     * @param string $social ('facebook', 'twitter')
     * @return Mention
     */
    public function by_social($user_id, $social = 'facebook') {
       // $social = ($social === 'twitter') ? $social : 'facebook';
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
    
    /**
     * Get filtered mentions
     *
     * @param array $filters
     * @return $this   
     */
    public function getByFilters($filters, $limit = null, $offset = null)
    {
        return $this->where($filters)->order_by('created_at', 'desc')
                                     ->limit($limit, $offset)
                                     ->get();
    }

    /**
     * Set other field
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function setOtherField($key, $value)
    {
        $other_fields = unserialize($this->other_fields);
        $other_fields[$key] = $value;
        $this->other_fields = serialize($other_fields);

        return $this->save();
    }
    
}