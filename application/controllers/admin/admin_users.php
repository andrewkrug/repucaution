<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_users extends Admin_Controller {

    protected $format = 'Y-m-d H:i:s';

    public function __construct() {
        parent::__construct();
        $this->config->load('manage_users');
        $this->lang->load('admin_users', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('admin_users', $this->language)
        ]);
    }

    public function index() {
        $group = $this->config->item('default_group', 'ion_auth');
        $limit = $this->config->config['users_on_page'];
        $page = (!empty($_GET['page'])) ? $_GET['page'] : 1;
        if ($page != 1) {
            $offset = $limit*($page-1);
        } else {
            $offset = '';
        }
        $searchText = Arr::get($_GET, 'search', '');
        $filter = Arr::get($_GET, 'filter', '');
        if ( $searchText || $filter !== '') {
            $users =  $this->search();
            JsSettings::instance()->add(array('search'=> $searchText,
                                        'filter' => $filter,
                                        'group' => $group));
        } else {
            $users = $this->ion_auth->getUsersByGroup($group);
            if ($users){
                $users->get($limit, $offset);
            }

        }

        CssJs::getInst()
            ->add_js('controller/admin/manage_users.js', 'footer')
            ->add_js(array(
                'controller/admin/users_pagination.js',
                'controller/admin/autocomplete.js'
            ))
            ->add_js('libs/test.js', null, 'footer');
        $this->template->set('users', $users);
        $this->template->set('group', $group);
        $this->template->set('limit', $limit);
        $this->template->set('page', $page);
        $this->template->set('c_user', $this->c_user);
        $this->template->render();
    }

    public function block($user_id = NULL) {

        $user = $this->prepare_user($user_id);
        $ia = $this->ion_auth;

        if ($ia->is_admin($user_id)) {
            $url = 'manage_admins';
        } elseif ($ia->is_manager($user_id)) {
            $url = 'manage_accounts';
        } else {
            $url = 'admin_users';
        }

        $deactivated = $ia->deactivate($user_id);
        if ($deactivated) {
            $this->addFlash(lang('block_success'), 'success');
            $sender = $this->get('core.mail.sender');
            $sender->sendAdminBlockMail(array('user' => new User($user_id)));
        } else {
            $this->addFlash(lang('block_error', [$this->ion_auth->errors()]));
        }


        redirect('admin/'.$url);
    }

    public function unblock($user_id = NULL) {

        $user = $this->prepare_user($user_id);
        $ia = $this->ion_auth;

        if ($ia->is_admin($user_id)) {
            $url = 'manage_admins';
        } elseif ($ia->is_manager($user_id)) {
            $url = 'manage_accounts';
        } else {
            $url = 'admin_users';
        }

        $activated = $ia->activate($user_id);
        if ($activated) {
            $this->addFlash(lang('unblock_success'), 'success');
            $sender = $this->get('core.mail.sender');
            $sender->sendAdminBlockMail(array('user' => new User($user_id)));
        } else {
            $this->addFlash(lang('unblock_error', [$this->ion_auth->errors()]));
        }

        redirect('admin/'.$url);
    }

    public function password($user_id = NULL) {

        $user = $this->prepare_user($user_id, TRUE);
    
        $password_min = $this->config->item('min_password_length', 'ion_auth');
        $password_max = $this->config->item('max_password_length', 'ion_auth');

        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[' 
            . $password_min . ']|max_length[' . $password_max . ']|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required');

        if ($this->form_validation->run() === TRUE)  {

            $data = array(
                'password' => $this->input->post('new_password'),
            );

            $update = $this->ion_auth->update($user->id, $data);
            if ($update) {
                $this->addFlash(lang('password_change_success'), 'success');
                redirect('admin/admin_users');    
            } else {
                $this->addFlash($this->ion_auth->errors());
            }
        } else {
            if (validation_errors()) {
                $this->addFlash( validation_errors());
            }
        }

        $this->template->set('user', $user);
        $this->template->render();
    }

    public function delete($user_id = NULL) {

        $user = $this->prepare_user($user_id);

        if ($this->ion_auth->is_admin($user_id)) {
            $url = 'manage_admins';
        } elseif ($this->ion_auth->is_manager($user_id)) {
            $url = 'manage_accounts';
        } else {
            $url = 'admin_users';
        }

        if ($this->ion_auth->is_collaborator($user_id)) {
            $this->c_user->delete($user);
        }

        $user_deleted = $this->ion_auth->delete_user($user->id);

        if ( ! $user_deleted) {
            $this->addFlash(lang('delete_error', [$this->ion_auth->errors()]));
            redirect('admin/admin_users');
        }
        $sender = $this->get('core.mail.sender');
        $sender->sendUserDeleteMail(array('user' => $user));

        $access_token = new Access_token;
        $access_token->where('user_id', $user->id)->get()->delete_all();

        $directory_user = new Directory_User;
        $directory_user->where('user_id', $user->id)->get()->delete_all();

        $facebook_fanpage = new Facebook_Fanpage;
        $facebook_fanpage->where('user_id', $user->id)->get()->delete_all();

        $keyword = new Keyword;
        $keyword->where('user_id', $user->id)->get();

        foreach($keyword as $k) {
            $keyword_rank = new Keyword_rank;
            $keyword_rank->where('keyword_id', $k->id)->get()->delete_all();
        }

        $keyword->delete_all();

        $media = new Media;
        $media->where('user_id', $user->id)->get()->delete_all();

        $post = new Post;
        $post->where('user_id', $user->id)->get();

        foreach ($post as $p) {
            $post_social = new Post_social;
            $post_social->where('post_id', $p->id)->get()->delete_all();
        }

        $post->delete_all();

        $review = new Review;
        $review->where('user_id', $user->id)->get()->delete_all();

        $reviews_notification = new Reviews_notification;
        $reviews_notification->where('user_id', $user->id)->get()->delete_all();

        $rss_feeds_users = new Rss_feeds_users;
        $rss_feeds_users->where('user_id', $user->id)->get()->delete_all();

        $social_post = new Social_post;
        $social_post->where('user_id', $user->id)->get()->delete_all();

        $social_value = new Social_value;
        $social_value->where('user_id', $user->id)->get()->delete_all();

        $user_additional = new User_additional;
        $user_additional->where('user_id', $user->id)->get()->delete_all();

        $user_feed = new User_feed;
        $user_feed->where('user_id', $user->id)->get()->delete_all();

        $user_timezone = new User_timezone;
        $user_timezone->where('user_id', $user->id)->get()->delete_all();

        $this->addFlash(lang('delete_success'), 'success');

        redirect('admin/'.$url);
    }

    public function search()
    {
        $limit = $this->config->config['users_on_page'];
        $page = (!empty($_GET['page'])) ? $_GET['page'] : 1;

        if ($page == 1) {
            $offset='';
        } else {
            $offset=($page-1)*$limit;
        }
        $searchText = Arr::get($_GET, 'search', '');
        $filter = Arr::get($_GET, 'filter', '');
        $group = Arr::get($_GET, 'group', '');

        $users = $this->searchUsers($searchText, $filter, $group, $limit, $offset);

        return $users;

    }

    public function autocomplete()
    {
        $searchText = Arr::get($_POST, 'search', '');
        $filter = Arr::get($_POST, 'filter', '');
        $group = Arr::get($_POST, 'group', '');
        $users = $this->searchUsers($searchText, $filter, $group, 10, '', 'after');
        if ($users->exists()) {
            echo $this->template->block('users', 'admin/admin_users/blocks/autocomplete.php', array('users' => $users,
                                                                                                    'c_user' => $this->c_user,
                                                                                                    'group' => $_POST['group']
            ));
        }
    }

    public function profile($userId)
    {
        if (!empty($userId)) {
            $user = new User($userId);
            $this->template->set('email', $user->email);
            $this->template->set('firstName', $user->first_name);
            $this->template->set('lastName', $user->last_name);
            $this->template->set('created', date($this->format, $user->created_on));
            $this->template->set('lastLogin', date($this->format, $user->last_login));
            $this->template->render();
        } else {
            redirect('admin/admin_users');
        }

    }

    protected function searchUsers($searchText, $filter, $group, $limit, $offset = '', $place = null)
    {

        $user = $this->ion_auth->getUsersByGroup($group);
        $managerGroup = $this->config->item('manager_group', 'ion_auth');
        $ownerId = null;
        if (!$this->ion_auth->is_superadmin() && $group == $managerGroup) {
            $ownerId = $this->c_user->id;
        }
        if ($manager = Arr::get($_POST, 'manager', '')) {
            $ownerId = $manager;
        }

        return $user->search($searchText, $filter, $place, $limit, $offset, $ownerId);

    }

    protected function prepare_user($user_id = NULL, $password = FALSE) {

        $user_id = intval($user_id);

        $user = $this->ion_auth->user($user_id)->row();
        if ( ! $user_id || empty($user)) {
            $this->addFlash(lang('user_not_found_error'));
            redirect('admin/admin_users');
        }

        if ( ! $password) {
            if ($user->id === $this->c_user->id) {
                $this->addFlash(lang('admin_user_error'));
                redirect('admin/admin_users');   
            }
        }

        return $user;
    }
    
}