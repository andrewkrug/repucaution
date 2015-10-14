<?php
/**
 * User: dev
 * Date: 16.01.14
 * Time: 15:06
 */

require_once __DIR__ . '/Base_Controller.php';

class MY_Controller extends Base_Controller
{

    /**
     * @var null|User
     */
    public $c_user = null;

    public $language;
    public $language_small;

    /**
     * @var null|Social_group
     */
    public $profile = null;
    protected $website_part = '';
    private $paymentsEnabled;
    private $trialEnabled;

    /**
     * @var bool
     */
    private $disableConfigChanges;

    /**
     * @var string
     */
    protected $demoConfigName;

    /**
     * @param null|string $website_part
     */
    public function __construct($website_part = null)
    {
        parent::__construct();
        $this->demoConfigName = 'demo_settings';

        //Load config file only for demo
        $this->config->load($this->demoConfigName, true, true);
        $this->disableConfigChanges = (bool)$this->config->item('disable_config_changes', $this->demoConfigName);

        $this->config->load('languages');
        if(!$this->language) {
            $this->language = $this->config->config['language'];
        }

        $this->c_user = $this->getUser();
        $this->load->library('session');
        if(!$this->isDemo()) {
            if($this->c_user) {
                $lang = User_language::get_user_language($this->c_user->id, $this->session->userdata('language'));
            } else {
                $lang = $this->session->userdata('language');
                if(!$lang) {
                    $lang = 'en';
                }
            }
        } else {
            $lang = 'en';
        }
        $this->language = $this->config->config['languages'][$lang];
        $this->language_small = $lang;
        $this->session->set_userdata('language', $lang);
        $this->template->set('default_language', $this->language);
        $this->template->set('available_languages', $this->config->config['languages']);

        $this->load->helper('my_language_helper');
        $this->load->helper('language');
        $this->lang->load('global', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('global', $this->language)
        ]);

        /**
         * Payments enabled block (send var to layout - used for changing 'Register link')
         */
        $this->paymentsEnabled = $this->get('core.status.system')->isPaymentEnabled();
        $this->trialEnabled = $this->get('core.status.system')->isTrialEnabled();

        if($this->router->fetch_method() !== 'updateLanguage') {
            // AUTO LOGIN START
            $noAuth = $this->config->item('no_auth', $this->demoConfigName);
            if ($noAuth !== false) {
                $this->autoLoginUser();
            }
            // AUTO LOGIN END

            if (!$this->ion_auth->logged_in()
                && $this->router->fetch_class() !== 'auth'
                && $this->router->fetch_class() !== 'payment'
                && $this->router->fetch_class() !== 'subscript'
            ) {
                redirect('auth');
            }
            if ($this->ion_auth->logged_in()
                && $this->router->fetch_class() !== 'auth'
                && $this->router->fetch_method() !== 'logout'
            ) {

            if (!$this->c_user) {
                $message = $this->ion_auth->is_collaborator() ?
                                                                'Your major user account is inactive' :
                                                                'Authentication error';
                $this->ion_auth->logout();
                $this->addFlash($message);
                redirect('auth');
            }

            $this->c_user->user_name = $this->c_user->first_name . ' ' . $this->c_user->last_name;

            if ($dropdownManagerUsers = $this->ion_auth->checkManagerCodeActuality()) {
                $this->template->set('dropdownManagerUsers', $dropdownManagerUsers);
                $this->template->set('currentId', $this->c_user->id);
                CssJs::getInst()->add_js(array('controller/manager/manager_header.js'));
            }

                if ($this->ion_auth->is_manager()) {

                    if ($this->router->fetch_directory() !== 'manager/') {
                        redirect('manager');
                    }
                } elseif ($this->ion_auth->is_admin()) {
                    if (!$this->ion_auth->is_superadmin()
                        && $this->router->fetch_class() == 'manage_admins'
                    ) {
                        redirect('admin');
                    }
                    if ($this->router->fetch_directory() !== 'admin/') {
                        redirect('admin');
                    }

                } else {

                    if ($this->router->fetch_directory() === 'admin/') {
                        redirect('/');
                    }

                    if ($this->router->fetch_directory() === 'manager/' && !$dropdownManagerUsers) {
                        redirect('/');
                    }
                    $activeProfile = $this->c_user->social_group->getActive($this->c_user->id);
                    if($activeProfile) {
                        $this->profile = $activeProfile;
                        $this->template->set('active_profile', $this->profile);
                        $this->template->set('profiles', $this->c_user->social_group->get());
                    } elseif($this->router->fetch_class() !== 'profiles'
                        && $this->router->fetch_directory() !== 'settings') {

                        $this->addFlash('Please select active profile or add it.');
                        redirect('/settings/profiles');
                    }

                    /**
                     * Redirect to subscriptions plans if user not have active payment
                     */
                    if ($this->paymentsEnabled && !$this->ion_auth->is_admin() && !$this->ion_auth->is_manager() && !$this->ion_auth->getManagerCode()) {
                        $this->checkActiveSubscription($this->c_user->id);
                    }

                    if (isset($website_part)) {
                        $this->website_part = $website_part;
                    }

                    $this->template->set('website_part', $this->website_part);

                }
            }
            $this->template->set('c_user', $this->c_user);

            $need_info = false;
            if ($this->router->fetch_directory() == 'settings/'
                && $this->c_user->social_group->count() > 1) {

                $availableProfilesCount = $this->getAAC()->getPlanFeatureValue('profiles_count');
                if ($availableProfilesCount || $availableProfilesCount != 1) {
                    $need_info = true;
                }
            }
            $this->template->set('need_info', $need_info);

            //for demo site
            $this->configChangePermissionsCheck();
            $this->bitly_load();
        }

