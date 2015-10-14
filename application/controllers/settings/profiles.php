<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profiles extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('profiles_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('profiles_settings', $this->language)
        ]);
    }

    /**
     * Used to show Social Media Settings Page
     * site_url/settings/socialmedia
     * Show Social-connect buttons
     *
     * @access public
     * @return void
     */
    public function index() {

        CssJs::getInst()
            ->c_js('settings/profiles', 'index');

        $groups = $this->c_user->social_group->get();
        $this->template->set('groups', $groups);

        $this->template->render();
    }

    /**
     * @param integer $id
     */
    public function edit_profile($id = null) {
        $groupsCount = $this->c_user->social_group->count();
        $availableProfilesCount = $this->getAAC()->getPlanFeatureValue('profiles_count');
        if (!$availableProfilesCount) {
            $availableProfilesCount = 1;
        }
        if($groupsCount >= $availableProfilesCount && !$id) {
            $this->addFlash(lang('profiles_count_error'), 'error');
            redirect('settings/profiles');
        }
        $group = new Social_group($id);
        if ($group->exists() && $group->user_id != $this->c_user->id) {
            $this->addFlash(lang('profile_owner_error'), 'error');
            redirect('settings/profiles');
        }
        if ($this->input->post()) {
            $group->name = $this->input->post('group_name');
            $group->description = $this->input->post('group_description');
            $group->user_id = $this->c_user->id;
            if ($group->save()) {
                $this->addFlash(lang('profile_saved_successfully'), 'success');
                redirect('settings/profiles');
            } else {
                $error_message = preg_replace('|<p>|', '', $group->error->string);
                $error_message = preg_replace('|</p>|', '<br>', $error_message);
                $this->addFlash($error_message, 'error');
            }
        }

        $this->template->set('group', $group);
        $this->template->render();
    }

    /**
     * Used to remove social group from our database.
     *
     * @access public
     * @param $id
     */
    public function delete_group($id) {
        try {
            $group = new Social_group($id);
            if ($group->user_id != $this->c_user->id) {
                $this->addFlash('It`s not your group.', 'error');
                redirect('settings/profiles');
            }
            $group->delete();
            $this->addFlash(lang('profile_deleted_successfully'), 'success');
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
        }
        redirect('settings/profiles');
    }

    public function changeActive() {
        if ($this->template->is_ajax()) {
            $post = $this->input->post();
            $social_groups = $this->c_user->social_group->get();
            $result = array(
                'success' => true
            );
            foreach($social_groups as $social_group) {
                $social_group->is_active = false;
                $social_group->save();
            }
            $social_group = new Social_group($post['id']);
            if($social_group->exists()) {
                $social_group->is_active = true;
                if($social_group->save()) {
                    $result['message'] = lang('profile_updated_successfully');
                } else {
                    $result['success'] = false;
                    $result['message'] = lang('profile_saving_error');
                }
            } else {
                $result['success'] = false;
                $result['message'] = lang('profile_saving_error');
            }
            echo json_encode($result);
        }
    }
}