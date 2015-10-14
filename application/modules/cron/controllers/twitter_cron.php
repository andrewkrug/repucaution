<?php

class Twitter_cron extends CLI_controller {

    /**
     * Daily social statistic collect
     * Add new access token to Queue
     *
     * @access public
     * @return void
     */
    public function run() {
        $types = array('twitter');
        $tokens = Access_token::inst()
            ->where_in('type', $types)
            ->get();

        $aac = $this->getAAC();

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
                //Twitter tasks
                $this->jobQueue->addJob('tasks/twitter_task/searchUsers',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/updateFollowers',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/randomRetweet',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/randomFavourite',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/sendWelcomeMessage',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/followNewFollowers',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
                $this->jobQueue->addJob('tasks/twitter_task/unfollowUnsubscribedUsers',  $args, array(
                    'thread' => self::SOCIAL_THREAD,
                    'execute_after' => $now
                ));
            }
        }
    }

}