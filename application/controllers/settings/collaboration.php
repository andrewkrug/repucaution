<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Collaboration extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('collaboration_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('collaboration_settings', $this->language)
        ]);
        $this->template->set('section', 'collaboration');
    }

    /**
     * Display subscriptions of user
     */
    public function index() 
    {
        CssJs::getInst()->add_js('controller/settings/collaboration.js');

        $users = $this->c_user;
        $managerId = $this->c_user->id;
        $group = $this->config->item('collaborator_group', 'ion_auth');
        $users->getManagerUsers();

        $this->template->set('users', $users);
        $this->template->set('c_user', $this->c_user);
        $this->template->set('group', $group);
        $this->template->set('managerAccount', $managerId);
        $this->template->render();
    }

    public function inviteUser($userId = null)
    {
        $emails = explode(',', $this->input->post('email'));
        foreach ($emails as $email) {
            if ($this->isPaymentEnabled()) {
                $limitCollaborators = $this->getAAC()->getPlanFeatureValue('collaboration_team');
                $teamCount = $this->c_user->getCollaboratorsCount();
                if ($teamCount >= $limitCollaborators) {
                    $this->addFlash(lang('max_users_error'));
                    exit;
                }
            }

            if ($userId) {
                $user = new User ($userId);
                $email = $user->email;
            }/* else {
                $email = $this->input->post('email');
            }*/
            if ($email) {
                $group = $this->config->item('collaborator_group', 'ion_auth');
                $managerGroup = $this->ion_auth->getGroupByName($group);
                $result = $this->ion_auth->invite($email, array($managerGroup->id), $userId);
                if ($result) {
                    if (!$userId) {
                        $user = new User($result['id']);
                        $user->save(array('manager_user' => new User($this->c_user->id)));
                    }

                    if ($user->exists()) {
                        $sender = $this->get('core.mail.sender');
                        $params['to'] = $email;
                        $params['data'] = array('link' => site_url('auth/invite/'.$result['code']),
                            'sitename' => $this->config->config['OCU_site_name']
                        );
                    }
                }

                if ($success = ($result && $sender->sendInviteCollaboratorMail($params))) {
                    $this->addFlash(lang('invite_success'), 'success');
                } else {
                    $this->addFlash(lang('invite_error'));
                }

                echo json_encode(array('success' => $success));
            }
        }
    }

    public function block($user_id = NULL) {

        $user = $this->prepare_user($user_id);
        $ia = $this->ion_auth;

        if ($ia->is_collaborator($user_id) && $this->c_user->isManager($user_id) ) {
            $deactivated = $ia->deactivate($user_id);
            if ($deactivated) {
                $this->addFlash(lang('block_success'), 'success');
                $sender = $this->get('core.mail.sender');
                $sender->sendAdminBlockMail(array('user' => new User($user_id)));
            } else {
                $this->addFlash(lang('block_error', [$this->ion_auth->errors()]));
            }
        } else {
            $this->addFlash(lang('permission_error'));
        }

        redirect('settings/collaboration');
    }

    public function unblock($user_id = NULL) {

        $user = $this->prepare_user($user_id);
        $ia = $this->ion_auth;

        if ($ia->is_collaborator($user_id) && $this->c_user->isManager($user_id) ) {
            $activated = $ia->activate($user_id);
            if ($activated) {
                $this->addFlash(lang('unblock_success'), 'success');
                $sender = $this->get('core.mail.sender');
                $sender->sendAdminBlockMail(array('user' => new User($user_id)));
            } else {
                $this->addFlash(lang('unblock_error', [$this->ion_auth->errors()]));
            }
        } else {
            $this->addFlash(lang('permission_error'));
        }

        redirect('settings/collaboration');
    }

    public function delete($user_id = NULL) {

        $user = $this->prepare_user($user_id);
        $ia = $this->ion_auth;

        if ($ia->is_collaborator($user_id) && $this->c_user->isManager($user_id) ) {

            $this->c_user->delete(new User($user_id));
            $user_deleted = $this->ion_auth->delete_user($user->id);

            if ( ! $user_deleted) {
                $this->addFlash(lang('delete_error', [$this->ion_auth->errors()]));
                redirect('settings/collaboration');
            }
            $sender = $this->get('core.mail.sender');
            $sender->sendUserDeleteMail(array('user' => $user));

            $media = new Media;
            $media->where('user_id', $user->id)->get()->delete_all();

            $post = new Post;
            $post->where('user_id', $user->id)->get();

            foreach ($post as $p) {
                $post_social = new Post_social;
                $post_social->where('post_id', $p->id)->get()->delete_all();
            }

            $post->delete_all();

            $social_post = new Social_post;
            $social_post->where('user_id', $user->id)->get()->delete_all();

            $this->addFlash(lang('delete_success'), 'success');
        } else {
            $this->addFlash(lang('permission_error'));
        }

        redirect('settings/collaboration');
    }

    protected function prepare_user($user_id = NULL) {

        $user_id = intval($user_id);

        $user = $this->ion_auth->user($user_id)->row();
        if ( ! $user_id || empty($user)) {
            $this->addFlash('User not found');
            redirect('settings/collaboration');
        }

        return $user;
    }
}