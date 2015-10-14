<?php
/**
 * User: dev
 * Date: 16.01.14
 * Time: 15:05
 */

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class Base_Controller
 *
 * @property Ion_auth_model ion_auth
 */
class Base_Controller extends MX_Controller
{
    /**
     * @var \Core\Service\DIContainer\PimpleContainer
     */
    private $container;

    /**
     * @var Ion_auth_model ion_auth
     */

    public function __construct()
    {

        parent::__construct();

        $this->container = get_instance()->container;

        $this->set_default_css();
        $this->set_default_js();

        $this->template->set('paymentsEnabled', $this->get('core.status.system')->isPaymentEnabled());

        $c_user = $this->getUser();
        $a_user = new User($this->ion_auth->get_user_id());
        $this->template->set('c_user', $c_user);
        $this->template->set('a_user', $a_user);
        $this->template->set('breadcrumbs', false);

    }

    /**
     * Get service or parameter from container
     *
     * @param $name string
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->container->get($name);
    }


    /**
     * Get model by id
     *
     * @param $id
     * @param $modelName
     *
     * @return DataMapper
     */
    protected function getModelFromId($id, $modelName)
    {
        $model = new $modelName($id);
        if (!$model->exists()) {
            show_404();
        }

        return $model;
    }

    /**
     * Get Request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->input->getRequest();
    }

    /**
     * Add flash message
     *
     * @param string $message
     * @param string $type
     */
    protected function addFlash($message, $type = 'error')
    {
        $this->input->getRequest()->getSession()->getFlashBag()->add($type, $message);

    }

    /**
     * Check if request method is ...
     *
     * @param $name
     *
     * @return bool
     */
    protected function isRequestMethod($name)
    {
        return $this->getRequest()->isMethod($name);
    }

    /**
     * Try ro get current user
     *
     * @return User | null
     */
    protected function getUser()
    {
        return $this->get('current_user.model');
    }

    /**
     * Get App Access Control
     *
     * @return \Core\Service\AccessControl\AppAccessControl
     */
    protected function getAAC()
    {
        return $this->get('core.service.app.access.control');
    }

    /**
     * Throws access denied exception
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function throwAccessDeniedException($message = 'Access denied!')
    {
        throw new AccessDeniedHttpException($message);
    }

    /**
     * It throws NotFoundException
     *
     * @param string $message
     */
    protected function throwNotFoundException($message = 'Page not found!')
    {
        show_404($message);
    }

    /**
     * Check model for existence and throw NotFoundException exception
     *
     * @param DataMapper $model
     * @param string $message
     */
    protected function throwModelNotFoundException(\DataMapper $model, $message = 'Resource not found!')
    {
        if (!$model->exists()) {
            $this->throwNotFoundException($message);
        }
    }

    /**
     * Redirect user to forward url
     */
    protected function forwardUser()
    {
        $forwardUrl = site_url();
        $session = $this->getRequest()->getSession();

        if ($session->has('forward_url')) {
            $forwardUrl = $session->get('forward_url');
            $session->remove('forward_url');
        }

        redirect($forwardUrl);
    }

    protected function set_default_css()
    {
        $css_array = array(
            array(
                'css' => '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
                'type' => 'external',
                'location' => 'header'
            ),
            'style.css',
            array(
                'css' => '//fonts.googleapis.com/css?family=Roboto:400,300,700',
                'type' => 'external',
                'location' => 'header'
            ),
            array(
                'css' => '//fonts.googleapis.com/css?family=Montserrat',
                'type' => 'external',
                'location' => 'header'
            ),
            array(
                'css' => '//fonts.googleapis.com/css?family=Lato:400,700',
                'type' => 'external',
                'location' => 'header'
            ),
            array(
                'css' => '//fonts.googleapis.com/css?family=Raleway:400,300',
                'type' => 'external',
                'location' => 'header'
            ),
            'bootstrap.min.css',
            'style.css',


        );

        CssJs::getInst()->add_css($css_array);
    }

    protected function set_default_js()
    {
        $js_array = array(
            array(
                'js' => 'code.jquery.com/jquery.min.js',
                'type' => 'external',
                'location' => 'header'
            ),
            array(
                'js' => 'code.jquery.com/ui/1.11.2/jquery-ui.min.js',
                'type' => 'external',
                'location' => 'header'
            ),
            'bootstrap.min.js',
            'checkBo.min.js',
            'jquery.fancybox.pack.js',
            'moment.min.js',
            'bootstrap-tagsinput/bootstrap-tagsinput.min.js',
            'daterangepicker.js',
            'datetimepicker.js',
            'date.js',
            'calendar_settings.js',
            'function.js',
            'script.js',
            'helpers.js',
            'libs/handlebar.js',
            'libs/handlebars_helpers.js'
        );

        CssJs::getInst()->add_js($js_array);

        $js_vars_array = array(
            'base_url' => base_url(),
            'current_url' => uri_string(),
        );

        JsSettings::instance()->add($js_vars_array);
    }

}