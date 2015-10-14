<?php
/**
 * Class Directory_Parser_Yahoo_Local
 *
 * Example url:
 * @link http://local.yahoo.com/info-21341983-giovanni-s-pizzaria-sunnyvale;_ylt=ArLdfWL3JDdGpbK5ebxEh3KGNcIF;_ylv=3
 *
 * @version 0.1 (27.02.2013)
 */

class Directory_Parser_Foursquare extends Directory_Parser implements Directory_Interface_UserStorage{

    protected $review_date_format = 'm/d/y';

    protected $userStorage = null;

	public function request($url){
		
		return $this->_request($url);
	}
	
    public function get_reviews() {

		$this->is_url_set();
        

        $reviews = array();

        $url = $this->_prepare_url();
		
        $content = $this->_request($url);
		$content = json_decode($content, true);
		$rate = $content['response']['venue']['rating'];
		$visitors = $content['response']['venue']['stats']['usersCount'];
		$checkins = $content['response']['venue']['stats']['checkinsCount'];//var_dump($rate."</br>".$this->directory_user['additional'] );die;
		$this->userStorage = array('rate'=> $rate, 'visitors' => $visitors, 'checkins' => $checkins);
		//$this->update_data($data);
		
       //var_dump($content['response']['venue']);die;
		
		
		//second request for more tips(launch one time)
		if($this->exist_reviews()==0){
			$url_s = $this->_prepare_url(true);
			$content_s = $this->_request($url_s);
			$content_s = json_decode($content_s, true);
			$tips = $content_s['response']['tips']['items'];
			
		}else{
			$tips = $content['response']['venue']['tips']['groups'][0]['items'];
		}
		
        if(!is_array($tips)){
            return false;
        }
		
        foreach($tips as $element){
            if(empty($element['id'])){
                continue;
            }
			//$element['rate'] = $rate;
            $reviews[] = $this->parse_one_review($element);
        }


        return $reviews;

    }
	
	
	
	protected function setRequestBody($data){
		
	}
	
    protected function _get_uniq_id( $element){
       return sha1($element['id']);

    }

    protected function _get_rank( $element){
        if( empty($element['rate']) ){
            return null;
        }
        return $element['rate'];
    }

    protected function _get_posted_date( $element){
        if(empty($element['createdAt'])){
            return null;
        }

        return $element['createdAt'];
    }

    protected function _get_text( $element){
        return $element['text'];
    }
    protected function _get_author( $element){
        if( empty($element['user']['firstName'])){
            return null;
        }
		$result = $element['user']['firstName'];
		if(isset($element['user']['lastName'])){
			$result.=" ".$element['user']['lastName'];
		}
        return $result;
    }

    protected function _prepare_url($second = null){
        $venueId = $this->_get_venueid_from_url();
		$keys = Api_key::inst()->value('foursquare');
		$json_url = 'http://api.foursquare.com/v2/venues/'.$venueId;
         if($second){
			$json_url .= '/tips';
		}				
        $data = array(
            'sort'=>'recent',
            'client_id'=> $keys['client_id'],
            'client_secret'=> $keys['client_secret'],
			'limit' => 500,
            'v' => date('Ymd', now())
        );

        return $json_url.'?'.http_build_query($data);
    }

    protected function _get_venueid_from_url(){
        if(empty($this->url)){
            throw new Exception('Please configure url.');
        }

        $path = parse_url($this->url, PHP_URL_PATH);
		$parts = pathinfo($path);
        if(empty($parts['basename'])){
            throw new Exception('Please configure url.');
        }
        return (string) $parts['basename'];

    }

    public function valid_url($url) {
        $pattern = '/^(https|http):\/\/([a-z]*\.)?foursquare.com\/v\//';
		preg_match($pattern, $url, $match);
		if(empty($match[0])){
			return false;
		}
		
        $match_len = strlen($match[0]);

        $url = trim($url);

        if(strlen($url) <= $match_len) {
            return false;
        }
        $sub_url = substr($url, 0, $match_len);
        return ($match[0] === $sub_url);

    }
	
	
	public function getDataToStore()
    {
        return $this->userStorage;
    }
	
    public function findUrl(){
        return 'https://foursquare.com';
    }

}