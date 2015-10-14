<?php

class Activitioner {
    
    //CI instance
    protected $ci;
    
    //Socializer object
    protected $socializer;
    
    protected $user_id;
    

    /**
     * Set Activitioner object params
     *
     *@access public
     *@return Activitioner object
     */
    public function __construct($user_id = NULL) {
        $this->ci = &get_instance();
        $this->socializer = $this->ci->load->library('Socializer/socializer');

        $this->user_id = $user_id;
    }
    
    
    /**
     * Get Linkedin activities and add it to database
     *
     * @access public
     * @return void
     */
    public function getLinkedinUpdates(){
        $access_tokens = Access_token::getAllByTypeAndUserIdAsArray('linkedin', $this->user_id);
        foreach ($access_tokens as $access_token) {
            /* @var Socializer_Linkedin $linkedin */
            $linkedin = $this->socializer->factory('Linkedin', $this->user_id, $access_token);
            if($linkedin) $linkedin->getUpdates();
        }

    }
    

    /**
     * Return instance of Activitioner
     *
     * @access public
     * @return Activitioner object 
     */
    public static function factory($user_id) {
        return new self($user_id);
    }
}