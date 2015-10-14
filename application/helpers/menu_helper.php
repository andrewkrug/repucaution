<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 12:59
 */


function menu_render($menuName, array $options = array(), $renderer = null)
{
    $servicesContainer = get_instance()->container;
    $menu = $servicesContainer->get('core.menu.builder.'.$menuName);

    if ($renderer) {
        $menuRenderer = $servicesContainer->get($renderer);
    } else {
        $menuRenderer = $servicesContainer->get($menu->getRendererService());
    }

    return $menuRenderer->render($menu->build(), $options);
}

function breadcrumb_render($menuName, array $options = array(), $renderer = null)
{
    $servicesContainer = get_instance()->container;
    $menu = $servicesContainer->get('core.menu.builder.'.$menuName);

    if ($renderer) {
        $menuRenderer = $servicesContainer->get($renderer);
    } else {
        $menuRenderer = $servicesContainer->get($menu->getRendererService());
    }

    return $menuRenderer->render($menu->build(), $options);
}