<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Options extends Admin_Controller
{

    public function __construct() {
        parent::__construct();
        $this->lang->load('options', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('options', $this->language)
        ]);
    }

    public function index() {
        /** @var System_setting $systemSettingsModel */
        $systemSettingsModel = $this->get('core.system.settings.model');
        $slugs = [
            'trial_enabled'
        ];
        if ($this->isRequestMethod('post')) {
            foreach($slugs as $slug) {
                $systemSettingsModel->setData($slug, 0);
            }
            if($options = $this->getRequest()->request->get('options')) {
                foreach($options as $slug => $option) {
                    $systemSettingsModel->setData($slug, $option);
                }
            }
            $this->addFlash(lang('option_success'), 'success');
        }
        $options = [];
        foreach($slugs as $slug) {
            $options[$slug]= $systemSettingsModel->getData($slug);
        }
        $this->template->set('options', $options);
        $this->template->render();
    }

}