<?php

/**
 * User: alkuk
 * Date: 11.03.14
 * Time: 16:32
 */
class influencers_settings extends Admin_Controller
{
    public function __construct() {
        parent::__construct();
        $this->lang->load('influencers_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('influencers_settings', $this->language)
        ]);
    }

    public function index()
    {

        $influencersConditions = new Influencers_condition();
        $influencersConditions->get();

        $this->template->set('influencers_conditions', $influencersConditions);
        $this->template->render();
    }

    public function edit($id)
    {
        $influencersCondition = $this->getModelFromId($id, 'Influencers_condition');

        if ($this->isRequestMethod('POST')) {

            $influencersCondition->value = $this->input->post('value');
            if ($influencersCondition->save()) {
                $text = lang('options_update_success', [$influencersCondition->option_name]);
                $this->addFlash($text, 'success');
                redirect('admin/influencers_settings');
            } else {
                $this->addFlash($influencersCondition->error->string);
            }
        }


        $this->template->set('condition', $influencersCondition);
        $this->template->render();
    }
}
 