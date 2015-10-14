<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Crm extends MY_Controller {

    protected $website_part = 'dashboard';

    protected $crmManager;

    private $activeSocials;

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('crm', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('crm', $this->language)
        ]);

        $this->load->config('site_config', TRUE);
        $this->crmManager = $this->get('core.crm.manager');
        $this->activeSocials = Access_token::inst()->get_crm_user_socials($this->c_user->id, $this->profile->id);
        $hasDirectories = Crm_directory::inst()->hasDirectories($this->c_user->id, $this->profile->id);
        $this->template->set('hasDirectories', $hasDirectories);

        $hasRequested = Crm_directory::inst()->hasRequested($this->c_user->id, $this->profile->id);
        $this->template->set('hasRequested', $hasRequested);
        //CssJs::getInst()->add_css('crm.css');
        CssJs::getInst()->add_js('controller/crm/index.js');
        $this->load->helper('clicable_links');
    }

    public function edit($directoryId = null)
    {
        $config = $this->config->item('crm_directories', 'site_config');
        $count = 0;
        if (!empty($config['count'])) {
            $count = $config['count'];
        }

        $availableDirectoriesCount = $this->getAAC()->getPlanFeatureValue('crm');
        if ($availableDirectoriesCount) {
            $count = $availableDirectoriesCount;
        }

        $userDirectories = Crm_directory::inst()->getUserDirectories(array(
            'user_id' => $this->c_user->id,
            'profile_id' => $this->profile->id
        ))->all_to_array();
        if (count($userDirectories) >= $count) {
            $this->addFlash(lang('directories_limit_error'));
            redirect('crm/directories');
        }
        $this->lang->load('form_validation', $this->language);
        $directory = Crm_directory::inst($directoryId);
        if ($this->getRequest()->isMethod('post')) {
            $data = $this->getRequest()->request->all();
            $directory->fillFromArray($data);
            if (!$directory->save(array(
                'user' => $this->c_user,
                'profile' => $this->profile
            ))) {
                $errors = $directory->error->all;
                JsSettings::instance()->add('errors', $errors);
                $this->template->set('recent', $data);
            } else {
                $this->addFlash(lang('record_saved_success'), 'success');
                redirect('crm/directories');
            }
        }
        $this->template->set('directory', $directory);

        $this->template->render();
    }

    public function add()
    {
        redirect('crm/edit');
    }

    public function directories()
    {
        $params['user_id'] = $this->c_user->id;
        $params['profile_id'] = $this->profile->id;
        $request = $this->getRequest()->query;
        $offset = $request->get('offset', '');
        if ($username = $request->get('username', '')) {
            $params['username'] = $username;
        }
        if ($company = $request->get('company', '')) {
            $params['company'] = $company;
        }
        $directories = $this->crmManager->getUserDirectories($params, null, $offset);
        $feed = $this->getHtmlData($directories);
        if ($this->template->is_ajax()) {
            echo json_encode(array('html' => $feed));
            exit;
        }
        $this->template->set('feed', $feed);
        $this->template->set('username', $username);
        $this->template->set('company', $company);
        $this->template->render();
    }

    public function directory($directoryId)
    {
        $directory = Crm_directory::inst($directoryId);
        $request = $this->getRequest()->query;
        $userId = $this->c_user->id;
        if (!$directory->exists() or !$directory->isUser($userId)) {
            $this->addFlash(lang('owner_error'));
            redirect('crm/directories');
        }

        $offset =  $this->getRequest()->query->get('offset', 0);
        $this->crmManager->setOffset($offset);
        $social = $request->get('social', '');

        $activities = $this->crmManager->getDirectoryActivities($this->c_user->id, $this->profile->id, $directoryId, $social);
        $feed = $this->getFeedHtmlData($activities);
        if ($this->template->is_ajax()) {
            echo json_encode(array('html' => $feed));
            exit;
        }
        $existsSocials = $this->crmManager->getExistsActivitiesSocials($directoryId);
        $this->template->set('feed', $feed);
        $this->template->set('directory', $directory);
        $this->template->set('username', $request->get('username', ''));
        $this->template->set('company', $request->get('company', ''));
        $this->template->set('existsSocials', $existsSocials);
        $this->template->set('social', $social);
        $this->template->render();

    }

    public function activity()
    {
        $offset =  $this->getRequest()->query->get('offset', 0);

        $this->crmManager->setOffset($offset);

        $activities = $this->crmManager->getDirectoryActivities($this->c_user->id, $this->profile->id);

        $feed = $this->getFeedHtmlData($activities);
        if ($this->template->is_ajax()) {
            echo json_encode(array('html' => $feed));
            exit;
        }

        $this->template->set('feed', $feed);
        $this->template->render();
    }

    public function getHtmlData($directories)
    {
        $htmlData = '';
        if ($directories->exists()) {
            $htmlData = $this->template->block('feed', 'crm/blocks/feed', array('directories' => $directories));
        }
        return $htmlData;
    }

    public function getFeedHtmlData($activities)
    {
        $htmlData = '';
        if ($activities->exists()) {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Facebook $facebook */
            $facebook = Socializer::factory('Facebook', $this->c_user->id);
            $fbUserImage = $facebook->get_profile_picture();
            $wlist = Influencers_whitelist::create()->getByUser($this->c_user->id);
            foreach ($activities as $activity) {
                $social = $activity->social;
                $radar = $this->get('core.radar');
                if ($social == 'facebook') {
                    $activity->creator_image_url = $facebook->get_profile_picture($activity->creator_id);
                    $activity->user_image = $fbUserImage;
                }
                $activity->actions = in_array($social, $this->activeSocials);
                $activity->influencer = array_key_exists($activity->creator_id, $wlist) && $wlist[$activity->creator_id] == $mention->social;
                $activity->created_at = $radar->formatRadarDate($activity->created_at);
                $activity->profileUrl = $radar->getProfileUrl($activity->social);
                $content = $this->template->block('_content', '/social/webradar/blocks/'.$activity->social, array('mention' => $activity));

                $blockData = array('mention' => $activity, 'content' => $content);
                $htmlData .= $this->load->view('social/webradar/blocks/_feed', $blockData, true);
            }
        }

        return $htmlData;
    }

    public function autocomplete()
    {
        $searchParam = $this->getRequest()->query->get('param');
        $searchValue = $this->getRequest()->query->get('value');
        $userId = $this->c_user->id;
        $directories = Crm_directory::inst()->getUserDirectories(array($searchParam => $searchValue, 'user_id' => $userId), 5);

        echo $this->template->block('users',
                                    'crm/blocks/autocomplete.php',
                                    array('directories' => $directories, 'searchParam' => $searchParam ));

    }

    public function delete($directoryId)
    {
        $directory = Crm_directory::inst($directoryId);
        $userId = $this->c_user->id;
        if (!$directory->exists() or !$directory->isUser($userId)) {
            $this->addFlash(lang('owner_error'));

        } else {
            $activities = $this->crmManager->getDirectoryActivities($userId, $this->profile->id, $directoryId);
            foreach($activities as $activity) {
                $activity->delete();
            }
            if ($directory->delete()){
                $this->addFlash(lang('directory_delete_success'), 'success');
            } else {
                $this->addFlash(lang('directory_delete_error'));
            }
        }

        redirect('crm/directories');
    }

}