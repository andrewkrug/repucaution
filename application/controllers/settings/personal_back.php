<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Personal extends MY_Controller {

    protected $website_part = 'dashboard';
    protected $analytics_settings;
    protected $access_token;

    /**
     * Load config and google access token model
     */
    public function __construct() {
        parent::__construct($this->website_part);

        $this->load->config('site_config', TRUE);

        $analytics_keys = Api_key::build_config('google', $this->config->item('google_app', 'site_config'));
        $this->analytics_settings = array(
            'client_id' => $analytics_keys['client_id'],
            'client_secret' => $analytics_keys['secret'],
            'redirect_uri' => site_url('settings/analytics/connect'),
        );

        $this->access_token = Access_token::getByTypeAndUserId('googlea', $this->c_user->id);
    }

    public function index() {

        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');

        if (isset($_POST) && !empty($_POST)) {
				
			$config = $this->config->config; 
		
				if(!$config['change_settings']){
					$this->addFlash('<span class="err_icon"></span>Demo version settings can\'t be changed');
				}else{
	
			
						$data = array(
							'first_name' => $this->input->post('first_name'),
							'last_name'  => $this->input->post('last_name'),
						);

						if ($this->input->post('old_password')) {

							$old_password = $this->input->post('old_password');

							$ion_model = new Ion_auth_model;
							$valid_old = $ion_model->hash_password_db($this->c_user->id, $old_password);

							$password_min = $this->config->item('min_password_length', 'ion_auth');
							$password_max = $this->config->item('max_password_length', 'ion_auth');

							$this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[' 
								. $password_min . ']|max_length[' . $password_max . ']|matches[confirm_password]');
							$this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required');

							if ($valid_old) {
								$data['password'] = $this->input->post('new_password');    
							} else {
							   $this->form_validation->create_error('Invalid Old Password');
							}
						}

						if ($this->form_validation->run() === TRUE)  {

							$update = $this->ion_auth->update($this->c_user->id, $data);
							if ($update) {
								$this->addFlash('Personal Settings Updated', 'success');
								redirect('settings/personal');    
							} else {
								$this->addFlash($this->ion_auth->errors());
							}
						} else {
							if (validation_errors()) {
								$this->addFlash(validation_errors());
							}
						}
			}
        }

        $this->template->set('email', $this->c_user->email);
        $this->template->set('first_name', 
        $this->form_validation->set_value('first_name', $this->c_user->first_name));
        $this->template->set('last_name', 
        $this->form_validation->set_value('last_name', $this->c_user->last_name));

        /*directories*/
        $directories = DM_Directory::get_all_sorted();

        $raw_dir_user = Directory_User::get_by_user($this->c_user->id);
        $user_directories = $raw_dir_user->to_dir_array();

        $is_notified = $raw_dir_user->isNotified();

        CssJs::getInst()->c_js();

        JsSettings::instance()->add(array(
            'autocomplete_directories_url' => site_url('settings/directories/google_autocomplete')
        ));

        $parsers = array();
        foreach ($directories as $_dir) {
            try{
                $parsers[$_dir->id] = Directory_Parser::factory($_dir->type);
            } catch(Exception $e){
                $parsers[$_dir->id] = new stdClass();
            }
        }

        $receive_emails = $this->getAAC()->isGrantedPlan('email_notifications');

        $this->template->set('is_notified', $is_notified);
        $this->template->set('parsers', $parsers);
        $this->template->set('directories', $directories);
        $this->template->set('user_directories', $user_directories);
        $this->template->set('receive_emails', $receive_emails);
        /*end directories*/


        /*google keywords*/
        $this->load->config('site_config', TRUE);
        $keywords_config = $this->config->item('keywords', 'site_config');
        $keywords_count = isset($keywords_config['count']) && $keywords_config['count'] ? $keywords_config['count'] : 10;

        // get user additional info (address)
        $user_additional = User_additional::inst()->get_by_user_id($this->c_user->id);
        // get available keywords
        $keywords = Keyword::inst()->get_user_keywords($this->c_user->id);
        // escape keywords names and website name
        $address_name = isset($address_name)
            ? HTML::chars($address_name)
            : HTML::chars($user_additional->address);

        $keywords_names = isset($keywords_names)
            ? HTML::chars_arr($keywords_names)
            : HTML::chars_arr(array_values($keywords->all_to_single_array('keyword')));

        JsSettings::instance()->add(array(
            'autocomplete_keywords_url' => site_url('settings/keywords/google_autocomplete'),
        ));

        CssJs::getInst()->c_js();

        $this->template->set('address_id', $user_additional->address_id);
        $this->template->set('address_name', $address_name);
        $this->template->set('keywords_names', $keywords_names);
        $this->template->set('keywords_count', $keywords_count);
        /*end google keywords*/

        /*socialmedia settings*/

        $this->load->library('Socializer/socializer');
        $this->load->config('timezones');
        CssJs::getInst()
            ->c_js('settings/socialmedia', 'index')
            ->c_js('settings/socialmedia', 'twitter');
        $tokens = new Access_token();
        $linkedin_data = $tokens->get_linkedin_token($this->c_user->id);
        $this->template->set('linkedin_token', $linkedin_data->id);

        $facebook_data = $tokens->get_facebook_token($this->c_user->id);

        if($facebook_data->id) {
            try {
                $facebook = Socializer::factory('Facebook', $this->c_user->id);
                $user_facebook_pages = $facebook->get_user_pages();
                $this->template->set('fb_pages', $user_facebook_pages);

                $selected_fanpage = Facebook_Fanpage::inst()->get_selected_page($this->c_user->id);
                $this->template->set('selected_fanpage_id', $selected_fanpage->fanpage_id);
            } catch (Exception $e) {
                if ($e->getCode() !== Socializer::FBERRCODE) {
                    $this->addFlash($e->getMessage());
                }
            }
        }
        $this->template->set('facebook_token', $facebook_data->id);

        $twitter_data = $tokens->get_twitter_token($this->c_user->id);
        $this->template->set('twitter_token', $twitter_data->id);

        $youtube_data = $tokens->get_youtube_token($this->c_user->id);
        $this->template->set('youtube_token', $youtube_data->id);

        $google_data = $tokens->get_google_token($this->c_user->id);
        $this->template->set('google_token', $google_data->id);

        $instagram_data = $tokens->get_instagram_token($this->c_user->id);
        $this->template->set('instagram_token', $instagram_data->id);

        $timezones = $this->config->item('timezones');
        $this->template->set('timezones', $timezones);
        $current_timezone = User_timezone::get_user_timezone($this->c_user->id, TRUE);
        $this->template->set('current_timezone', $current_timezone);

        /*end socialmedia settings*/

        /*mention keywords*/
        $this->load->config('site_config', TRUE);
        $keywords_config = $this->config->item('mention_keywords', 'site_config');
        $config_count = (isset($keywords_config['count']) && $keywords_config['count'])
            ? $keywords_config['count']
            : 10;

        $availableKeywordsCount = $this->getAAC()->getPlanFeatureValue('brand_reputation_monitoring');
        if ($availableKeywordsCount) {
            $config_count = $availableKeywordsCount;
        }
        $keywords = Mention_keyword::inst()->get_user_keywords($this->c_user->id);

        JsSettings::instance()->add(array(
            'max_keywords' => $config_count,
        ));

        CssJs::getInst()->add_js(array('libs/handlebar.js', 'libs/handlebars_helpers.js'));

        $this->template->set('keywords', $keywords);
        $this->template->set('config_count', $config_count);
        /*end mention keywords*/

        /*analytics*/
        $analyticsData = array();
        if ($this->session->flashdata('ga_redirect_to_accounts')) {
            if ( ! $this->access_token->token1 OR ! $this->access_token->token2) {
                $analyticsData['error'] = 'Please, connect your Google Analytics account.';

            } else {
                JsSettings::instance()->add(array(
                    'analytics' => array(
                        'get_accounts_url' => site_url('settings/analytics/get_accounts'),
                    ),
                ));
                CssJs::getInst()->c_js();
                $analyticsData['account_info'] = $this->access_token->account_info();
            }


        } else {
            JsSettings::instance()->add(array(
                'analytics' => array(
                    'client_id' => $this->analytics_settings['client_id'],
                    'redirect_uri' => $this->analytics_settings['redirect_uri'],
                ),
            ));
            CssJs::getInst()->c_js();
            $analyticsData['access_token'] = $this->access_token;
            $analyticsData['account_info'] = $this->access_token->account_info();
        }
        $this->template->set('analyticsData', $analyticsData);
        /*end analytics*/
        CssJs::getInst()->add_js('controller/settings/index.js');
        $this->template->render();
    }

}