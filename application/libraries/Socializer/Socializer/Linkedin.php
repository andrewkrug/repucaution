<?php

require_once __DIR__.'/../vendors/linkedin/linkedin.php';

/**
 * Linkedin service.
 *
 * @author Alex Z <ajorjik@tut.by>
 * 
 */
class Socializer_Linkedin {

    private $_ci;
    private $_config;
    private $_linkedin;
    private $_user_id;

    /**
     * Current user token for twitter
     *
     * @var
     */
    private $_token;

    const MAX_DESCRIPTION_LENGTH = 400;
    const MAX_TITLE_LENGTH = 200;

    /**
     * Method for get access to linkedin
     *
     * @access public
     * @param $user_id id current user
     * @param array|null $token
     * @return Socializer_Linkedin
     */
    function __construct($user_id, $token) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');

        $this->_config = Api_key::build_config('linkedin', $this->_ci->config->item('linkedin'));

        $this->_user_id = (string)$user_id;
        $this->_linkedin = new Linkedin($this->_config);
        if (!$token) {
            $this->_token = Access_token::inst()->get_one_by_type('linkedin', $this->_user_id)->to_array();
        } else {
            $this->_token = $token;
        }
    }
    /**
     * Method for get access to linkedin
     *
     * @access public
     * @return string
     */
    public function get_access(){
        $response = $this->_linkedin->retrieveTokenRequest();
        $secret = isset($response['linkedin']['oauth_token_secret']) ? $response['linkedin']['oauth_token_secret'] : '';
        $token = isset($response['linkedin']['oauth_token']) ? $response['linkedin']['oauth_token'] : '';
        $this->_ci->session->set_userdata(array('linkedin_token_secret' => $secret));

        return Linkedin::_URL_AUTH . $token;
    }
    
    
    /**
     * Create access token for linkedin
     *
     * @access public
     * @return void
     */
    public function add_new_account($profile_id){
        if($this->_ci->session->userdata('linkedin_token_secret')){
            $verifier = $_GET['oauth_verifier'];
            $oauth_token = $_GET['oauth_token'];
            $response = $this->_linkedin->retrieveTokenAccess($oauth_token, $this->_ci->session->userdata('linkedin_token_secret'), $verifier);
            $this->_ci->session->unset_userdata('linkedin_token_secret');
            $token = serialize($response['linkedin']);
            $access_token = new Access_token();
            $profile = Linkedin::xmlToArray($this->_linkedin->profile('~:(id,formatted-name,picture-url)')['linkedin'])['person']['children'];
            $tokens = array(
                'token' => null,
                'secret_token' => null,
                'data' => $token,
                'name' => $profile['formatted-name']['content'],
                'username' => $profile['id']['content'],
                'image' => $profile['picture-url']['content']
            );
            $token = $access_token->add_token($tokens, 'linkedin', $this->_user_id);

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));
        }
        
    }
    
    /**
     * Creates post at linkedin
     *
     * @param array $data param of posting data
     * @return array
    */
    public function createPost($data){
        
        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);
                
        $content['title'] = $data['title'];
        $content['description'] = $data['description'];
        $content['submitted-url'] = $data['url'];
        if(isset($data['image_name']) && !empty($data['image_name'])) {
            $content['submitted-image-url'] = site_url('/public/uploads/'.$this->_user_id.'/'.$data['image_name']);
        }

        $response = $this->_linkedin->share('new', $content);

        return $response;
        
    }
    
    /**
     * Get network updates and add it to database
     *
     * @access public
     * @return void
     */
    public function getUpdates(){
        
        $token = unserialize($this->_token['data']);
        if(is_array($token)){
           
            $this->_linkedin->setToken($token);
            $qOptions = array(
                              '?scope=self&count=250&show-hidden-members=true',
                              '?scope=self&type=SHAR&count=250&show-hidden-members=true',
                              '?count=250&show-hidden-members=true',
                              '?type=SHAR&count=250&show-hidden-members=true',          
                        );
            
            foreach ($qOptions as $options) {
                $result = array();
                $response = $this->_linkedin->updates($options);
                $result = $this->parseUpdates($response['linkedin']);
                echo $response['linkedin'] . "\n";
                $response = null;
                if(count($result)>0){
                    foreach($result as $update){
                        $activity = new Social_activity();
                        foreach($update as $k=>$v){
                            if(is_array($v)){
                                $v = serialize($v);
                            }
                            $activity->$k = $v;
                        }
                        $activity->save();
                    }
                }
            }    
        }
    }
    
    /**
     * Parse linkedin's network updates
     *
     * @access protected
     * @param $xml (string) xml response from api
     * @return array of parsed updates
     */
    protected function parseUpdates($xml){
        
        $sxml = simplexml_load_string($xml);
        $count = $sxml->update->count();
        $result=array();
        if($count>0){
            foreach($sxml->update as $u){
                $update = array();
                $update['created_at'] = substr_replace((string)$u->timestamp,'',10, 3);
                $update['original_id'] = (string)$u->{'update-key'};

                $type = (string)$u->{'update-type'};
                switch ($type){
                    case 'CONN':
                        $person = $u->{'update-content'}->person;
                        if(!((string)$person->id == 'private')){
                            $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                            $person_id = (string)$person->id;
                            $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                            $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;

                            $userconn = $person->connections->person;
                            $userconn_name = (string)$userconn->{'first-name'}." ".(string)$userconn->{'last-name'};
                            if(isset($userconn->{'headline'})){
                                $userconn_headline = ', '.(string)$userconn->{'headline'};          
                                
                            }else{
                                $userconn_headline = '';
                            }
                            $update['picture_conn'] = (string)$userconn->{'picture-url'};
                            
                            $profile_conn = (string)$userconn->{'site-standard-profile-request'}->url;
                            if ($userconn_name == 'private private') {
                                $message = '<a href="'.$profile_url.'">'.$person_name.'</a> connected to private';
                            } else {
                                $message = '<a href="'.$profile_url.'">'.$person_name.'</a> connected to <a href="'.$profile_conn.'">'.$userconn_name.'</a>'.$userconn_headline;
                            }
                        } else {
                            $message = null;
                        }
                        break;
                    
                    case 'PICU':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $message =  '<a href = "'.$profile_url.'">'.$person_name."</a> has a new photo!";
                        $update['other_fields']['image'] = $picture;
                        break;
                    
                    case 'CCEM':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $message = '<a href="'.$profile_url.'">'.$person_name."</a> has joined LinkedIn.";
                        break;
                    
                    case 'MSFC':
                        $person = $u->{'update-content'}->{'company-person-update'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $company = $u->{'update-content'}->company;
                        $company_name = (string)$company->name;
                        
                        $message = '<a href="'.$profile_url.'">'.$person_name.'</a> starts following a company '.$company_name;
                        break;
                    
                    case 'JGRP':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $group = $person->{'member-groups'}->{'member-group'};
                        $group_name = (string)$group->name;
                        $group_link = (string)$group->{'site-group-request'}->url;
                        $message = '<a href="'.$profile_url.'">'.$person_name.'</a> joined the group <a href="'.$group_link.'">'.$group_name.'</a>.';
                        break;
                        
                    case 'CMPY':
                        $company = (string)$u->{'update-content'}->{'company-update'}->company->name;
                        $company_id = (string)$u->{'update-content'}->{'company-update'}->company->id;
                        if (isset($u->{'update-content'}->{'company-update'}->{'company-profile-update'})) {
                            $profile_url = (string)$u->{'update-content'}->{'company-update'}->{'company-profile-update'}->{'site-standard-profile-request'}->url;
                            $action = (string)$u->{'update-content'}->{'company-update'}->{'company-profile-update'}->action->code;     
                            $message = 'Profile of company <a href="'.$profile_url.'">'.$company .'</a>was '.$action.'.';
                        }
                        if (isset($u->{'update-content'}->{'company-update'}->{'company-job-update'})) {
                            $job = (string)$u->{'update-content'}->{'company-update'}->{'company-job-update'}->job->position->title;
                            $job_link =(string)$u->{'update-content'}->{'company-update'}->{'company-job-update'}->job->{'site-job-request'}->url;
                            $action = (string)$u->{'update-content'}->{'company-update'}->{'company-job-update'}->action;
                            $message = 'Company '.$company.' was '.$action.'a job <a href="'.$job_link.'">'.$job.'</a>';
                        }
                        
                        if (isset($u->{'update-content'}->{'company-update'}->{'company-person-update'})) {
                            $person = $u->{'update-content'}->{'company-update'}->{'company-person-update'}->person;
                            $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                            $profile_url = (string)$u->{'update-content'}->{'company-update'}->{'company-profile-update'}->{'site-standard-profile-request'}->url;
                            $action = (string)$u->{'update-content'}->{'company-update'}->{'company-person-update'}->action;
                            $message = $person_name." was ".$action." to ".$company;
                        }
                        break;
                        
                    case 'PFOL':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $person_foll = $person->following->people->person;
                        $update['other_fields']['follow']['name'] = (string)$person_foll->{'first-name'}." ".(string)$person_foll->{'last-name'};
                        if (isset($person_foll->{'headline'})) {
                            $update['other_fields']['follow']['headline'] = (string)$person_foll->{'headline'};         
                            
                        } else {
                            $update['other_fields']['follow']['headline'] = '';
                        }
                        $update['other_fields']['follow']['id'] = (string)$person_foll->id;
                        $update['other_fields']['follow']['picture'] = (isset($person_foll->{'picture-url'})) ? (string)$person_foll->{'picture-url'} : null;
                        $update['other_fields']['follow']['profile'] = (string)$person_foll->{'site-standard-profile-request'}->url;
                        
                        $share = $person_foll->{'current-share'}->content;
                        $update['other_fields']['share']['thumbnail'] = (string)$share->{'thumbnail-url'};
                        $update['other_fields']['share']['title'] = (string)$share->title;
                        $update['other_fields']['share']['description'] = (string)$share->description;
                        $update['other_fields']['share']['url'] = (string)$share->{'submitted-url'};        
                        
                        
                        $message = '<a href="'.$profile_url.'">'.$person_name.'</a> is now following what '.$update['other_fields']['follow']['name'].' is saying on LinkedIn.';
                        break;          
                    
                    case 'SHAR':
                        $person = $u->{'update-content'}->person;
                        $person_id = (string)$person->id;
                        if (!($person_id == 'private')) {
                            $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                            $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                            $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                            
                            $share = $person->{'current-share'};
                            $message = ($share->comment) ? $share->comment : 'blank';;
                            $update['other_fields']['author'] = $person_name;
                            $update['other_fields']['author_profile'] = $profile_url;
                            if ($share->content) {
                                $update['other_fields']['share']['thumbnail'] = (string)$share->content->{'thumbnail-url'};
                                $update['other_fields']['share']['title'] = (string)$share->content->title;
                                $update['other_fields']['share']['description'] = (string)$share->content->description;
                                $update['other_fields']['share']['url'] = (string)$share->content->{'submitted-url'};        
                            } 
                        } else {
                            $message = null;
                        }
                        break;
                    
                    case 'PROF':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        $message = '<a href="'.$profile_url.'">'.$person_name.'</a> updated profile.';
                        break;
                    
                    case 'PREC':
                        $person = $u->{'update-content'}->person;
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        
                        if ($person->{'recommendations-given'}) {
                            $recommend = $person->{'recommendations-given'}->recommendation;
                            $recommendee = $recommend->recommendee;
                            $recommendee_name = (string)$recommendee->{'first-name'}." ".(string)$recommendee->{'last-name'};
                            $recommendee_url = (string)$recommendee->{'site-standard-profile-request'}->url;
                            $message = '<a href="'.$profile_url.'">'.$person_name.'</a> recommends <a href="'.$recommendee_url.'">'.$recommendee_name.'</a>.';
                        
                        } else {
                            $recommend = $person->{'recommendations-received'}->recommendation;
                            $recommender = $recommend->recommender;
                            $recommender_name = (string)$recommender->{'first-name'}." ".(string)$recommender->{'last-name'};
                            $recommender_url = (string)$recommender->{'site-standard-profile-request'}->url;
                            $message = '<a href="'.$profile_url.'">'.$person_name.'</a> was recommended by <a href="'.$recommender_url.'">'.$recommender_name.'</a>.';
                        }
                        $recommend_type = (string)$recommend->{'recommendation-type'}->code;
                        $message .= '<a href="'.$recommend->{'web-url'}.'">View recommendation</a>';
                        break;
                    
                    case 'JOBP':
                        $job = $u->{'update-content'}->job;
                        $position = (string)$job->position->title;
                        $company =  (string)$job->company->name;
                        
                        $person = $job->{'job-poster'};
                        $person_name = (string)$person->{'first-name'}." ".(string)$person->{'last-name'};
                        $person_id = (string)$person->id;
                        $picture = (isset($person->{'picture-url'})) ? (string)$person->{'picture-url'} : null;
                        $profile_url = (string)$person->{'site-standard-profile-request'}->url;
                        
                        $message = '<a href="'.$profile_url.'">'.$person_name.'</a> posting job: '.$position.' at '.$company;
                        break;
                    
                    default:
                        $message = null;
                        break;
                }
                
                if ($message) {
                    $update['other_fields']['comment']=(string)$u->{'is-commentable'};
                    if ($u->{'update-comments'}) {
                        $update['other_fields']['comment_count']=(string)$u->{'update-comments'}[0]['total'];
                    }
                    $update['message'] = $message;
                    $update['social'] = 'linkedin';
                    $update['user_id'] = $this->_user_id;
                    $update['creator_name'] = (isset($person_name)) ? $person_name : $company;
                    $update['creator_id'] = (isset($person_id)) ? $person_id : $company_id;
                    $update['creator_image_url'] = (isset($picture)) ? $picture : null;
                    $update['other_fields']['type'] = $type;
                    if (isset($image)) {
                        $update['other_fields']['image'] = $image;
                    }
                    if (isset($image_conn)) {
                        $update['other_fields']['image_conn'] = $image_conn;
                    }
                    $update['other_fields']['likable'] = (string)$u->{'is-likable'};
                    
                    if ($update['other_fields']['likable']) {
                        $update['likes'] = (string)$u->{'num-likes'};
                        $update['other_fields']['liked'] = (string)$u->{'is-liked'};
                    }
                    
                    $result[]=$update;
                }
            }
            
        }
       
       return $result;
        
    }
    
    /**
     * Get comments of update from linkedin
     *
     * @access public
     * @param $key string id of update
     * @return array
     */
    public function getComments($key){
        
        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);
        
        $result=array();
        $response = $this->_linkedin->comments($key);
        return $this->parseComments($response['linkedin']);
    }
    
    /**
     * Parse response with comments from linkedin
     *
     * @access protected
     * @param $data string of xml response
     * @return array
     */
    protected function parseComments($data){
        
        $result = array();
        $sxml = simplexml_load_string($data);
        $count = $sxml->{'update-comment'}->count();
        
        if($count>0){
            
            foreach($sxml->{'update-comment'} as $c){
                $comment['comment'] = (string)$c->comment;
                $comment['author'] = array(
                    'name'=>(string)$c->person->{'first-name'}.' '.(string)$c->person->{'last-name'},
                    'profile'=>(string)$c->person->{'site-standard-profile-request'}->url,
                    'picture'=>(string)$c->person->{'picture-url'},
                );
                                        
                $comment['created'] = (string)$c->timestamp;
                $result[] = $comment;
                
            }
            
        }
        
        return $result;
    }
    /**
     * Post comment to linkedin
     *
     * @access public
     * @return void
     */
    public function postComment(){

        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);
        
        $key = $_POST['key'];
        $result=array();
        $response = $this->_linkedin->comment($key, $_POST['message']);
        

    }
    
    /**
     * Post like to linkedin
     *
     * @access public
     * @return array
     */
    public function like(){

        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);

        return $this->_linkedin->like($_POST['key']);
    }
    
    
    /**
     * Post unlike to linkedin
     *
     * @access public
     * @return array
     */
    public function unlike(){

        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);

        return $this->_linkedin->unlike($_POST['key']);
    }
    
    
    
    /**
     * Get count of linkedin connections
     *
     * @access public
     * @return integer of count of user's connections
     */
    public function get_conns_count(){
        
        $token = unserialize($this->_token['data']);
        $this->_linkedin->setToken($token);
        
        $response = $this->_linkedin->connections();
        $sxml = simplexml_load_string($response['linkedin']);
        $conns_count = (string)$sxml[0]['total'];
        return $conns_count;
    }


    /**
     * @param $xml
     * @return array
     * @throws LinkedInException
     */
    public function xmlToArray($xml) {
        return $this->_linkedin->xmlToArray($xml);
    }
}

