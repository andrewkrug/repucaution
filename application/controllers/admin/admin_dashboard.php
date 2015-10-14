<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_dashboard extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load('admin_dashboard', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('admin_dashboard', $this->language)
        ]);
    }

    public function index() {      

        $has_empty_api_keys = Api_key::has_empty();
        $cache_dir = APPPATH . 'cache';

        $dashboardLinks = array(
            'admin/admin_users' => lang('manage_customers'),
            'admin/manage_accounts' => lang('account_managers'),
            'admin/admin_api' => lang('api_keys'),
            'admin/manage_plans' => lang('plans_management'),
            'admin/payment_settings' => lang('payment_settings'),
            'admin/influencers_settings' => lang('influencers_settings'),
            'admin/transactions' => lang('payment_transactions'),
            'admin/social_settings' => lang('social_settings'),
        );
        if ($this->ion_auth->is_superadmin()) {
            $dashboardLinks['admin/manage_admins'] = lang('admins_management');
            $dashboardLinks['admin/mailchimp'] = lang('export_to_mailchimp');
        }

//        $this->template->set('has_empty_api_keys', $has_empty_api_keys);
//        $this->template->set('cache_dir', $cache_dir);
        $this->template->set('dashboard_links', $dashboardLinks);
        if ($has_empty_api_keys){
            $message = lang('api_keys_error', [site_url('admin/admin_api')]);
            $this->addFlash($message);
        }
        if ( ! is_writable($cache_dir)){
            $message = lang('cache_error', [$cache_dir]);
            $this->addFlash($message);
        }
        $this->template->render();
    }
    
}