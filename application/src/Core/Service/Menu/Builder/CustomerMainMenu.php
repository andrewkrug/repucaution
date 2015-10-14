<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 11:44
 */

namespace Core\Service\Menu\Builder;

use Core\Service\Menu\MenuBuilder;
use Knp\Menu\MenuItem;

class CustomerMainMenu extends MenuBuilder
{
    public function build()
    {
        $menu = $this->getMenuFactory()->createItem('Customer Main Menu');

        $menu->addChild(lang('dashboard'), array(
            'path' => 'dashboard',
            'icon_class' => 'ti-home',
        ));

        if ($this->getAAC()->planHasFeature('brand_reputation_monitoring')) {
            $menu->addChild(lang('web_mentions'), array(
                'path' => 'webradar',
                'icon_class' => 'ti-world',
            ));

            $menu[lang('web_mentions')]->addChild(lang('all_mentions'), array('path' => 'webradar/all'));
            $menu[lang('web_mentions')]->addChild(lang('twitter_mentions'), array('path' => 'webradar/twitter'));
//            $menu[lang('web_mentions')]->addChild(lang('facebook_mentions'), array('path' => 'webradar/facebook'));
            $menu[lang('web_mentions')]->addChild(lang('google_plus_mentions'), array('path' => 'webradar/google'));
            $menu[lang('web_mentions')]->addChild(lang('instagram_mentions'), array('path' => 'webradar/instagram'));

            if ($this->getAAC()->isGrantedPlan('brand_influencers_watch')) {
                $menu[lang('web_mentions')]->addChild(lang('influencers_watch'), array('path' => 'webradar/influencers'));
            }
        }

        if ($this->getAAC()->isGrantedPlan('reviews_monitoring')) {
            $menu->addChild(lang('reviews'), array(
                'path' => 'reviews',
                'icon_class' => 'ti-comment-alt',
            ));

            $directories = \DM_Directory::get_all_sorted();

            foreach ($directories as $directory) {
                $menu[lang('reviews')]->addChild($directory->name, array('path' => 'reviews/'.$directory->id));
            }
        }


        if ($this->getAAC()->isGrantedPlan('local_search_keyword_tracking')) {
            $menu->addChild(lang('google_rank'), array(
                'path' => 'rank',
                'icon_class' => 'ti-bar-chart'
            ));
        }

        if ($this->getAAC()->isGrantedPlan('social_activity')) {
            $menu->addChild(lang('social_media'), array(
                'path' => 'social',
                'icon_class' => 'ti-thumb-up',
            ));

            if ($this->getAAC()->isGrantedPlan('social_media_management')) {
                $menu[lang('social_media')]->addChild(lang('social_media_create'), array('path' => 'social/create'));

                if ($this->getAAC()->isGrantedPlan('scheduled_posts')) {
                    $menu[lang('social_media')]->addChild(lang('social_media_scheduled_posts'), array('path' => 'social/scheduled'));
                    $menu[lang('social_media')]->addChild(lang('social_media_cron_posts'), array('path' => 'social/cron_posts'));
                }
            }


            $menu[lang('social_media')]->addChild(lang('social_media_social_activity'), array('path' => 'social/activity'));
            $menu[lang('social_media')]->addChild(lang('social_media_social_reports'), array('path' => 'social/reports'));

        }

        if ($this->getAAC()->isGrantedPlan('website_traffic_monitoring')) {
            $menu->addChild(lang('analytics'), array(
                'path' => 'traffic',
                'icon_class' => 'ti-stats-up'
            ));
            $menu[lang('analytics')]->addChild(lang('analytics_google'), array(
                'path' => 'traffic'
            ));
            $menu[lang('analytics')]->addChild(lang('analytics_piwik'), array(
                'path' => 'piwik'
            ));
        }

        if ($this->getAAC()->isGrantedPlan('crm')) {
            $menu->addChild(lang('crm'), array(
                'path' => 'crm',
                'icon_class' => 'ti-user',
            ));

            $menu[lang('crm')]->addChild(lang('add_record'), array('path' => 'crm/add'));
            $menu[lang('crm')]->addChild(lang('directory'), array('path' => 'crm/directories'));
            $menu[lang('crm')]->addChild(lang('clients_activity'), array('path' => 'crm/activity'));

        }

        $menu->addChild(lang('settings'), array('path' => 'settings', 'icon_class' => 'ti-settings'));
        $menu[lang('settings')]->addChild(lang('settings_personal_settings'), array('path' => 'settings/personal'));

        $menu[lang('settings')]->addChild(lang('settings_my_profiles'), array('path' => 'settings/profiles'));

        if ($this->getAAC()->isGrantedPlan('reviews_monitoring')) {
            $menu[lang('settings')]->addChild(lang('settings_directory_settings'), array('path' => 'settings/directories'));
        }


        if ($this->getAAC()->isGrantedPlan('local_search_keyword_tracking')) {
            $menu[lang('settings')]->addChild(lang('settings_google_places_keywords'), array('path' => 'settings/keywords'));
        }

        if ($this->getAAC()->isGrantedPlan('social_activity') ||
            $this->getAAC()->planHasFeature('brand_reputation_monitoring')
        ) {
            $menu[lang('settings')]->addChild(lang('settings_social_media'), array('path' => 'settings/socialmedia'));

            if($this->getAAC()->planHasFeature('twitter_marketing_tools')) {
                $menu[lang('settings')]->addChild(lang('settings_user_search_keywords'), array('path' => 'settings/user_search_keywords'));
            }

            if ($this->getAAC()->planHasFeature('brand_reputation_monitoring')) {
                $menu[lang('settings')]->addChild(lang('settings_social_keywords'), array('path' => 'settings/mention_keywords'));
            }

        }

        if ($this->getAAC()->isGrantedPlan('website_traffic_monitoring')) {
            $menu[lang('settings')]->addChild(lang('settings_analytics'), array('path' => 'settings/analytics'));
            $menu[lang('settings')]->addChild(lang('settings_piwik'), array('path' => 'settings/piwik'));
        }


        if ($this->getAAC()->isGrantedPlan('social_activity') &&
            $this->getAAC()->isGrantedPlan('social_media_management')
        ) {
            $menu[lang('settings')]->addChild(lang('settings_rss'), array('path' => 'settings/rss'));
        }

        if ($this->getAAC()->isGrantedPlan('collaboration_team')) {
            $menu[lang('settings')]->addChild(lang('settings_collaboration_team'), array('path' => 'settings/collaboration'));
        }

        if (!get_instance()->ion_auth->is_manager() &&
            !get_instance()->ion_auth->getManagerCode()
            && $this->get('core.status.system')->isPaymentEnabled()
        ) {
            $menu[lang('settings')]->addChild(lang('settings_subscriptions'), array('path' => 'settings/subscriptions'));
        }

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
        $request = $this->get('core.request.current');

        foreach ($menu->getChildren() as $child) {
            $child->setAttribute('class', 'sidebar_item '.$child->getAttribute('class'));
            $uri = $request->getUri();
            $itemUri = $child->getUri();
            $linkClass = ($uri == $itemUri || stripos($uri, $itemUri) !== false) ? ' active' : '';
            $child->setLinkAttribute('class', 'sidebar_link'.$linkClass);
            if ($child->hasChildren()) {
                foreach ($child->getChildren() as $subChild) {
                    $subChild->setAttribute('class', 'sidebar_submenu_item '.$subChild->getAttribute('class'));
                    $subLinkClass = ($request->getUri() == $subChild->getUri()) ? ' active' : '';
                    $subChild->setLinkAttribute('class', 'sidebar_submenu_link'.$subLinkClass);
                }

                $openClass = (stripos($uri, $itemUri) === false) ? '' : 'active';
                $child->setChildrenAttribute('class', 'sidebar_submenu '.$openClass);


                $child->setExtra('safe_label', true);
                $child->setLabel($child->getLabel().' <span class="arrow"></span>');
            }


        }


        return $menu;
    }
}
