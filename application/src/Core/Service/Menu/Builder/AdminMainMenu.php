<?php
/**
 * User: ajorjik
 * Date: 02.06.15
 * Time: 15:44
 */

namespace Core\Service\Menu\Builder;

use Core\Service\Menu\MenuBuilder;
use Knp\Menu\MenuItem;

class AdminMainMenu extends MenuBuilder
{
    public function build()
    {
        $menu = $this->getMenuFactory()->createItem('Admin Main Menu');

        $menu->addChild(lang('manage_customers'), array(
            'path' => 'admin/admin_users',
            /*'icon_class' => 'ti-home',*/
        ));

        $menu->addChild(lang('account_managers'), array(
            'path' => 'admin/manage_accounts',
            /*'icon_class' => 'ti-home',*/
        ));

        $menu->addChild(lang('api_keys'), array(
            'path' => 'admin/admin_api',
            /*'icon_class' => 'ti-home',*/
        ));

        $menu->addChild(lang('plans_management'), array(
            'path' => 'admin/manage_plans',
            /*'icon_class' => 'ti-home',*/
        ));

        $menu->addChild(lang('payment_settings'), array(
            'path' => 'admin/payment_settings',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('influencers_settings'), array(
            'path' => 'admin/influencers_settings',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('payment_transactions'), array(
            'path' => 'admin/transactions',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('social_settings'), array(
            'path' => 'admin/social_settings',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('admins_management'), array(
            'path' => 'admin/manage_admins',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('export_to_mailchimp'), array(
            'path' => 'admin/mailchimp',
            /*'icon_class' => 'ti-home',*/
        ));
        $menu->addChild(lang('options'), array(
            'path' => 'admin/options',
            /*'icon_class' => 'ti-home',*/
        ));


        return $this->customizeMenu($menu);
    }

    /**
     * Customize menu
     *
     * @param MenuItem $menu
     *
     * @return MenuItem
     */
    protected function customizeMenu(MenuItem $menu)
    {

        $menu->setChildrenAttribute('class', 'sidebar_content');

        foreach ($menu->getChildren() as $child) {
            $child->setAttribute('class', 'sidebar_item '.$child->getAttribute('class'));
            $child->setLinkAttribute('class', 'sidebar_link');
            if ($child->hasChildren()) {

                foreach ($child->getChildren() as $subChild) {
                    $subChild->setAttribute('class', 'sidebar_submenu_item '.$subChild->getAttribute('class'));
                    $subChild->setLinkAttribute('class', 'sidebar_submenu_link');
                }

                /*$child->setAttribute('class', 'toggle open '. $child->getAttribute('class'));*/
                $child->setChildrenAttribute('class', 'sidebar_submenu');
                $child->setExtra('safe_label', true);
                $child->setLabel($child->getLabel().' <span class="arrow"></span>');
            }


        }


        return $menu;
    }
}
