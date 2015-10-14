<?php
/**
 * User: alkuk
 * Date: 25.05.14
 * Time: 15:00
 */

/**
 * return false - to restrict access
 */

$config['parameters.firewall.routes.restrict'] = array(
    '
    auth
    auth/*
    ' => function(){
        return true;
    },
    'social/scheduled*'    => function ($acl) {
        $acl->isGrantedPlanWithException('scheduled_posts');
    },
    'influencers/*' => function ($acl) {
        $acl->isGrantedPlanWithException('brand_influencers_watch');
    },
    'settings/*' => function ($acl) {
        $user = new User(get_instance()->ion_auth->get_user_id());
        $acl->setUser($user);
        if (get_instance()->ion_auth->is_collaborator()) {
            redirect('dashboard');
        }
    },
    '
    reviews*,
    settings/directories*
    ' => function ($acl) {
        $acl->isGrantedPlanWithException('reviews_monitoring');
    },
    '
    traffic/*,
    settings/analytics*
    ' => function ($acl) {
        $acl->isGrantedPlanWithException('website_traffic_monitoring');
    },
    '
    rank/*,
    settings/keywords*
    ' => function ($acl) {
        $acl->isGrantedPlanWithException('local_search_keyword_tracking');
    },
    'settings/socialmedia*' => function ($acl) {
        if (!($acl->isGrantedPlan('social_activity') ||
            $acl->planHasFeature('brand_reputation_monitoring'))
        ) {
            $acl->throwPlanAccessDeniedException();
        }
    },
    'settings/rss*' => function ($acl) {
        $acl->isGrantedPlanWithException('social_media_management');
    },
    'social/webradar*' => function ($acl) {
        $acl->planHasFeatureWithException('brand_reputation_monitoring');
    },
    'social*' => function ($acl) {
        $acl->isGrantedPlanWithException('social_activity');
    },
    'admin/*' => function ($acl) {
            if (!$acl->isGrantedRole('enter_admin_side')) {
                redirect('auth');
            }
     },
    'admin/manage_accounts*' => function ($acl) {
        $acl->isGrantedRoleWithException('manage_admins');
    },
    'manager/*' => function ($acl) {
        if (!$acl->isGrantedRole('enter_manager_side') && !get_instance()->ion_auth->getManagerCode()) {
            redirect('auth');
        }
    },
    'settings/collaboration*' => function ($acl) {

            $acl->planHasFeatureWithException('collaboration_team');
    },
    'settings/subscriptions*' => function ($acl) {
            if ($acl->isGrantedRole('enter_manager_side') || get_instance()->ion_auth->getManagerCode()) {
                redirect();
            }
    },

);
