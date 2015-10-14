<?php
/**
 * User: Dred
 * Date: 05.03.13
 * Time: 13:00
 */

class Directory_Parser_Google_Places extends Directory_Parser {

    //protected $review_date_format = 'U';
    protected $api_key;

    public function __construct(){
        parent::__construct();
        get_instance()->config->load('site_config', TRUE);
        // $api = get_instance()->config->item('google_app', 'site_config');
        // $this->api_key = $api['simple_api_key'];
        $this->api_key = Api_key::value('google', 'developer_key');
        if(empty($this->api_key)){
            throw new Exception('Setup google api key.');
        }
    }

    public function get_reviews() {

        $this->is_url_set();

        $reviews = array();

        $content = $this->_request($this->url);
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
        return sha1( serialize($element) );
    }

    protected function _get_rank($element) {
        if(empty($element['aspects'])) {
            return null;
        }
        $count = count($element['aspects']);
        if($count < 1){
            return null;
        }
        $rank = 0;
        foreach($element['aspects'] as $_rank) {
            $rank += $_rank['rating'];
        }
        $rank = floatval($rank / $count);
        return $rank +1;
    }

    protected function _get_posted_date($element) {
        if(!isset($element['time']) || !is_numeric($element['time'])) {
            return null;
        }
        return $element['time'];
    }

    protected function _get_text($element) {
        if(empty($element['text'])) {
            return null;
        }
        return $this->_prepare_text($element['text']);
    }

    protected function _get_author($element) {
        if(empty($element['author_name'])) {
            return null;
        }
        return $this->_prepare_text($element['author_name']);
    }

    public function valid_url($place_reference) {
         $url = $this->getDetailsUrl($place_reference);
         $content = $this->_request($url);
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
        $base_url = 'https://maps.googleapis.com/maps/api/place/details/json?';

        $args = array(
            'sensor' => 'false',
            'reference' => $place_reference,
            'key' => $this->api_key
        );
        return $base_url.http_build_query($args);
    }


    public function autocomplete($name){
        $link = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';

        $args = array(
            'sensor' => 'false',
            'language' => 'en',
            'input' => $name,
            'key' => $this->api_key,
            'types' => 'establishment'
        );

        $link .= http_build_query($args);

        $content = $this->_request($link);
        $content = json_decode($content, true);

        $data = array();

        if( empty($content['predictions']) ){
            return $data;
        }

        foreach($content['predictions'] as $_location) {
            $data[] = array(
                'label' => $_location['description'],
                'value' => $_location['reference']
            );
        }

        return $data;

    }

}