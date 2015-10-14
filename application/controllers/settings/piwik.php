<?php
/**
 * Created by PhpStorm.
 * User: beer
 * Date: 9.10.15
 * Time: 11.21
 */

class Piwik extends MY_Controller {

    protected $website_part = 'settings';
    protected $piwik_settings;
    protected $piwik = null;

    /**
     * Load config and google access token model
     */
    public function __construct() {
        parent::__construct($this->website_part);
        $this->piwik_settings = Api_key::build_config('piwik');

        $this->lang->load('piwik_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('piwik_settings', $this->language)
        ]);

        if(!empty($this->piwik_settings['domain']) && !empty($this->piwik_settings['token'])){
            $this->piwik = new \VisualAppeal\Piwik(
                $this->piwik_settings['domain'],
                $this->piwik_settings['token'],
                "all"
            );
        }
    }

    public function index() {
        if(!$this->piwik) {
            redirect("settings/piwik/config");
        }
        CssJs::getInst()->add_js(array(
            'controller/settings/piwik/index.js'
        ));
        $sites = $this->piwik->getAllSites();
        $this->template->set('sites', $sites);
        $this->template->set('site_id', $this->c_user->ifUserHasConfigValue('piwik_site_id'));
        $this->template->render();
    }

    public function config() {
        $this->template->render();
    }

}