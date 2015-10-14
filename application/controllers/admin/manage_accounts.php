<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_accounts extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('manage_users');
        CssJs::getInst()->add_js(array('controller/admin/manage_accounts.js',
                                'controller/admin/manage_users.js',
                                'controller/admin/users_pagination.js',
                                'controller/admin/autocomplete.js'))
                        ->add_js('libs/test.js', null, 'footer');
        $this->lang->load('admin_global_users', $this->language);
        $this->lang->load('manage_accounts', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('manage_accounts', $this->language)
        ]);
    }

    public function index()
    {
        $group = $this->config->item('manager_group', 'ion_auth');
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
        $ownerId = ($this->ion_auth->is_superadmin()) ? null : $this->c_user->id;
        if ($searchText || $filter != '') {
            $managers  = $ia->getUsersByGroup($group);
            if ($managers) {
                $managers->search($searchText, $filter, null, $limit, $offset, $ownerId);
            }
            $searchList = true;
            JsSettings::instance()->add(array('search'=> $searchText,
                'filter' => $filter,
                'group' => $group));
        } else {
            if ($this->ion_auth->is_superadmin()) {
                $managers = $ia->getUsersByGroup($group);
                if ($managers) {
                    $managers->get($limit, $offset);
                }
                $searchList = true;
            } else {
                $managers = new User();
                $managers->getManagerUsers($limit, $offset, $ownerId);
                $searchList = false;
            }

        }
        $this->template->set('searchList', $searchList);
        $this->template->set('users', $managers);
        $this->template->set('group', $group);
        $this->template->set('page', $page);
        $this->template->set('limit', $limit);
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
            $group = $this->config->item('manager_group', 'ion_auth');
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

            if ($success = ($result && $sender->sendInviteMail($params))) {
                $this->addFlash(lang('invite_success'), 'success');
            } else {
                $this->addFlash(lang('invite_error'));
            }
            if ($userId) {
                redirect ('admin/manage_accounts');
            }

            echo json_encode(array('success' => $success));
        }
    }

    public function account($managerId)
    {

        $limit = $this->config->config['users_on_page'];
        $page = (!empty($_GET['page'])) ? $_GET['page'] : 1;
        if ($page==1) {
            $offset ='';
        } else {
            $offset = $limit*($page-1);
        }
        $users = new User($managerId);
        $search = Arr::get($_GET, 'search', '');
        $filter = Arr::get($_GET, 'filter', '');
        $group = $this->config->item('default_group', 'ion_auth');
        if ($filter!='' || $search) {
            $users->search($search, $filter,  null, $limit, $offset, $managerId);
            JsSettings::instance()->add(array('search'=> $search,
                'filter' => $filter,
                'group' => $group));
            $searchList = true;
        } else {
            $users->getManagerUsers($limit, $offset);
            $searchList = false;
        }


        $groupId = $this->ion_auth->getGroupByName($group)->id;
        $members = $this->ion_auth->getUsersByGroup($group);
        if ($members) {
            $members = $members->getFreeUsersDropdown($managerId, $groupId);
        }

        $limit = $this->config->config['users_on_page'];
        $this->template->set('searchList', $searchList);
        $this->template->set('limit', $limit);
        $this->template->set('users', $users);
        $this->template->set('freeusers', $members);
        $this->template->set('page', $page);
        $this->template->set('c_user', $this->c_user);
        $this->template->set('group', $group);
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
        if ($user->delete(array('manager_user' => $manager))) {
            $this->addFlash(lang('user_remove_success'), 'success');
        } else {
            $this->addFlash(lang('user_remove_error'));
        }

        redirect('admin/manage_accounts/account/'.$managerId);
    }

}