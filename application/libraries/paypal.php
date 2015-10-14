<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
@author Calvin Froedge
@url	www.calvinfroedge.com

Use however you like!
*/

class Paypal
{

    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $password;

    /**
     * @var
     */
    private $signature;

    /**
     * @var
     */
    private $proxyHost;

    /**
     * @var
     */
    private $proxyPort;

    /**
     * @var
     */
    private $sandboxMode;

    /**
     * @var
     */
    private $useProxy;

    /**
     * @var
     */
    private $version;

    /**
     * @var
     */
    private $apiEndpoint;

    /**
     * @var
     */
    private $paypalUrl;

    /**
     * @var
     */
    private $sBNCode;

    public function __construct( $config )
    {

        $paypalSettings = new Paypal_api_key(1);

        $this->user = $paypalSettings->user;
        $this->password = $paypalSettings->password;
        $this->signature = $paypalSettings->signature;
        $this->proxyHost = $config['proxyHost'];
        $this->proxyPort = $config['proxyPort'];
        $this->useProxy = $config['useProxy'];
        $this->sandboxMode = $paypalSettings->sandbox_mode;
        $this->version = $config['version'];
        $this->sBNCode = $config['sBNCode'];

        if ( $this->sandboxMode ) {
            $this->apiEndpoint = "https://api-3t.sandbox.paypal.com/nvp";
            $this->paypalUrl = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
        } else {
            $this->apiEndpoint = "https://api-3t.paypal.com/nvp";
            $this->paypalUrl = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
        }

    }

    public function createReccuringPaymentsProfile($data)
    {
        $methodName = 'CreateRecurringPaymentsProfile';
        $dataString = $this->buildQueryString($data);

        return $this->apiCall( $methodName, $dataString );
    }

    private function buildQueryString( $postData )
    {
       foreach ($postData as $_key => $_value) {
           $postData[$_key] = urlencode($_value);
       }

        $nvpstr = "";
        $nvpstr.="&SHIPTONAME=".$postData['firstName'].''.$postData['lastName'];
        $nvpstr.="&SHIPTOSTREET=".$postData['address'];
        $nvpstr.="&SHIPTOCITY=".$postData['city'];
        $nvpstr.="&SHIPTOSTATE=".$postData['state'];
        $nvpstr.="&SHIPTOZIP=".$postData['zipCode'];
        $nvpstr.="&SHIPTOCOUNTRY=".$postData['country'];
        $nvpstr.="&PROFILESTARTDATE=".$postData['startDate'];
        $nvpstr.="&DESC=".urlencode("Plan subscription");
        $nvpstr.="&BILLINGPERIOD=".$postData['billingPeriod'];
        $nvpstr.="&BILLINGFREQUENCY=".$postData['billingFrequency'];
        $nvpstr.="&AMT=".$postData['amount'];
        $nvpstr.="&CREDITCARDTYPE=".$postData['ccType'];
        $nvpstr.="&ACCT=".$postData['ccNumber'];
        $nvpstr.="&EXPDATE=".$postData['expirationMonth'].$postData['expirationYear'];
        $nvpstr.="&CVV2=".$postData['cvvCode'];
        $nvpstr.="&CURRENCYCODE=USD";
        $nvpstr.="&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];

        return $nvpstr;
    }


    private function apiCall( $methodName, $nvpStr )
    {


        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiEndpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
        if( $this->useProxy )
            curl_setopt ($ch, CURLOPT_PROXY, $this->proxyHost. ":" . $this->proxyPort);

        //NVPRequest for submitting to server
        $nvpreq="METHOD=" . urlencode( $methodName ) . "&VERSION=" . urlencode( $this->version ) .
            "&PWD=" . urlencode( $this->password ) . "&USER=" . urlencode( $this->user ) .
            "&SIGNATURE=" . urlencode( $this->signature ) . $nvpStr . "&BUTTONSOURCE=" . urlencode( $this->sBNCode );

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec($ch);

        $result =  $this->deformatNVP($response);

        return $result;
    }

    /*'----------------------------------------------------------------------------------
	 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  * It is usefull to search for a particular key and displaying arrays.
	  * @nvpstr is NVPString.
	  * @nvpArray is Associative Array.
	   ----------------------------------------------------------------------------------
	  */
    private function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr))
        {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }
        return $nvpArray;
    }

    /**
     * Performs an Express Checkout NVP API operation as passed in $action.
     *
     * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
     * Express Checkout  NVP API can be used.
     */
    public function changeSubscriptionStatus( $profile_id, $action ) {

        $api_request = 'USER=' . urlencode( $this->user )
            .  '&PWD=' . urlencode( $this->password )
            .  '&SIGNATURE=' . urlencode( $this->signature )
            .  '&VERSION='.$this->version
            .  '&METHOD=ManageRecurringPaymentsProfileStatus'
            .  '&PROFILEID=' . urlencode( $profile_id )
            .  '&ACTION=' . urlencode( $action )
            .  '&NOTE=' . urlencode( 'Profile cancelled at store' );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp' ); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
        curl_setopt( $ch, CURLOPT_VERBOSE, 1 );

        // Uncomment these to turn off server and peer verification
        // curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        // Set the API parameters for this transaction
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );

        // Request response from PayPal
        $response = curl_exec( $ch );

        // If no response was received from PayPal there is no point parsing the response
        if( ! $response )
            die( 'Calling PayPal to change_subscription_status failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );

        curl_close( $ch );

        // An associative array is more usable than a parameter string
        parse_str( $response, $parsed_response );

        return $parsed_response;
    }

}