<?php

/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 12:12
 */

class Test extends MY_Controller
{
    protected $website_part = 'dashboard';

    protected $testMe;

    public function index()
    {



        //var_dump($getMessage());


        exit;

        redirect('https://www.facebook.com/dialog/oauth?client_id=481670691944487&redirect_uri=http%3A%2F%2Frepucaution.loc%2Ffbbuilder%2Fbootstrap%23%2Fpublish%2F&state=4f30210bb1ca2b91580148860c004669&sdk=php-sdk-3.2.3&scope=manage_pages%2Cbasic_info%2Cemail');

        exit(call_user_func($this->testMe));

        $this->renderJson(
            [
                'isXhr' => get_instance()->input->server('HTTP_X_REQUESTED_WITH')
            ]
        );

       //print_d($this->getPagesService()->getAdminPages());

       /* $tf = $this->container->get('ikantam.theme.factory');

        echo $tf->getThemeByName('Mobile application')->buildLayoutWithTemplate('default', 'main'); */


        exit;
        /*$params = array(
            'x_axis' => 60,
            'y_axis' => 80,
            'width' => 500,
            'height' => 350,
            'maintain_ratio' => false,
            'new_image' => APPPATH . 'L-shape-yellow-sofa-design_cropped.jpg',
            'source_image' => APPPATH . '../public/uploads/files/images/34/L-shape-yellow-sofa-design.jpg'
        );
        $this->load->library('image_lib');
        $this->image_lib->initialize($params);
        $this->image_lib->crop();*/

        print_d(array_merge(array('a' => 1),array('a'=>2, 'b'=>2)));
    }

    /**
     * @return \Core\Service\Image\Crop
     */
    protected function getCropService()
    {
        return $this->container->get('core.service.image.crop');
    }

    /**
     * @return \Core\Service\Facebook\Pages
     */
    protected function getPagesService()
    {
        return $this->container->get('core.service.facebook.pages');
    }

    /**
     * @return \Facebook
     */
    protected function getFacebookApi()
    {
        return $this->container->get('facebook.sdk.api');
    }

    /**
     * @return \Core\Service\Facebook\ViewPage
     */
    protected function getFBViewPageService()
    {
        return $this->container->get('core.service.facebook.view');
    }

    /**
     * @return \Core\Service\Theme\SessionHelper
     */
    protected function getSessionHelper()
    {
        return $this->container->get('core.service.theme.session.helper');
    }

    public function exampleImageUrls()
    {
        $layout = new ThemeLayout(1);
        $template = new ThemeTemplate(1);
        $theme = new Theme(1);
        print_d($layout->getThemeConfig());

        var_dump($theme->getPath());

        var_dump($template->getImageUrl());
        echo '<br>';
        var_dump($layout->getImageUrl());
        echo '<br>';
        var_dump($theme->getImageUrl());
    }

    public function exampleGetThemesFactory()
    {
        $tf = $this->container->get('ikantam.theme.factory');
    }

    public function exampleInstallTheme()
    {
        $tf = $this->container->get('ikantam.theme.factory');
        $installer = $this->container->get('core.service.theme.installer');

        foreach ($tf->getAllThemes() as $theme) {
            $installer->uninstall($theme);
            $installer->install($theme);
        }
    }

    public function exampleRetrieveHtml()
    {
        $htmlGetter = $this->container->get('core.service.theme.html');
        //get html for theme Example with layout two_columns and template index
        echo $htmlGetter->get('Example', 'two_columns', 'index');
    }

    public function exampleSaveUserData()
    {
        $someTextComponentId = 'text_0ac730906fe7556dd13520a7a98910eb'; // <- id attribute of html element (accepts text)
        //  $arrayComponentId = 'link_763476tr73fg763476f38487375'; // link accept 2 parameters: text and url

        $storage = $this->container->get('core.service.theme.user.data.storage.db');

        // public function save($userId, $templateId, $componentId, $componentValue)
        $storage->save(1, 1, $someTextComponentId, 'User input string');
        //$storage->save(1, 1, $arrayComponentId, array('url' => 'http://user-url.com', 'text' => 'user input'));

        // or better way to save via helper class:
        /*
        $themeName = 'second theme';
        $layoutName = 'two_columns';
        $templateName = 'index';


         // @var \Core\Service\Theme\Helper

        $service = $this->container->get('core.service.theme.helper');
        var_dump($service->saveThemeForUser(1, $themeName, $layoutName, $templateName, array(
                    'text_1e1b7f373cab78543cc07342510575cc' => 'text',
                    'some_array' => array(1,2,3)
                )));
        */

    }

    public function exampleRetrieveComponentData()
    {
        $storage = $this->container->get('core.service.theme.user.data.storage.db');

        print_d($storage->retrieveComponentValue(1, 1, 'link_763476tr73fg763476f38487375'));
    }

    public function exampleHtml($mode = 'edit')
    {
        $html = $this->container->get('core.service.theme.html');
        echo '<per>';
        echo htmlspecialchars($html->getEdit(1, 'example', 'two_columns', 'index'));
    }

    public function sessionHelper()
    {
        $service = $this->container->get('core.service.theme.session.helper');

        $service->removeAll();
        $res = count($service->getAll());
        assert('count($service->getAll()) === 0;', 'Should give an empty array. But contains: '. $res . ' elements' );
        $service->setThemeId(7);

        assert('count($service->getAll()) === 1;', 'Should have 1 element');
        assert('$service->getThemeId() === 7;', 'Expected theme id to equals 7');

        $service->setThemeName('Chrome');

        assert('count($service->getAll()) === 2;', 'Should have 2 elements');
        assert('$service->getThemeName() === "Chrome";', 'Expected theme name to equals Chrome');

        $service->store(new Theme(7), new ThemeTemplate(10), new ThemeLayout(9));

        assert('count($service->getAll()) === 6;', 'Should have 6 elements');
        assert('$service->getThemeId() === 7;', 'Expected theme id to equals 7');
        assert('$service->getLayoutId() === 9;', 'Expected layout id to equals 9');
        assert('$service->getTemplateId() === 10;', 'Expected template id to equals 10');
        $service->removeAll();

    }

    public function getLocation() {
        $user_additional = User_additional::inst()->get_by_user_id(3);
        $this->load->config('site_config', TRUE);
        // $google_app_config = $this->config->item('google_app', 'site_config');
        $google_app_config = Api_key::build_config(
            'google',
            $this->config->item('google_app', 'site_config')
        );

        $this->load->library('gls');
        $gls = new Gls; // important
        $gls->set(array(
            // 'key' => $google_app_config['simple_api_key'],
            'key' => $google_app_config['developer_key'],
        ));

        $gls->request('social');

        if ($gls->success()) {

            $rank = $gls->location_rank($user_additional->address_id);
            echo $rank;

        } else {

            throw new Exception('Google Rank Grabber Error: ' . $gls->error());
        }
    }


    public function postGoogle() {
        $this->load->library('Socializer/socializer');
        $google = Socializer::factory('Google', $this->c_user->id, Social_group::getAccountByTypeAsArray($this->profile->id, 'google')[0]);
        $google->post();
    }
} 