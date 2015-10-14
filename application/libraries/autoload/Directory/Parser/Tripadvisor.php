<?php

class Directory_Parser_Tripadvisor extends Directory_Parser {

    //protected $review_date_format = 'U';
    protected $api_key;

    public function __construct(){
        parent::__construct();
        get_instance()->config->load('site_config', TRUE);
        // $api = get_instance()->config->item('google_app', 'site_config');
        // $this->api_key = $api['simple_api_key'];
        $this->api_key = Api_key::value('tripadvisor', 'token');
        if(empty($this->api_key)){
            throw new Exception('Setup tripadvisor api key.');
        }
    }

    public function get_reviews() {

        $this->is_url_set();

        $reviews = array();

        $content = $this->_request($this->url, true);
        $content = json_decode($content, true);

        if(empty($content['result']['reviews'])) {
            return $reviews;
        }


        foreach($content['result']['reviews'] as $_review) {
            $reviews[] = $this->parse_one_review($_review);
        }

        return $reviews;

    }

    protected function _get_uniq_id($element) {
        return $element['id'];
    }

    protected function _get_rank($element) {
        return $element['rating'];
    }

    protected function _get_posted_date($element) {
        $date = new DateTime($element['published']);
        return $date->getTimestamp();
    }

    protected function _get_text($element) {
        if(empty($element['text'])) {
            return null;
        }
        return $this->_prepare_text($element['text']);
    }

    protected function _get_author($element) {
        if(empty($element['user']['username'])) {
            return null;
        }
        return $this->_prepare_text($element['user']['username']);
    }

    public function valid_url($place_reference) {
         $url = $this->getDetailsUrl($place_reference);
         $content = $this->_request($url, true);
         $content = json_decode($content, true);

         return ( !empty($content['status']) && $content['status'] === 'OK' );
    }


    public function set_url($place_reference){
        $this->url = $this->getDetailsUrl($place_reference);
        return $this;
    }

    /**
     * Generate Google places details url
     *
     * @param $place_reference
     *
     * @return string
     */
    protected function getDetailsUrl($place_reference){
        $base_url = "http://api.tripadvisor.com/api/partner/2.0/location/$place_reference";
//        $base_url = "http://rcp-dev.ext.tripadvisor.com/api/partner/2.0/location/$place_reference";

        $args = array(
            'key' => $this->api_key.'123'
        );
        return $base_url.http_build_query($args);
    }

}