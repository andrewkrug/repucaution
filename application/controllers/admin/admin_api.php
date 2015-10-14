<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_api extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load('admin_api', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('admin_api', $this->language)
        ]);
    }

    public function index() {

        if ( ! empty($_POST)) {

            if ($this->isConfigChangeable()) {
                foreach ($_POST as $post_key => $value) {
                    $parts = explode('/', $post_key);
                    $social = $parts[0];
                    $key = $parts[1];

                    Api_key::inst()
                        ->where(array(
                                'social' => $social,
                                'key' => $key,
                            ))
                        ->update('value', $value ? $value : NULL);


                }
                $this->addFlash(lang('api_update_success'), 'success');
            } else {

            }


        }

        $api_keys = Api_key::inst()->get();

        $this->template->set('api_keys', $api_keys);
        $this->template->render();
    }
    
}