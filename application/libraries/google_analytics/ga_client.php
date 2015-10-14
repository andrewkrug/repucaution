<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');
/**
 * ga_client class
 * 
 * extends Google_Client class for as-CI-library use
 * which is located at ./application/third_party/google-analytics-api/Google_Client.php'
 * 
 * Google APIs Client Library For PHP @version 0.6.0
 * @link https://code.google.com/p/google-api-php-client/
 */

/**
 * Include Google_Client
 */
require_once APPPATH.'third_party/google-analytics-api/Google_Client.php';


class ga_client extends Google_Client {

    protected $settings;

    public function __construct() {   
        parent::__construct();
    }

    /**
     * Set Client ID, Client Secret and Redirect URI
     * 
     * if u already have google api access for apps
     * find them at http://code.google.com/apis/console, API Access menu item
     * 
     * @param $client_id (string) - client id
     * @param $client_secret (string) - client secret
     * @param $redirect_uri (string) - redirect uri
     * @return ga_client
     */
    public function client_init($settings) {
        // set app params
        $this->setClientId($settings['client_id']);
        $this->setClientSecret($settings['client_secret']);
        $this->setRedirectUri($settings['redirect_uri']);
        $this->settings = $settings;
        // use google analytics
        $this->setScopes(array('https://www.googleapis.com/auth/analytics.readonly', 'https://gdata.youtube.com', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/plus.me'));
        // get data as object
        $this->setUseObjects(TRUE);
        return $this;
    }

    public function logout($data) {
        if (isset($data['logout'])) {
            $this->authenticate();
            $_SESSION['token'] = $this->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }
    }

    public function code($data) {
        $result = array(NULL, NULL);

        if (isset($data['code'])) {

            $params = array(
                'code' => $data['code'],
                'client_id' => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'redirect_uri' => $this->settings['redirect_uri'],
                'grant_type' => 'authorization_code'
            );

            $url = "https://accounts.google.com/o/oauth2/token" ;
            $response = $this->_curl_post($url, $params);
            $json = json_decode($response);     
                
            if ($response == FALSE OR $json == FALSE OR ! isset($json->refresh_token) OR ! $json->refresh_token) {
                return $result;
            }

            $this->refreshToken($json->refresh_token); 
            $access_token = $this->getAccessToken();

            $result = array($access_token, $json->refresh_token);
        }

        return $result;
    }




    /**
     * Curl post for url
     * 
     * @param $url (string)
     * @param $params (array) - post data
     * @return curl response
     */
    protected function _curl_post($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 

        $response = curl_exec($ch);
        curl_close ($ch);

        return $response;
    }


    /**
     * Closes current window
     * 
     * @return void
     */
    public function auto_js_redirect($url) {
        print '
        <script language="JavaScript">
        <!--
            window.opener.location.href = window.opener.location.href;
            if (window.opener.progressWindow) {
                window.opener.progressWindow.close();
            }
            window.close();
        -->
        </script>';
        // if no js
        print '
        <noscript>Your browser does not support JavaScript!
            <p>Redirecting to <a href="' . $url . '">' . $url . '</a> ...</p>
            <META HTTP-EQUIV=Refresh CONTENT="0; URL=\'' . $url . '\'">
        </noscript>';
    }

}
/* End of file ga_client.php */
/* Location: ./application/libraries/ga_client.php */