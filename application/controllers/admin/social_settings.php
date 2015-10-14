<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Social_settings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load('twitter_tools', $this->language);
        $this->lang->load('social_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_settings', $this->language)
        ]);
    }

    public function index() {
        $types = Access_token::$types_with_tools;
        $configs = Config::findAll()->all_to_array();

        $available_configs = array();

        foreach($types as $type) {
            foreach($configs as $config) {
                $available_config = Available_config::create()
                    ->where('type', $type)
                    ->where('config_id', $config['id'])
                    ->get(1);
                $config['is_enable'] = ($available_config->exists()) ? true : false;
                $available_configs[$type][$config['id']] = $config;
            }
        }

        $post = $this->input->post();

        if (!empty($post)) {
            $old_available_configs = Available_config::findAll();
            foreach($old_available_configs as $old_available_config) {
                $old_available_config->delete();
            }
            foreach($post as $_type => $configs) {
                foreach($configs as $config_id => $value) {
                    $new_available_config = Available_config::create();
                    $new_available_config->type = $_type;
                    $new_available_config->config_id = $config_id;
                    $new_available_config->save();
                }
            }

            $this->addFlash(lang('social_update_success'), 'success');
            redirect('admin/social_settings');
        }

        $this->template->set('available_configs', $available_configs);
        $this->template->render();
    }

}