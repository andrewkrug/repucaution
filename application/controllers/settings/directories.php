<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Directories extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('directories_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('directories_settings', $this->language)
        ]);
        $this->template->set('section', 'directories');
    }

    public function index(){

        if( !empty($_POST) ){
            $this->save_links();
        }

        $directories = DM_Directory::get_all_sorted();

        $raw_dir_user = Directory_User::get_by_user_and_profile($this->c_user->id, $this->profile->id);
        $user_directories = $raw_dir_user->to_dir_array();

        $is_notified = $raw_dir_user->isNotified();

        CssJs::getInst()->c_js();

        JsSettings::instance()->add(array(
            'autocomplete_url' => site_url('settings/directories/google_autocomplete')
        ));

        $parsers = array();
        foreach ($directories as $_dir) {
            try{
                $parsers[$_dir->id] = Directory_Parser::factory($_dir->type);
            } catch(Exception $e){
                $parsers[$_dir->id] = new stdClass();
            }
        }

        $receive_emails = $this->getAAC()->isGrantedPlan('email_notifications');

        $this->template->set('is_notified', $is_notified);
        $this->template->set('parsers', $parsers);
        $this->template->set('directories', $directories);
        $this->template->set('user_directories', $user_directories);
        $this->template->set('receive_emails', $receive_emails);

        $this->template->render();
    }

    protected function save_links(){
        $directories = (array) $this->input->post('directory');
        $directories = array_filter($directories);
        $notify = (int)(bool)$this->input->post('email_notify');

        $additions = array(
            'notify' => $notify
        );

        $errors = Directory_User::update_user_dir($this->c_user->id, $this->profile->id, $directories, $additions);
        if(!empty($errors)){
            $this->addFlash(implode('<br>',$errors));
        }else{
            $this->addFlash(lang('directories_successfully_saved'), 'success');
        }

        $this->get('core.job.queue.manager')->addUniqueJob('tasks/reviews_task/addByUser',  array($this->c_user->id));
        redirect( 'settings/directories' );
    }

    public function google_autocomplete(){
        $term = $this->input->get('term');
        $term = trim($term);

        $type = 'Google_Places';

        if(empty($term) || !DM_Directory::isActiveByType($type)){
            exit;
        }

        $data = array();

        try{
            $google_paces = Directory_Parser::factory($type);
            $data = $google_paces->autocomplete($term);
        } catch (Exception $e){
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    }

}