<?php
/**
 * User: alkuk
 * Date: 25.05.14
 * Time: 13:01
 */

use Core\Service\DIContainer\PimpleContainer as ServicesContainer;
use Knp\Menu\Matcher\Voter\UriVoter;
use Core\Service\Menu\Voter\UrlVoter;

/**
 * Class SubKernel
 */
class SubKernel
{
    protected $codeIgniter;

    public function init()
    {

        $this->codeIgniter = get_instance();
        $this->initServicesContainer();

        $this->configurePlanFeaturesAcl();
        $this->initFireWall();

        $this->menuVoters();
    }

    /**
     * Initialize Services Container
     */
    protected function initServicesContainer()
    {
        $this->codeIgniter->container = new ServicesContainer($this->codeIgniter);
    }

    protected function initFireWall()
    {
        $route = array();
        $route[] = $this->codeIgniter->router->fetch_directory();
        $route[] = $this->codeIgniter->router->fetch_class();
        $route[] = $this->codeIgniter->router->fetch_method();
        $route = array_filter($route);

        $route = array_map(function ($item) {
            return trim($item, '/');
        }, $route);

        $route = implode('/', $route);

        $this->get('core.firewall.route')->routeRestrict($route, $this->codeIgniter->config->getRequest());
    }

    /**
     * Proxy for Services Container
     *
     * @param $serviceName
     */
    protected function get($serviceName)
    {
        return $this->codeIgniter->container->get($serviceName);
    }

    /**
     * Enable plan.features.acl if payment is active
     */
    protected function configurePlanFeaturesAcl()
    {
        if ($this->get('core.status.system')->isPaymentEnabled()) {
            //switch on 'plan features acl'
            $this->get('plan.features.acl.provider')->enable();
        }
    }

    /**
     * Setup voters that required for menu
     */
    protected function menuVoters()
    {
        $matcher = $this->get('knp.menu.matcher');
        $matcher->addVoter(new UrlVoter(current_url()));
        $matcher->addVoter(new UriVoter(current_url()));
    }
}
