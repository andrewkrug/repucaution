<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Paypal_settings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $post = $this->input->post();
        $ppSettings = new Paypal_api_key(1);

        $this->form_validation->set_rules('user', 'User', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('signature', 'Signature', 'required');

        if (!empty($post)) {
            if ($this->form_validation->run() !== false) {

                $ppSettings->user = $post['user'];
                $ppSettings->password = $post['password'];
                $ppSettings->signature = $post['signature'];
                $ppSettings->sandbox_mode = 0;

                if(isset($post['sandbox_mode'])) {
                    $ppSettings->sandbox_mode = 1;
                }

                if($ppSettings->save()) {
                    $this->addFlash('Settings successfully updated', 'success');
                    redirect('admin/paypal_settings');
                }
            } else {
                $this->addFlash(validation_errors());
                redirect('admin/paypal_settings');
            }
        }

        $user = isset($ppSettings->user) ? $ppSettings->user : '';
        $password = isset($ppSettings->password) ? $ppSettings->password : '';
        $signature = isset($ppSettings->signature) ? $ppSettings->signature : '';
        $isSandbox = isset($ppSettings->sandbox_mode) ? $ppSettings->sandbox_mode : true;


        $this->template->set('ppUser', $user);
        $this->template->set('ppPassword', $password);
        $this->template->set('ppSignature', $signature);
        $this->template->set('isSandbox', $isSandbox);

        $this->template->render();
    }

}