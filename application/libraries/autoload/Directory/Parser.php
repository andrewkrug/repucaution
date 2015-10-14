<?php
/**
 * User: Dred
 * Date: 22.02.13
 * Time: 15:37
 */
require_once __DIR__.DIRECTORY_SEPARATOR.'simple_html_dom.php';

abstract class Directory_Parser {

    protected $url;
	protected $directory_user;
    protected $user_agent_list = array(
        //chrome
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.60 Safari/537.17',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1309.0 Safari/537.17',
        'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.15 (KHTML, like Gecko) Chrome/24.0.1295.0 Safari/537.15',
        'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.14 (KHTML, like Gecko) Chrome/24.0.1292.0 Safari/537.14',
        //firefox
        'Mozilla/6.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1',
        'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1',
        'Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1',
        'Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20120716 Firefox/15.0a2',
        //safari
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10',
        'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3'
    );
	
	
    protected $curl_timeout = 50;
    protected $curl_roxies = array();
    protected $review_date_format = 'Y-m-d';

    protected function __construct(){}

    protected function _get_user_agent(){
        return array_rand($this->user_agent_list, 1);
    }
    protected function _get_proxy(){
        return array_rand($this->curl_roxies, 1);
    }

    protected function _request($url, $post=false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($this->curl_roxies)){

            $proxy = $this->curl_roxies[$this->_get_proxy()];
            $parts = explode(':', $proxy);
            curl_setopt($ch, CURLOPT_PROXY, $parts[0]);
            if (isset($parts[1])) {
                curl_setopt($ch, CURLOPT_PROXYPORT, $parts[1]);
            }
            
        }
        if($post) {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_get_user_agent() );
        $content = trim(curl_exec($ch));
		//var_dump(curl_error($ch));
		//var_dump($content);
		//var_dump(curl_getinfo($ch));
		//die;
        curl_close($ch);
        return $content;

    }

    protected function _timestamp_from_date($date){
        $date = DateTime::createFromFormat($this->review_date_format, $date);
        if(!is_object($date)){
            return null;
        }
        return $date->getTimestamp();
    }

    protected function parse_one_review($element){
        return array(
            'posted' => $this->_get_posted_date($element),
            'text' => $this->_get_text($element),
            'author' => $this->_get_author($element),
            'rank' => $this->_get_rank($element),
            'review_uniq' => $this->_get_uniq_id($element),
        );
    }

    protected function _prepare_text($text){
        $text = str_replace('<br>',"\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text);
        $text = trim($text);
        return $text;
    }

    public abstract function get_reviews();
	
    protected abstract function _get_posted_date($element);
    protected abstract function _get_text($element);
    protected abstract function _get_author($element);
    protected abstract function _get_rank($element);
    protected abstract function _get_uniq_id($element);
    public abstract function valid_url($url);

    /**
     * Create new Directory_Parser child
     *
     * @param $directory
     *
     * @return Directory_Parser
     * @throws Exception
     */
    static public function factory($directory){
        $class_name = 'Directory_Parser_'.$directory;
        if(!class_exists($class_name)){
            throw new Exception('Unable to load class: '.$class_name);
        }
        return new $class_name();
    }

    /**
     * Set url to parser
     *
     * @param $url
     *
     * @return Directory_Parser
     * @throws Exception
     */
    public function set_url($url){
        if(!$this->valid_url($url)){
            throw new Exception('Invalud directory url: '.$url);
        }
        $this->url = $url;
        return $this;
    }

    /**
     * Check is set url
     *
     * @throws Exception
     */
    protected function is_url_set(){
        if(empty($this->url)){
            throw new Exception('Url of directory doesn\'t configured.');
        }

    }
	
	/*
	*Return count collected reviews until now
	*
	*
	*/
	public function exist_reviews(){
		
		$raws = Review::count_by_user_dir($this->directory_user['user_id'], $this->directory_user['directory_id']);
		
		return $raws;
	}
	/*
	*Set directory user
	*
	*@directory_user array from Directory_User model
	*/
	public function set_directory_user($directory_user){
	
		$this->directory_user = $directory_user;
		
	}

}
