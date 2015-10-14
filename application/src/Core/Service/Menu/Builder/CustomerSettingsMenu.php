<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 16:41
 */

namespace Core\Service\Menu\Builder;

use Knp\Menu\MenuItem;
use User;

class CustomerSettingsMenu extends CustomerMainMenu
{
    public function build()
    {
        $menu = $this->getMenuFactory()->createItem('Customer Main Menu');

        $menu->addChild('Personal Settings', array('path' => 'settings/personal'));

        $menu->addChild('My profiles', array('path' => 'settings/profiles'));

        if ($this->getAAC()->isGrantedPlan('reviews_monitoring')) {
            $menu->addChild('Directory Settings', array('path' => 'settings/directories'));
        }


        if ($this->getAAC()->isGrantedPlan('local_search_keyword_tracking')) {
            $menu->addChild('Google Places Keywords', array('path' => 'settings/keywords'));
        }

        if ($this->getAAC()->isGrantedPlan('social_activity') ||
            $this->getAAC()->planHasFeature('brand_reputation_monitoring')
        ) {
            $menu->addChild('Social Media', array('path' => 'settings/socialmedia'));

            $menu->addChild('User Search Keywords', array('path' => 'settings/user_search_keywords'));

            if ($this->getAAC()->planHasFeature('brand_reputation_monitoring')) {
                $menu->addChild('Social Keywords', array('path' => 'settings/mention_keywords'));
            }

        }



        if ($this->getAAC()->isGrantedPlan('website_traffic_monitoring')) {
            $menu->addChild('Analytics', array('path' => 'settings/analytics'));
        }


        if ($this->getAAC()->isGrantedPlan('social_activity') &&
            $this->getAAC()->isGrantedPlan('social_media_management')
        ) {
            $menu->addChild('Rss', array('path' => 'settings/rss'));
        }

        if ($this->getAAC()->isGrantedPlan('collaboration_team')) {
            $menu->addChild('Collaboration Team', array('path' => 'settings/collaboration'));
        }

        if (!get_instance()->ion_auth->is_manager() &&
            !get_instance()->ion_auth->getManagerCode()
            && $this->get('core.status.system')->isPaymentEnabled()
        ) {
            $menu->addChild('Subscriptions', array('path' => 'settings/subscriptions'));
        }




        return $this->customizeMenu($menu);
    }

    /**
     * {@inheritdoc}
     */
    protected function customizeMenu(MenuItem $menu)
    {
        $menu = parent::customizeMenu($menu);

        foreach ($menu->getChildren() as $child) {
            $child->setAttribute('class', $child->getAttribute('class').' settings_link');
        }

        return $menu;
    }

}
