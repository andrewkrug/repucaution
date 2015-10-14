<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Manager_route extends MY_Controller {

    protected $website_part = 'dashboard';

    public function __construct() {
        parent::__construct($this->website_part);
    }

    public function index()
    {
        if (!$code = $this->ion_auth->getManagerCode()) {
            $code = $this->ion_auth->createManagerCode();
            $this->c_user->manager_code = $code;
            $this->c_user->save();
        }
        if ($manager = $this->ion_auth->codeManagerExist($code)) {
            $managerUsers = $manager->getManagerUsers();
            if ($managerUsers->exists()) {
                $managerLoginAs = (int)$manager->manager_login_as;
                $attachedUserIds = $managerUsers->all_to_single_array('id');

                $userId = (in_array($managerLoginAs, $attachedUserIds)) ? $managerLoginAs : $managerUsers->user_id;
                if ($this->c_user->id !== $userId) {
                    $this->login($userId);
                }

            } else {
/*                $admin = new User($this->c_user->id);
                $this->template->set('email', $admin->manager_user->get()->email);*/
                $this->template->render('manager');
            }

        } else {
            redirect('auth');
        }

    }

    public function login($userId = null)
    {

        if (!$userId) {
            $userId =$this->getRequest()->request->get('user', '');
        }

        $user = new User($userId);
        $code = $this->ion_auth->getManagerCode();
        if ($code && $manager = $this->ion_auth->codeManagerExist($code)) {
            if (!$manager->isManager($userId)->exists()) {
                $error[] = 'You are not a manager of this user';
            }
            if ($this->get('core.status.system')->isPaymentEnabled() && !$user->hasActiveSubscription()) {
                $error[] = 'Selected customer do not have an active subscription';
            }
            if ($this->ion_auth->is_admin($userId) || $this->ion_auth->is_manager($userId)) {
                $error[] = 'User is not a simple member';
            }
            if (empty($error)) {
                $manager->manager_code = $this->ion_auth->createManagerCode();
                $manager->manager_login_as = $userId;

                if ($manager->save()) {
                    $ionAuth = new Ion_auth_model();
                    $ionAuth->loginForce($user->email);
                    $this->addFlash('You are succesfully login as another user', 'success');
                }
            } else {
                foreach($error as $e) {
                    $this->addFlash($e);
                }
                //$this->template->set('email', $manager->manager_user->get()->email);
                $this->template->render('manager');
                return;
            }

        }

        redirect();
    }

}