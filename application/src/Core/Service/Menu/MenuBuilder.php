<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 11:43
 */

namespace Core\Service\Menu;

use Knp\Menu\MenuFactory;
use Core\Service\Menu\Extension\CoreExtension;

abstract class MenuBuilder implements MenuBuilderInterface
{
    protected $serviceContainer;

    public function __construct($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function getRendererService()
    {
        return 'core.menu.renderer.list';
    }

    /**
     * Get menu factory
     *
     * @return MenuFactory
     */
    protected function getMenuFactory()
    {
        $menuFactory = new MenuFactory();
        $menuFactory->addExtension(new CoreExtension(), -11);

        return $menuFactory;
    }

    /**
     * Proxy for service container
     *
     * @param $serviceName
     *
     * @return mixed
     */
    protected function get($serviceName)
    {
        return $this->serviceContainer->get($serviceName);
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
}
