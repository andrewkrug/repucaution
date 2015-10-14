<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_admins extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('manage_users');
        CssJs::getInst()->add_js('controller/admin/manage_users.js', 'footer')
            ->add_js(array('controller/admin/users_pagination.js',
                'controller/admin/autocomplete.js',
                 'controller/admin/manage_admins.js'))
            ->add_js('libs/test.js', null, 'footer');
        $this->lang->load('admin_global_users', $this->language);
        $this->lang->load('manage_admins', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('manage_admins', $this->language)
        ]);
    }

    public function index()
    {
        $group = $this->config->item('admin_group', 'ion_auth');
        $ia = new Ion_auth_model();
        $limit = $this->config->config['users_on_page'];
        $page = (!empty($_GET['page'])) ? $_GET['page'] : 1;
        if ($page==1) {
            $offset ='';
        } else {
            $offset = $limit*($page-1);
        }
        $searchText = Arr::get($_GET, 'search', '');
        $filter = Arr::get($_GET, 'filter', '');
        $admins = $ia->getUsersByGroup($group);
        if ( $searchText || $filter !== '') {
            $admins->search($searchText, $filter, null, $limit, $offset);
            JsSettings::instance()->add(array('search'=> $searchText,
                'filter' => $filter,
                'group' => $group));
        } else {

            if ($admins) {
                $admins->get($limit, $offset);
            }
        }
        $this->template->set('users', $admins);
        $this->template->set('group', $group);
        $this->template->set('limit', $limit);
        $this->template->set('page', $page);
        $this->template->set('c_user', $this->c_user);
        $this->template->render();
    }

    public function inviteUser($userId = null)
    {
        if ($userId) {
            $user = new User ($userId);
            $email = $user->email;
        } else {
            $email = $this->input->post('email');
        }
        if ($email) {
            $group = $this->config->item('admin_group', 'ion_auth');
            $adminGroup = $this->ion_auth->getGroupByName($group);
            $result = $this->ion_auth->invite($email, array($adminGroup->id), $userId);
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

            if ($success = ($result && $sender->sendInviteMail($params))) {
                $this->addFlash(lang('invite_success'), 'success');
            } else {
                $this->addFlash(lang('invite_error'));
            }
            if ($userId) {
                redirect ('admin/manage_admins');
            }
            echo json_encode(array('success' => $success));
        }
    }

    public function account($managerId)
    {
        $users = new User($managerId);
        $users->getManagerUsers();
        $members = $this->ion_auth->getUsersByGroup('members');
        $freeusers = $members->getFreeUsersDropdown($managerId);

        $this->template->set('users', $users);
        $this->template->set('freeusers', $freeusers);
        $this->template->set('c_user', $this->c_user);
        $this->template->set('managerAccount', $managerId);
        $this->template->render();
    }

    public function adduser()
    {
        if (!empty($_POST['user']) && !empty($_POST['manager'])) {
            $user = new User($_POST['user']);
            $manager = new User($_POST['manager']);
            $user->save(array('manager_user' => $manager));
            $this->addFlash('User succesfully added!', 'success');
        }
        redirect('admin/manage_accounts/account/'.$_POST['manager']);
    }

    public function removeuser($userId, $managerId)
    {
        $manager = new User($managerId);
        $user = new User($userId);
        $success = $user->delete(array('manager_user' => $manager));
        $message = ($success) ? lang('user_remove_success')
                              : lang('user_remove_error');
        echo json_encode(array(
                                'success' => $success,
                                'message' => $message
        ));
    }

}