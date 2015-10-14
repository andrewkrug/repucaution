<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 11:41
 */

namespace Core\Service\Menu;


interface MenuBuilderInterface
{
    /**
     * Return renderer service
     *
     * @return string
     */
    public function getRendererService();


    /**
     * Build menu
     *
     * @return \Knp\Menu\MenuItem
     */
    public function build();
}
