<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Personal extends MY_Controller
{

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('personal_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('personal_settings', $this->language)
        ]);
        $this->template->set('section', 'personal');
    }

    public function index()
    {

        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');

        if ($this->input->post()) {
            $config = $this->config->config;

            if (!$config['change_settings']) {
                $this->addFlash(lang('demo_error'));
            } else {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                );

                if ($this->input->post('old_password') || $this->c_user->password == 0) {

                    $old_password = $this->input->post('old_password');

                    $ion_model = new Ion_auth_model;
                    if($this->c_user->password == 0) {
                        $valid_old = true;
                    } else {
                        $valid_old = $ion_model->hash_password_db($this->c_user->id, $old_password);
                    }

                    $password_min = $this->config->item('min_password_length', 'ion_auth');
                    $password_max = $this->config->item('max_password_length', 'ion_auth');

                    $this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length['
                        . $password_min . ']|max_length[' . $password_max . ']|matches[confirm_password]');
                    $this->form_validation->set_rules('confirm_password', lang('confirm_new_password'), 'required');

                    if ($valid_old) {
                        $data['password'] = $this->input->post('new_password');
                    } else {
                        $this->form_validation->create_error(lang('invalid_old_password_error'));
                    }
                }

                if ($this->form_validation->run() === true) {

                    $update = $this->ion_auth->update($this->c_user->id, $data);
                    if ($update) {
                        $this->addFlash(lang('personal_settings_updated'), 'success');
                        redirect('settings/personal');
                    } else {
                        $this->addFlash($this->ion_auth->errors());
                    }
                } else {
                    if (validation_errors()) {
                        $this->addFlash(validation_errors());
                    }
                }
            }
        }

        $this->load->config('timezones');
        $timezones = $this->config->item('timezones');
        $this->template->set('timezones', $timezones);
        $current_timezone = User_timezone::get_user_timezone($this->c_user->id, true);
        $this->template->set('current_timezone', $current_timezone);

        $this->template->set('email', $this->c_user->email);
        $this->template->set('first_name',
            $this->form_validation->set_value('first_name', $this->c_user->first_name));
        $this->template->set('last_name',
            $this->form_validation->set_value('last_name', $this->c_user->last_name));

        CssJs::getInst()
            ->add_js(array(
                'controller/settings/index.js',
                'controller/settings/personal/index.js'
            ));
        $this->template->render();
    }

    public function save_timezone()
    {
        if ($this->template->is_ajax()) {
            $response['success'] = false;
            $response['message'] = lang('timezone_error');
            $post = $this->input->post();
            if (isset($post['timezone'])) {
                User_timezone::save_timezone($this->c_user->id, $post['timezone']);
                $response['success'] = true;
                $response['message'] = lang('timezone_success');
            }
            echo json_encode($response);
        }
        exit();
    }

    public function updateLanguage() {
        if ($this->template->is_ajax()) {
            $lang = $this->input->post('language');
            if($this->isDemo()) {
                echo json_encode([
                    'success' => false,
                    'message' => lang('language_demo_error')
                ]);
            } else {
                if($this->c_user) {
                    if(User_language::set_user_language($this->c_user->id, $lang)) {
                        $this->session->set_userdata('language', $lang);
                        echo json_encode([
                            'success' => true,
                            'message' => lang('language_success')
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => lang('language_error')
                        ]);
                    }
                } else {
                    $this->session->set_userdata('language', $lang);
                    echo json_encode([
                        'success' => true,
                        'message' => lang('language_success')
                    ]);
                }
            }
        }
    }
}