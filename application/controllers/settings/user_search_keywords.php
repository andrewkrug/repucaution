<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_search_keywords extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('user_search_keywords', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('user_search_keywords', $this->language)
        ]);
    }

    /**
     * Form with mentions keywords list
     */
    public function index() {

        $keywords = User_search_keyword::inst()->get_user_keywords($this->c_user->id, $this->profile->id);

        $new_keywords = array();
        $errors = array();
        $saved_ids = array(0);  // '0' to prevent datamapper error caused by empty array
        $delete = true;

        if ($post = $this->input->post()) {

            unset($post['submit']);

            $grouped = Arr::collect($post);

            foreach ($grouped as $id => $data) {
                if (strpos($id, 'new_') === 0) {
                    $keyword = User_search_keyword::inst()->fill_from_array($data, $this->c_user->id, $this->profile->id);
                    $new_keywords[$id] = $keyword;
                } else {
                    $keyword = User_search_keyword::inst()->fill_from_array($data, $this->c_user->id, $this->profile->id, $id);
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

            if (empty($errors)) {
                if ($delete) {
                    User_search_keyword::inst()->set_deleted($this->c_user->id, $this->profile->id, $saved_ids);
                }
                $this->addFlash(lang('keywords_saved_success'), 'success');
                redirect('settings/user_search_keywords');
            } else {
                $this->addFlash(implode('<br>', Arr::map('strip_tags', $errors)));
            }
        }

        CssJs::getInst()
            ->c_js('settings/user_search_keywords', 'index');

        $configs = Available_config::getByKeysAsArray(array(
            'auto_follow_users_by_search',
            'max_daily_auto_follow_users_by_search'
        ), $this->c_user, $this->profile->id);

        $outp_keywords = array();
        foreach ($keywords as $keyword) {
            $outp_keywords[$keyword->id] = $keyword;
        }
        $outp_keywords = array_merge($outp_keywords, $new_keywords);

        $this->template->set('keywords', $outp_keywords);
        $this->template->set('errors', $errors);
        $this->template->set('configs', $configs);
        $this->template->render();
    }

    public function updateUserConfig() {
        $data['success'] = false;
        $user = new User($this->c_user->id);

        $key = $this->getRequest()->request->get('key', null);
        $configValue = $this->getRequest()->request->get('value', null);
        $token_id = $this->getRequest()->request->get('token_id', null);
        $user->setConfig($key, $configValue, $token_id);

        if (empty($user->error->all)) {
            $data['success'] = true;
            $data['message'] = lang('settings_saved_success');
        } else {
            $data['error'] = $user->error->string;
        }

        echo json_encode($data);
    }

}