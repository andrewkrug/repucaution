<?php defined('BASEPATH') or die('No direct script access.');

require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Client.php';
require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Auth/OAuth2.php';

class Socializer_Google
{
    private $_ci;
    private $_config;
    private $_google;
    private $_user_id;
    private $_token;
    protected $dateTimeFormat = 'Y-m-d\TH:i:s.u\Z';

    function __construct( $user_id, $token = null ) {
        $this->_ci =& get_instance();
        $this->_ci->config->load('social_credentials');

        $mtype = $this->getMediaType();
        $this->_config = Api_key::build_config('google', $this->_ci->config->item($mtype));

        $this->_user_id = $user_id;
        $this->_google = new Google_Client();
        $this->_google->setApplicationName("Script test");
        $this->_google->setClientId($this->_config['client_id']);
        $this->_google->setClientSecret($this->_config['secret']);
        $this->_google->setRedirectUri($this->_config['redirect_uri']);
        $this->_google->setDeveloperKey($this->_config['developer_key']);
        $this->_google->setAccessType('offline');
        if($mtype == 'youtube'){
            $this->_google->setScopes(array(
                'https://gdata.youtube.com',
                'https://www.googleapis.com/auth/userinfo.profile'
            ));
        }elseif($mtype == 'google_auth' || $mtype == 'google_login'){
            $this->_google->setScopes(array(
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email'
            ));
        }else{
            $this->_google->setScopes(array(
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/plus.me',
//                'https://www.googleapis.com/auth/plus.stream.write',
//                'https://www.googleapis.com/auth/plus.media.upload'
            ));
        }

        if (!$token) {
            $this->_token = Access_token::inst()->get_one_by_type('google', $this->_user_id)->to_array();
        } else {
            $this->_token = $token;
        }
    }
    /**
     * Used for detrmine media type(Google Plus or Youtube)
     * 
     *
     * @access protected
     * @return string
     */
    protected function getMediaType(){
        preg_match('/^youtube/',$this->_ci->router->method, $match);
        if(!empty($match[0])){
            $mtype = 'youtube';
        }else{
            preg_match('/^auth/',$this->_ci->router->class, $match);
            if(!empty($match[0])){
                preg_match('/^(google_auth|google_login)/',$this->_ci->router->method, $match);
                if(!empty($match[0])) {
                    $mtype = 'google_login';
                } else {
                    $mtype = 'google_auth';
                }
            } else {
                $mtype = 'google';
            }
        }
        return $mtype;
    }

    /**
     * Used to get google access url
     * Use Google_Client Library
     *
     * @access public
     * @return string
     */
    public function get_access_url() {
        return $this->_google->createAuthUrl();
    }

    /**
     * Used to add new record to Access Tokens Table
     *
     * @access public
     *
     * @param $profile_id
     *
     * @return string
     */
    public function add_new_account($profile_id) {
        if(isset($_GET['code'])) {

            require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';
            $plusService = new Google_Service_Plus($this->_google);

            $this->_google->authenticate($_GET['code']);
            $token = $this->_google->getAccessToken();
            
            $access_token = new Access_token();

            $profile = $plusService->people->get('me');

            $tokens = array(
                'token' => null,
                'secret_token' => null,
                'data' => $token,
                'name' => $profile['displayName'],
                'image' => $profile['image']['url'],
                'username' => $profile['id']
            );

            $mtype = $this->getMediaType();
            $token = $access_token->add_token($tokens, $mtype, $this->_user_id);

            $social_group = new Social_group($profile_id);
            $social_group->save(array('access_token' => $token));

            $redirect_url = site_url('settings/socialmedia');

            return $redirect_url;
        }
    }

    /**
     * Used to add new record to Access Tokens Table
     *
     * @access public
     *
     * @return string
     */
    public function sign_up() {
        if(isset($_GET['code'])) {

            require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';
            $plusService = new Google_Service_Plus($this->_google);

            $this->_google->authenticate($_GET['code']);
            $profile = $plusService->people->get('me');

            return $profile;
        } else {
            return false;
        }
    }

    public function post_video( $title, $description, $video_name ) {
        require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/YouTube.php';
        $token = Access_token::inst()->get_youtube_token($this->_user_id);
        $this->_google->setApplicationName("Google keywords");
        $this->_google->setAccessToken($token->data);
        $youTubeService = new Google_Service_YouTube($this->_google);

        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($description);
        $snippet->setTags(array("video"));
        $snippet->setCategoryId("22");

        $status = new Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = "public";

        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);
        
        $error = true;
        $i = 0;

