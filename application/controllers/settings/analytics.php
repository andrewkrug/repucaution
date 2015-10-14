<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends MY_Controller {

    protected $website_part = 'settings';
    protected $analytics_settings;
    protected $access_token;

    /**
     * Load config and google access token model
     */
    public function __construct() {
        parent::__construct($this->website_part);
        $this->template->set('section', 'analytics');
        $this->load->config('site_config', TRUE);

        $this->lang->load('analytics_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('analytics_settings', $this->language)
        ]);

        $analytics_keys = Api_key::build_config('google', $this->config->item('google_app', 'site_config'));
        $this->analytics_settings = array(
            'client_id' => $analytics_keys['client_id'],
            'client_secret' => $analytics_keys['secret'],
            'redirect_uri' => site_url('settings/analytics/connect'),
        );

        $this->access_token = Access_token::getByTypeAndUserIdAndProfileId(
            'googlea',
            $this->c_user->id,
            $this->profile->id
        );
    }

    /**
     * Check if should do redirect after connect
     * if not - just basic info
     */
    public function index() {
        if ($this->session->flashdata('ga_redirect_to_accounts')) {
            //$this->session->keep_flashdata('alert_success');
            //$this->session->keep_flashdata('alert_error');
            redirect('settings/analytics/accounts');
        }
        JsSettings::instance()->add(array(
            'analytics' => array(
                'client_id' => $this->analytics_settings['client_id'],
                'redirect_uri' => $this->analytics_settings['redirect_uri'],
            ),
        ));
        CssJs::getInst()->c_js();
        $this->template->set('access_token', $this->access_token);
        $this->template->set('account_info', array_filter($this->access_token->account_info()));
		
        $this->template->render();
    }

    /**
     * Save ga account to db after user "in-new-window" google authorization
     */
    public function connect() {
        try {  
            $this->load->library('google_analytics/ga_client');
            $client = $this->ga_client->client_init($this->analytics_settings);
            $client->logout($_GET);
            list($ga_access_token, $ga_refresh_token) = $client->code($_GET);
            if ($ga_access_token && $ga_refresh_token) {            
                $this->access_token->token1 = $ga_access_token;
                $this->access_token->token2 = $ga_refresh_token;
                $this->access_token->user_id = $this->c_user->id;
                $this->access_token->type = 'googlea';
                $this->access_token->save(array(
                    'social_group' => $this->profile
                ));
                $this->addFlash(lang('connected_success'), 'success');
                $this->session->set_flashdata('ga_redirect_to_accounts', TRUE);
                //$client->auto_js_redirect(site_url('settings/analytics'));
                redirect('settings/analytics');
            }
            if ( ! $client->getAccessToken()) {
                 if ( ! $error = $this->input->get('error')) {
                    $auth_url = $client->createAuthUrl();
                    redirect($auth_url);
                 } else {
                    $this->addFlash(lang('connection_canceled_success'), 'success');
                    $client->auto_js_redirect(site_url('settings/analytics'));
                 }
            }
        } catch (Google_ServiceException $e) {
            $parts = explode(')', $e->getMessage());
            $error_message = (is_array($parts) && $parts[ count($parts) - 1]) ? $parts[ count($parts) - 1] : $e->getMessage();
            $this->addFlash($error_message);
            redirect('settings/analytics');
        } catch(Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/analytics');
        }
    }

    /**
     * Detach ga account (remove from db)
     *
     * @param $id
     */
    public function logout($id) {
        $access_token = new Access_token($id);
        $access_token->delete();
        $this->addFlash(lang('logged_out_success'), 'success');
        redirect('settings/analytics');
    }

    /**
     * - Show all accounts page, accounts loaded by ajax
     * - Save new ga profile 
     */
    public function accounts() {
        if ( ! $this->access_token->token1 OR ! $this->access_token->token2) {
            $this->addFlash(lang('please_connect_error'));
            redirect('settings/analytics');
        }
        if ($this->input->post()) {
            $new_profile = $this->input->post('profile');
            $account_info = Arr::extract($this->input->post(), array('account_name', 'webproperty_name', 'profile_name'));
            $this->access_token->data = serialize($account_info);
            $this->access_token->instance_id = $new_profile;
            $this->access_token->save();
            $this->addFlash(lang('profile_saved_success'), 'success');
            redirect('settings/analytics');
        }
        JsSettings::instance()->add(array(
            'analytics' => array(
                'get_accounts_url' => site_url('settings/analytics/get_accounts'),
            ),
        ));
        CssJs::getInst()->c_js();
        CssJs::getInst()->add_js(array(
            'libs/handlebar.js', 'libs/handlebars_helpers.js'
        ));
        $this->template->set('account_info', $this->access_token->account_info());
        $this->template->render();
    }

    /**
     * Get all ga accounts/webproperties/profiles for this user from ga
     * returned by ajax
     */
    public function get_accounts() {
        if ($this->input->is_ajax_request()) {
            try {
                $this->_service_init($this->access_token->token2);
                
                $accounts = $this->ga_service->get_accounts($this->access_token->instance_id);

                $result['success'] = TRUE;
                $result['result'] = $accounts;
                $result['current'] = $this->access_token->instance_id;
            } catch (Google_AuthException $e) {

                $result['success'] = FALSE;
                $result['error'] = lang('authorization_error');

            } catch (Google_ServiceException $e) {

                $parts = explode(')', $e->getMessage());
                $error_message = (is_array($parts) && $parts[ count($parts) - 1]) ? $parts[ count($parts) - 1] : $e->getMessage();

                $result['success'] = FALSE;
                $result['error'] = $error_message;

            } catch(Exception $e) {
                $result['success'] = FALSE;
                $result['error'] = $e->getMessage();
            }
            exit( json_encode($result) );            
        }
        redirect('settings/analytics');
    }

    /**
     * Load ga client lib and ga service lib for this client to controller
     */
    protected function _service_init($token) {
        $this->load->library('google_analytics/ga_client');
        $client = $this->ga_client->client_init($this->analytics_settings);
        $client->refreshToken($token);
        $this->load->library('google_analytics/ga_service', array('client' => $client));
    }

}