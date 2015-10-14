<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rss extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('rss_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('rss_settings', $this->language)
        ]);
        $this->template->set('section', 'rss');
    }

    /**
     * Rss settings page
     */
    public function index() {

        $rss_custom = Rss_feed::inst()->user_custom_feeds($this->c_user->id, $this->profile->id);

        JsSettings::instance()->add(array(
            'rss' => array(
                'custom_action' => $this->session->flashdata('rss_custom_action'),
                'remove_url' => site_url('settings/rss/remove_rss_custom'),
            ),
        ));
        CssJs::getInst()->c_js();
        $this->template->set('rss_custom', $rss_custom);
        $this->template->render();
    }


    /**
     * Save new custom rss feeds
     * @
     */
    public function update_rss_custom() {
        if ($this->input->is_ajax_request()) {

            $feeds = $this->input->post('custom');

            list($errors, $models) = Rss_feed::validate_rss_custom_pack($feeds);

            if (empty($errors)) {

                Rss_feed::save_rss_custom_pack($models, $this->c_user->id, $this->profile->id);

                $result['success'] = TRUE;
                // page reloads in JS
                $this->addFlash(lang('rss_saved_success'), 'success');
                $this->session->set_flashdata('rss_custom_action', TRUE);
            } else {
                $result['success'] = FALSE;
                $result['errors'] = $errors;
            }

            exit( json_encode($result) );
        }
    }

    public function remove_rss_custom() {
        if ($this->input->is_ajax_request()) {

            $rss_feed_id = $this->input->post('id');

            $deleted = Rss_feed::remove($rss_feed_id, $this->c_user->id, $this->profile->id);

            if ($deleted) {
                $result['success'] = TRUE;
            } else {
                $result['success'] = FALSE;
                $result['errors'] = lang('rss_remove_error');
            }

            exit( json_encode($result) );
        }
    }

}