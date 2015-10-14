<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mailchimp extends Admin_Controller
{
    public function __construct() {
        parent::__construct();
        $this->lang->load('mailchimp', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('mailchimp', $this->language)
        ]);
    }

    public function index()
    {
        $request = $this->getRequest();
        $mailchimp = $this->get('core.mailchimp.manager');

        if ($request->isMethod('post')) {
            $lists = $request->request->get('lists');
            $groups = $request->request->get('groups');
            if (!empty($lists) && !empty($groups)) {
                try {
                    $result = $mailchimp->exportEmails($groups, $lists);
                    foreach ($result as $message) {
                        $this->addFlash($message, 'success');
                    }
                } catch (Exception $e) {
                    $this->addFlash($e->getMessage());
                }
            } else {
                $this->addFlash(lang('not_selected_error'));
            }
        }

        $lists = $mailchimp->getLists();
        $groups = Group::findAll();
        $this->template->set('groups', $groups);
        $this->template->set('lists', $lists);
        $this->template->render();
    }
}