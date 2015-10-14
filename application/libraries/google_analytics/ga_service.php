<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');
/**
 * ga_service class
 * 
 * extends Google_AnalyticsService class for as-CI-library use
 * which is located at ./application/third_party/google-analytics-api/contrib/Google_AnalyticsService.php'
 * 
 * Google APIs Client Library For PHP @version 0.6.0
 * @link https://code.google.com/p/google-api-php-client/
 */

/**
 * Include Google_AnalyticsService
 */
require_once APPPATH.'third_party/google-analytics-api/contrib/Google_AnalyticsService.php';


class ga_service extends Google_AnalyticsService {

    public function __construct($params) {   
        parent::__construct($params['client']);
    }

    /**
     * Get Google Analytics accounts with webproperties
     * 
     * @access public
     * @return array - array[ %account_id%&%account_name ] [ %webproperty_id%&%webproperty_name% ] [ %profile_id% ] = %profile_name%
     */
    public function get_accounts() {
        $result = array();
        // get GA accounts
        $accounts = $this->management_accounts->listManagementAccounts();
        $accounts_items = $accounts->getItems();
        if (count($accounts_items) > 0) {
            foreach ($accounts_items as $account_item) {
                // create "id&name" - key
                // $result[ $account_item->getId() . '&' . $account_item->getName() ] = array();
                $result[ $account_item->getName() ] = array();
                // get webproperties for current account
				
                $webproperties = $this->management_webproperties->listManagementWebproperties($account_item->getId());
                $webproperties_items = $webproperties->getItems();
                // add each webproperty to array
                foreach($webproperties_items as $webproperty_item) {
                    $profiles = $this->management_profiles->listManagementProfiles($account_item->getId(), $webproperty_item->getId());
                    $profiles_items = $profiles->getItems();
                    if (is_array($profiles_items)) {
                        foreach($profiles_items as $profile_item) {
                            // $result[ $account_item->getId() . '&' . $account_item->getName() ]
                            $result[ $account_item->getName() ]
                                // [ $webproperty_item->getId() . '&' . $webproperty_item->getName() ]
                                [ $webproperty_item->getName() ]
                                [ $profile_item->getId() ] = $profile_item->getName();    
                        }
                    } else if( ! is_null($profiles_items) ) {
                        // $result[ $account_item->getId() . '&' . $account_item->getName() ]
                        $result[ $account_item->getName() ]
                                // [ $webproperty_item->getId() . '&' . $webproperty_item->getName() ]
                                [ $webproperty_item->getName() ]
                                [ $profiles_items['id'] ] = $profiles_items['name'];
                    }
                }     
            }
            return $result;
        } else {
            throw new Exception('No accounts found');
        }
    }

}
/* End of file ga_service.php */
/* Location: ./application/libraries/ga_service.php */