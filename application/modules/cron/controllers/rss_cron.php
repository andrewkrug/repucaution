<?php

class Rss_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
        try {
            /* @var User[] $users */
            $users = User::withActiveSubscription();
            $types = array('facebook', 'twitter', 'linkedin');
            $now = new \DateTime('UTC');
            foreach($users as $user) {
                if((bool)$user->rss_feed->count()) {
                    $tokens = $user->access_token
                        ->where_in('type', $types)
                        ->get();
                    $tokens_array = $tokens->all_to_array();
                    foreach($user->rss_feed->get() as $rss_feed) {
                        foreach($tokens_array as &$token) {
                            $token['profile_id'] = $rss_feed->profile_id;
                        }
                    }
                    $this->jobQueue->addJob('tasks/rss_task/send',  $tokens_array, array(
                        'thread' => self::RSS_THREAD,
                        'execute_after' => $now
                    ));
                    $now->modify('1 minutes');
                }
            }
        } catch(Exception $e) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }

}