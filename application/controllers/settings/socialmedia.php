<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SocialMedia extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('twitter_tools', $this->language);
        $this->lang->load('socialmedia', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('socialmedia', $this->language)
        ]);
    }

    /**
     * Used to show Social Media Settings Page
     * site_url/settings/socialmedia
     * Show Social-connect buttons
     *
     * @access public
     * @return void
     */
    public function index() {
        $this->load->config('timezones');

        CssJs::getInst()
            ->c_js('settings/socialmedia', 'index')
            ->c_js('settings/socialmedia', 'twitter')
            ->add_js(array(
                'masonry-docs.min.js',
                'masonry.pkgd.min.js'
            ))
            ->add_css(array('//fonts.googleapis.com/css?family=Roboto'),'external');

        $type_tokens = Access_token::getGroupTokensArray($this->profile->id, array(), $this->getAAC()->planHasFeature('twitter_marketing_tools'));
        $this->template->set('type_tokens', $type_tokens);
        $this->template->set('has_twitter_marketing_tools', $this->getAAC()->planHasFeature('twitter_marketing_tools'));

        $this->template->render();
    }

    /**
     * Used to remove user access token from our database (access tokens table)
     *
     * @access public
     * @param $id
     */
    public function social_logout($id) {
        try {
            $token = Access_token::inst($id);
            if ($token->user_id != $this->c_user->id) {
                $this->addFlash(lang('account_owner_error'), 'error');
                redirect('settings/socialmedia');
            }
            if ($token->type == 'facebook') {
                Facebook_Fanpage::inst()->get_selected_page($this->c_user->id, $token->id)
                    ->delete();
            }
            $this->profile->delete_access_token($token);
            if(!$token->social_group->count()) {
                $token->delete();
            }
            $this->addFlash(lang('log_out_success'), 'success');
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
        }
        redirect('settings/socialmedia');
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function edit_account($id) {
        $token = Access_token::inst($id);
        if ($token->user_id != $this->c_user->id) {
            $this->addFlash(lang('account_owner_error'), 'error');
            redirect('settings/socialmedia');
        }
        if (!$this->profile->has_account($id)) {
            redirect('settings/socialmedia');
        }
        $available_configs = Available_config::getByTypeAsArray($token->type, []);
        if ($this->input->post()) {
            $errors = array();
            $configs = $this->input->post('config');
            foreach($available_configs as $available_config) {
                $config_key = $available_config['key'];
                $value = isset($configs[$config_key]) ? $configs[$config_key] : '';
                $userConfig = $this->c_user->setConfig($config_key, ($value == 'on') ? true : $value, $token->id);
                if (!$userConfig) {
                    $error_message = preg_replace('|<p>|', '', $userConfig->error->string);
                    $error_message = preg_replace('|</p>|', '<br>', $error_message);
                    $errors[] = $error_message;
                }
            }
            if ($token->type == 'facebook') {
                try {
                    if( $this->input->post('page_group') == '0') {
                        throw new Exception(lang('fanpage_error'));
                    }

                    $this->load->library('Socializer/socializer');
                    /* @var Socializer_Facebook $facebook */
                    $facebook = Socializer::factory('Facebook', $this->c_user->id, $token->to_array());
                    $userdata = $facebook->get_profile();
                    Facebook_Fanpage::inst()->save_selected_page(
                        $this->c_user->id,
                        $this->input->post('page_group'),
                        $userdata['id'],
                        $token->id
                    );
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            if(empty($errors)) {
                $this->addFlash(lang('config_save_success'), 'success');
                redirect('settings/socialmedia');
            } else {
                $this->addFlash(implode('',$errors), 'error');
            }
        }
        if ($token->type == 'facebook') {
            try {
                $this->load->library('Socializer/socializer');
                /* @var Socializer_Facebook $facebook */
                $facebook = Socializer::factory('Facebook', $this->c_user->id, $token->to_array());
                $user_facebook_pages = $facebook->get_user_pages();
                $pages = $user_facebook_pages;

                $selected_fanpage = Facebook_Fanpage::inst()->get_selected_page($this->c_user->id, $token->id);
                $selected_fanpage_id = $selected_fanpage->fanpage_id;

                $this->template->set('pages', $pages);
                $this->template->set('selected_fanpage_id', $selected_fanpage_id);
            } catch (Exception $e) {
                if ($e->getCode() !== Socializer::FBERRCODE) {
                    $this->addFlash($e->getMessage());
                }
            }
        }
        $not_display_configs = [
            'welcome_message_text',

            'days_before_unfollow',

            'auto_favourite_min_favourites_count',
            'auto_favourite_max_favourites_count',
            'auto_favourite_min_retweets_count',
            'auto_favourite_max_retweets_count',

            'auto_retweet_min_favourites_count',
            'auto_retweet_max_favourites_count',
            'auto_retweet_min_retweets_count',
            'auto_retweet_max_retweets_count',

            'max_daily_auto_follow_users_by_search',
            'age_of_account',
            'number_of_tweets'
        ];
        $not_display_configs_values = [];
        foreach($available_configs as &$available_config) {
            if(in_array($available_config['key'], $not_display_configs)) {
                $not_display_configs_values[$available_config['key']] = [
                    'value' => $this->c_user->ifUserHasConfigValue($available_config['key'], $id),
                    'type' => Config::getConfigType($available_config['key'])
                ];
            }
            $available_config['value'] = $this->c_user->ifUserHasConfigValue($available_config['key'], $id);
            $available_config['type'] = Config::getConfigType($available_config['key']);
        }
        $this->template->set('available_configs', $available_configs);
        $this->template->set('not_display_configs', $not_display_configs);
        $this->template->set('not_display_configs_values', $not_display_configs_values);

        CssJs::getInst()
            ->add_js(array(
                'masonry-docs.min.js',
                'masonry.pkgd.min.js'
            ));

        $this->template->set('token', $token);
        $this->template->render();
    }

    public function account_analytics($id) {
        $token = Access_token::inst($id);
        if ($token->user_id != $this->c_user->id) {
            $this->addFlash(lang('account_owner_error'), 'error');
            redirect('settings/socialmedia');
        }
        if (!$this->profile->has_account($id)) {
            redirect('settings/socialmedia');
        }
        CssJs::getInst()
            ->c_js('settings/socialmedia', 'account_analytics');

        CssJs::getInst()->add_js(array(
            'libs/highcharts/highcharts.js'
        ));

        $this->template->set('token', $token);
        $this->template->render();
    }

    public function get_analytics_data() {
        if ($this->template->is_ajax()) {
            $id = $this->input->post('access_token_id');
            $period = $this->input->post('period');
            $to = new DateTime('UTC');
            $from = new DateTime('UTC');
            $from->modify('-'.$period);
            $token = Access_token::inst($id);
            $data = $token
                ->social_analytics
                ->get_by_period($from->format('Y-m-d'), $to->format('Y-m-d'))
                ->all_to_array();
            $answer = [
                Social_analytics::RETWEETS_ANALYTICS_TYPE => [],
                Social_analytics::FAVOURITES_ANALYTICS_TYPE => [],
                Social_analytics::NEW_FOLLOWING_ANALYTICS_TYPE => [],
                Social_analytics::NEW_UNFOLLOWERS_ANALYTICS_TYPE => [],
                Social_analytics::NEW_FOLLOWING_BY_SEARCH_ANALYTICS_TYPE => [],
                Social_analytics::NEW_UNFOLLOWING_ANALYTICS_TYPE => []
            ];
            foreach($data as $el) {
                $answer[$el['type']][$el['date']] = $el['value'];
            }

            $social_values = Social_value::inst();
            $social_values->set_values($this->c_user->id, $this->profile->id, array(
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'type' => 'twitter'
            ));
            $answer['followers'] = $social_values->get_data()['twitter'];
            unset($answer['followers']['']);

            echo json_encode($answer);
        }
    }

    /**
     * Used to save selected Facebook fanpage
     * Available after user connect to facebook (see 'facebook' method)
     *
     * @access public
     * @return void
     */
    public function save_facebook_preferences() {
        $post = $this->input->post();
        try {
            if( ! (isset($post['fan_page_id']) && $post['fan_page_id'] != '0')) {
                throw new Exception(lang('fanpage_error'));
            }

            $this->load->library('Socializer/socializer');
            /* @var Socializer_Facebook $facebook */
            $facebook = Socializer::factory('Facebook', $this->c_user->id);
            $userdata = $facebook->get_profile();
            Facebook_Fanpage::inst()->save_selected_page(
                $this->c_user->id,
                $post['fan_page_id'],
                $userdata['id']
            );
            $this->addFlash(lang('fanpage_save_success'), 'success');
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
        }
        redirect(site_url('settings/socialmedia/'));
    }

    /**
     * Used to connect user to Facebook account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function facebook() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'facebook')) {
                $this->addFlash('This profile already has facebook account. Delete it or choose another profile.', 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Facebook $facebook_socializer */
            $facebook_socializer = Socializer::factory('Facebook', $this->c_user->id);
            $redirect_url = $facebook_socializer->add_new_account($this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            User_additional::inst()->unset_value($this->c_user->id, 'facebook_profile_photo');
            redirect($redirect_url);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage(), 'error');
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to get temporary keys from Twitter
     * Use Socializer Library
     * Set temporary keys to session (in Socializer function)
     *
     * @access public
     * @return void
     */
    public function twitter() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'twitter')) {
                $this->addFlash(lang('already_has_account_error', ['Twitter']), 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Twitter $twitter_socializer */
            $twitter_socializer = Socializer::factory('Twitter', $this->c_user->id);   
            $redirect_uri = $twitter_socializer->set_temporary_credentials();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to connect user to Twitter account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function twitter_callback() {
        try {
            $this->load->library('Socializer/socializer');
            $oauth_verifier = $_REQUEST['oauth_verifier'];
            /* @var Socializer_Twitter $twitter_socializer */
            $twitter_socializer = Socializer::factory('Twitter', $this->c_user->id);
            $redirect_uri = $twitter_socializer->add_new_account($oauth_verifier, $this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to get access url from Google (Youtube and Google have same OAUTH system)
     * Use Socializer Library
     * After - redirect to access url
     *
     * @access public
     * @return void
     */
    public function youtube() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'youtube')) {
                $this->addFlash(lang('already_has_account_error', ['Youtube']), 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $youtube_socializer */
            $youtube_socializer = Socializer::factory('Google', $this->c_user->id);
            $redirect_uri = $youtube_socializer->get_access_url();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to connect user to Youtube(Google) account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function youtube_callback() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $youtube_socializer */
            $youtube_socializer = Socializer::factory('Google', $this->c_user->id);
            $youtube_socializer->add_new_account($this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            redirect(site_url('settings/socialmedia'));
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }
    
    /**
     * Used to get access url from Instagram
     * Use Socializer Library
     * After - redirect to access url
     *
     * @access public
     * @return void
     */
    public function instagram() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'instagram')) {
                $this->addFlash(lang('already_has_account_error', ['Instagram']), 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Instagram $instagram_socializer */
            $instagram_socializer = Socializer::factory('Instagram', $this->c_user->id);
            $redirect_uri = $instagram_socializer->instagramLogin();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to connect user to Instagram account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function instagram_callback() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Instagram $instagram_socializer */
            $instagram_socializer = Socializer::factory('Instagram', $this->c_user->id);
            $instagram_socializer->add_new_account($this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            redirect(site_url('settings/socialmedia'));
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to get access url from Linkedin
     * Use Socializer Library
     * 
     * @access public
     * @return void
     */
    public function linkedin() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'linkedin')) {
                $this->addFlash(lang('already_has_account_error', ['Linkedin']), 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Linkedin $linkedin_socializer */
            $linkedin_socializer = Socializer::factory('Linkedin', $this->c_user->id);
            
            $redirect_uri = $linkedin_socializer->get_access();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to connect user to Linkedin account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function linkedin_callback() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Linkedin $linkedin_socializer */
            $linkedin_socializer = Socializer::factory('Linkedin', $this->c_user->id);

            $linkedin_socializer->add_new_account($this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            redirect(site_url('settings/socialmedia'));
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }

    /**
     * Used to get access url from Google (Youtube and Google have same OAUTH system)
     * Use Socializer Library
     * After - redirect to access url
     *
     * @access public
     * @return void
     */
    public function google() {
        try {
            if(Social_group::hasSocialAccountByType($this->profile->id, 'google')) {
                $this->addFlash(lang('already_has_account_error', ['Google']), 'error');
                redirect('settings/socialmedia');
            }
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google', $this->c_user->id);
            $redirect_uri = $google_socializer->get_access_url();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }
    
    /**
     * Used to connect user to Youtube(Google) account
     * Use Socializer Library
     * Add new record to 'Access tokens' and redirect to settings/socialmedia page
     *
     * @access public
     * @return void
     */
    public function google_callback() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google', $this->c_user->id);
            $google_socializer->add_new_account($this->profile->id);
            $this->addFlash(lang('connected_success'), 'success');
            redirect(site_url('settings/socialmedia'));
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect('settings/socialmedia');
        }
    }
}