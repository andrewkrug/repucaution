<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mention_keywords extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('mention_keywords', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('mention_keywords', $this->language)
        ]);
        $this->template->set('section', 'mention_keywords');
    }

    /**
     * Form with mentions keywords list
     */
    public function index() {

        $this->load->config('site_config', TRUE);
        $keywords_config = $this->config->item('mention_keywords', 'site_config');
        $config_count = (isset($keywords_config['count']) && $keywords_config['count'])
            ? $keywords_config['count'] 
            : 10;

        $availableKeywordsCount = $this->getAAC()->getPlanFeatureValue('brand_reputation_monitoring');
        if ($availableKeywordsCount) {
            $config_count = $availableKeywordsCount;
        }

        $keywords = Mention_keyword::inst()->get_user_keywords(
            $this->c_user->id,
            $this->profile->id
        );

        $new_keywords = array();
        $errors = array();
        $saved_ids = array(0);  // '0' to prevent datamapper error caused by empty array     
        $delete = true;

        if ($post = $this->input->post()) {
            
            unset($post['submit']);

            $grouped = Arr::collect($post);

            if ($availableKeywordsCount && count($grouped) > $config_count) {
                $planError = lang('keywords_count_error', [$config_count]);
                $errors[] = $planError;
                $delete = false;
            } else {
                foreach ($grouped as $id => $data) {
                    if(empty($data['keyword'])) continue;
                    if (strpos($id, 'new_') === 0) {
                        $keyword = Mention_keyword::inst()->fill_from_array(
                            $data,
                            $this->c_user->id,
                            $this->profile->id
                        );
                        $new_keywords[$id] = $keyword;
                    } else {
                        $keyword = Mention_keyword::inst()->fill_from_array(
                            $data,
                            $this->c_user->id,
                            $this->profile->id,
                            $id
                        );
                        if ($keyword->id !== $id) {
                            $new_keywords[$id] = $keyword;
                        }
                    }
                    if ($keyword->save()) {
                        $saved_ids[] = $keyword->id;
                    } else {
                        $errors[$id] = $keyword->error->string;
                    }
                }
            }

            if (empty($errors)) {
                if ($delete) {
                    Mention_keyword::inst()->set_deleted($this->c_user->id, $this->profile->id, $saved_ids);
                }
                $this->addFlash(lang('keywords_saved_success'), 'success');
                redirect('settings/mention_keywords');
            } else {
                $this->addFlash(implode('<br>', Arr::map('strip_tags', $errors)));
            }
        }

        JsSettings::instance()->add(array(
            'max_keywords' => $config_count,
        ));

        CssJs::getInst()->c_js();

        $outp_keywords = array();
        foreach ($keywords as $keyword) {
            $outp_keywords[$keyword->id] = $keyword;
        }
        $outp_keywords = array_merge($outp_keywords, $new_keywords);

        $this->template->set('keywords', $outp_keywords);
        $this->template->set('errors', $errors);
        $this->template->set('config_count', $config_count);
        $this->template->render();
    }

}