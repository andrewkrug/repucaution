<?php

class Crm_directory_activity extends DataMapper {

    public static $socials = array(
        'facebook',
        'twitter',
        'instagram',
    );

    var $table = 'crm_directory_activity';

    var $created_field = 'grabbed_at';
    
    var $has_one = array('crm_directory');

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
     * Parse message links
     */
    public function parse_message_links() {
        return preg_replace(
            "/\b((http(s?):\/\/)|(www\.))([\w\.]+)([\/\w+\.]+)([\?\w+\.\=]+)([\&\w+\.\=]+)\b/i", 
            "<a href=\"http$3://$4$5$6$7$8\" target=\"_blank\">$2$4$5$6$7$8</a>", 
            $this->message
        );
    }

    /**
     * Update other field of activity
     *
     * @param $original_id
     * @param $key
     * @param string $operation
     * @return bool
     */
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
     * Get crm directories activities
     *
     * @param array $directories
     * @param null $limit
     * @param null $offset
     * @param null $social
     * @return DataMapper
     */
    public function getByDirectories($directories, $limit = null, $offset = null, $social = null)
    {
        if ($social) {
            $this->where('social', $social);
        }

        return $this->where_in('crm_directory_id', $directories)
                    ->order_by('created_at', 'desc')->get($limit, $offset);
    }


    
}