        $video_path = FCPATH.'public/uploads/'.$this->_user_id.'/'.$video_name;
        if(function_exists('mime_content_type')) {
            $mime_type = mime_content_type($video_path);
        } else {
            $mime_type = $this->_get_mime_content_type($video_name);
        }
        try {
            $obj = $youTubeService->videos->insert("status,snippet", $video,
                array("data"=>file_get_contents($video_path),
                    "mimeType" => $mime_type));
            return $obj;
        } catch(Google_ServiceException $e) {
            print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage(). " <br>";
            print "Stack trace is ".$e->getTraceAsString();
        }
    }

    public function post() {
        try {
            require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/PlusDomains.php';

            $this->_google->setAccessToken($this->_token['data']);

            $object = new Google_Service_PlusDomains_ActivityObject();
            $object->setOriginalContent('Test message');

            $access = new Google_Service_PlusDomains_Acl();
            $access->setDomainRestricted(true);

            $resource = new Google_Service_PlusDomains_PlusDomainsAclentryResource();
            $resource->setType("public");

            $resources = array();
            $resources[] = $resource;
            $access->setItems($resources);

            $activity = new Google_Service_PlusDomains_Activity();
            $activity->setObject($object);
            $activity->setAccess($access);

            $plusDomain = new Google_Service_PlusDomains($this->_google);
            var_dump($plusDomain->activities->insert('me', $activity));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Return response from Google Plus searching activity via API
     *
     * @access public
     * @param $query keyword for searching
     * @param $params array of optional params
     * @return Google_Service_Plus_ActivityFeed
     */
    public function search_activities($query, $params = array()) {
        require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';

        $this->_google->setAccessToken($this->_token['data']);

        $plus = new Google_Service_Plus($this->_google);

        $searchResponse = $plus->activities->search($query, $params); 

        return $searchResponse;
    }


    /**
     * Get user activity via API
     *
     * @access public
     * @param $params array of optional params
     * @return mixed
     */
    public function getUserActivities($params = array())
    {
        require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';

        $this->_google->setAccessToken($this->_token['data']);

        $plus = new Google_Service_Plus($this->_google);
        $response = $plus->activities->listActivities("me", "public", $params);

        return $this->processGoogleActivitiesResponse($response);
    }

    /**
     * Process google activity response
     *
     * @param $response
     *
     * @return mixed
     */
    public function processGoogleActivitiesResponse($response)
    {

        if (!isset($response['nextPageToken'])) {
            $response['nextPageToken'] = null;
        }

        if (!isset($response['items'])) {
            $response['items'] = array();
        }

        if (!empty($response['updated'])) {
            $response['updated'] = $this->convertDateTime($response['updated']);
        }

        foreach ($response['items'] as $key => $item) {
            $response['items'][$key]['published'] = $this->convertDateTime($item['published']);
            $response['items'][$key]['updated'] = $this->convertDateTime($item['updated']);
        }

        return $response;
    }

    /**
     * Convert string to DateTime
     *
     * @param $dateTimeString
     *
     * @return DateTime
     */
    public function convertDateTime($dateTimeString)
    {
       return DateTime::createFromFormat($this->dateTimeFormat, $dateTimeString);
    }
    
    /**
     * Return comments for Google Plus activity
     *
     * @access public
     * @param $activityId unique id of activity
     * @return array
     */
    public function getComments($activityId) {
        require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';
        
        $this->_google->setAccessToken($this->_token['data']);
        
        $plus = new Google_Service_Plus($this->_google);//var_dump($plus->activities);die;
        $params = array();
        $comments = $plus->comments->listComments($activityId, $params); 
        //$searchResponse = $plus->activities->listActivities('me', 'public', array('maxResults'=>1)); 
        //var_dump($searchResponse);die;
        return $comments;
    }

    /**
     * Return count of people whos can view user
     *
     * @access public
     *
     * @param string $userId
     *
     * @return int
     */
    public function getPeopleCount($userId = 'me') {
        require_once __DIR__.'/../../../../vendor/google/apiclient/src/Google/Service/Plus.php';
        
        //delay for not over quota limits
        sleep(1);
        $this->_google->setAccessToken($this->_token['data']);
        
        $plus = new Google_Service_Plus($this->_google);
        $params = array('maxResults'=>1);
        $response = $plus->people->listPeople($userId, 'visible', $params);
        return $response['totalItems'];
    }


    /**
     * Google comment time beautifier
     * Return comment create-time in format like : '12 hours ago'
     * or '12 of march on Google' or '10 minutes ago'
     *
     * @access public
     * @param $comment_time
     * @return string
     */
    public function convert_google_time( $comment_time ) {
        $diff = time() - strtotime($comment_time);
        $date = strtotime($comment_time);
        if($diff > 3600){
            if($diff < 86400){
                $diff = $diff/3600;
                return round($diff,0).' hours ago on Google';
            } else {
                return date('d F',$date).' on Google';
            }
        } else{
            $diff = $diff/60;
            return round($diff,0).' minutes ago on Google';
        }
    }
    
    /*
     * Get content type of file
     * Used for old PHP versions
     * Where not isset function mime_content_type
     *
     * @access private
     * @return void
     */
    private function _get_mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
}

}