<?php

class Social_reports_cron extends CLI_controller {

    /**
     * Daily social statistic collect
     * Add new access token to Queue
     *
     * @access public
     * @return void
     */
    public function run() {
        $types = array('facebook', 'twitter', 'linkedin', 'google', 'instagram');
        $tokens = Access_token::inst()
            ->where_in('type', $types)
            ->get();

        $aac = $this->getAAC();
        $acceptedUsersIds = array();

        $now = new \DateTime('UTC');

        foreach($tokens as $_token) {
            $now->modify('1 minutes');
            $user = new User($_token->user_id);
            if (!$user->exists()) {
                continue;
            }
            $aac->setUser($user);

            if (!$aac->isGrantedPlan('social_activity')) {
                continue;
            }
            $args = $_token->to_array();
            foreach($_token->social_group->get() as $profile) {
                $args['profile_id'] = $profile->id;
                if(!in_array($args['user_id'], $acceptedUsersIds) && $args['type'] != 'instagram') {
                    array_push($acceptedUsersIds, $args['user_id']);
                }
            }
        }
        $this->jobQueue->addJob('tasks/social_reports_task/statistic',  $acceptedUsersIds, array(
            'thread' => self::SOCIAL_THREAD
        ));
    }

}