        $this->template->set('breadcrumbs', true);

        $this->template->layout = 'layouts/customer';
        if ('settings' == $this->website_part) {
            $this->template->layout = 'layouts/customer_settings';
        }
        $this->piwik_load();
    }


    /**
     * load bit.ly library + set configs
     */
    protected function bitly_load()
    {
        $bitly_config = Api_key::build_config('bitly');
        if (isset($bitly_config['username'], $bitly_config['apikey'])) {
            $args = array(
                'login' => $bitly_config['username'],
                'apiKey' => $bitly_config['apikey'],
            );

            $this->load->library('bitly', $args);
        }

    }

    protected function piwik_load() {
        $piwik_config = Api_key::build_config('piwik');
        if (isset($piwik_config['domain'], $piwik_config['site_id'])) {
            $this->template->set('piwik_enabled', true);
            $this->template->set('piwik_domain', $piwik_config['domain']);
            $this->template->set('piwik_site_id', $piwik_config['site_id']);
        } else {
            $this->template->set('piwik_enabled', false);
        }
    }


    /**
     * Allow site mode: this mode not don't require user's authentication
     */
    protected function autoLoginUser()
    {
        $userEmail = 'admin@admin.com';

        $user = User::findByEmail($userEmail);
        if (!$user) {
            return;
        }
        //force logout the user if his id isn't equal autologin user's id
        if (!$this->c_user || !$this->c_user->id || !$this->c_user->id === $user->id) {
            $this->ion_auth->logout();
        }

        if (!$this->ion_auth->logged_in()) {
            $this->ion_auth->loginForce($user->email, true);
            redirect('/');
        }
    }

    /**
     * Check if payment is enabled
     *
     * @access protected
     * @return bool
     */
    protected function isPaymentEnabled()
    {
        return $this->paymentsEnabled;
    }

    /**
     * Check if payment is enabled
     *
     * @access protected
     * @return bool
     */
    protected function isTrialEnabled()
    {
        return $this->trialEnabled;
    }

    /**
     * Check is configs are changeable
     *
     * @return bool
     */
    protected function isConfigChangeable()
    {
        return !$this->disableConfigChanges;
    }

    /**
     * Return true if this is demo site
     *
     * @return bool
     */
    protected function isDemo() {
        if ($this->isConfigChangeable()) {
            return false;
        }
        if($this->config->item('forbidden_parts_of_site', $this->demoConfigName)) {
            return true;
        } else {
            return false;
        }
    }


    //TODO clear code in this method
    /**
     * Redirect to link if user forbidden part of site
     *
     * @param null|string $redirectTo (to redirect)
     */
    protected function configChangePermissionsCheck($redirectTo = null)
    {

        if ($this->isConfigChangeable()) {
            return;
        }

        if (!$redirectTo) {

            $directory = trim($this->router->fetch_directory(), '/');
            $class = $this->router->fetch_class();
            $method = $this->router->fetch_method();

            $requestMethod = $this->input->server('REQUEST_METHOD');

            $forbiddenParts = $this->config->item('forbidden_parts_of_site', $this->demoConfigName);

            $referer = $this->input->server('HTTP_REFERER');

            $defaultRedirectLink = empty($referer) ? base_url().uri_string() : $referer;

            if (!$forbiddenParts || !is_array($forbiddenParts)) {
                return;
            }

            if (!empty($directory)) {
                if (empty($forbiddenParts[$directory])) {
                    return;
                }

                $forbiddenDirectory = $forbiddenParts[$directory];

                if (!empty($forbiddenDirectory['redirect'])) {
                    $defaultRedirectLink = $forbiddenDirectory['redirect'];
                }

                if (!empty($forbiddenDirectory['classes'])) {
                    $forbiddenParts = $forbiddenDirectory['classes'];
                }

            }


            if (!empty($forbiddenDirectory['allowed_request_methods']) &&
                is_array($forbiddenDirectory['allowed_request_methods']) &&
                !in_array($requestMethod, $forbiddenDirectory['allowed_request_methods'])) {

                $redirectTo = $defaultRedirectLink;

            }else {

                $newRedirectUrl = isset($forbiddenPartsClass['redirect']) ? site_url(
                    $forbiddenPartsClass['redirect']
                ) : $defaultRedirectLink;

                if (empty($forbiddenParts[$class])) {
                    return;
                }

                $forbiddenPartsClass = $forbiddenParts[$class];

                if (!empty($forbiddenPartsClass['allowed_request_methods']) &&
                    is_array($forbiddenPartsClass['allowed_request_methods']) &&
                    !in_array($requestMethod, $forbiddenPartsClass['allowed_request_methods'])
                ) {
                    $redirectTo = $newRedirectUrl;
                } elseif (!empty($forbiddenPartsClass['sub_paths']) &&
                    is_array($forbiddenPartsClass['sub_paths'])
                ) {
                    $forbiddenSubPaths = $forbiddenPartsClass['sub_paths'];
                    if (isset($forbiddenSubPaths[$method]) &&
                        is_array($forbiddenSubPaths[$method])
                    ) {
                        //TODO complete this functional
                    } elseif (in_array($method, $forbiddenSubPaths)) {
                        $redirectTo = $newRedirectUrl;
                    }
                } elseif (!empty($forbiddenPartsClass['allowed_methods']) &&
                    is_array($forbiddenPartsClass['allowed_methods']) &&
                    !in_array($method, $forbiddenPartsClass['allowed_methods'])
                ) {
                    $redirectTo = $newRedirectUrl;
                }

            }


        }


        if (!$redirectTo) {
            return;
        }

        $this->template->set_message("This is the demo site and you can't change any settings!", "error");
        redirect($redirectTo);
    }
    
    /**
     * Check if user has an active payment
     *
     * @param int $userId   
     */
    protected function checkActiveSubscription($userId)
    {
        $user = new User($userId);
        if (!$user->hasActiveSubscription() && $this->router->fetch_class() != 'payment' && $this->router->fetch_class() != 'subscript') {
            if ($this->ion_auth->is_collaborator()) {
                $this->addFlash('Your major user don`t have active subscription');
                redirect ('auth/login');
            } else {
                $this->template->set('no_subscription', true);
                $this->template->set('last_plan_id', $this->c_user->getLastSubscription()->plan_id);
            }

        }    
    }
	
	/**
     * Push to stdout json encoded data with content: application/json header
     *
     * @param array $data
     */
    protected function renderJson($data = array())
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * 	Check if request is XMLHttpRequest
     *  @return bool
     */

   protected function isAjax()
   {
        return $this->input->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
   }

}