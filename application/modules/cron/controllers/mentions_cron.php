<?php

class Mentions_cron extends CLI_controller {

    /**
     * Check for not-updated-today, not-deleted  keywords
     * And set for mentions update if exist
     */
    public function queue_mention_keywords_for_update() {
		
        // get all keywords, that are not set for deletion
        // and they have a date of last request more then one day ago
        $mention_keywords = Mention_keyword::inst()->get_for_cron_update();


        if (!$mention_keywords->exists()) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No mention keywords for update');
            return;
        }

        $today = date('U', strtotime('today'));
        $yesterday = date('U', strtotime('yesterday'));

        $users_cache = array();
        $aac = $this->getAAC();

        /** @var Mention_keyword $mention_keyword */
        foreach ($mention_keywords as $mention_keyword) {
            $user = new User($mention_keyword->user_id);

            if (!$user->exists()) {
                continue;
            }

            $aac->setUser($user);

            if (!$aac->planHasFeature('brand_reputation_monitoring')) {
                continue;
            }

            if (!isset($users_cache[$mention_keyword->user_id])) {
                $users_cache[$mention_keyword->user_id] = 0;
            }

            $usersBrandReputationMonitoring = $aac->getPlanFeatureValue('brand_reputation_monitoring');

            //$aac->isGrantedPlan('brand_reputation_monitoring')
            if ($usersBrandReputationMonitoring &&
                $users_cache[$mention_keyword->user_id] >= $usersBrandReputationMonitoring) {
                break;
            }

            $users_cache[$mention_keyword->user_id]++;

            // if keywords has some socials set as grabbed, but also has non-requested date
            // clear all socials to try to grab mentions again
            if ($mention_keyword->grabbed_socials 
                && $mention_keyword->requested_at 
                && $mention_keyword->requested_at < $yesterday
            ) {
                $mention_keyword->grabbed_socials = NULL;
                $saved = $mention_keyword->save();
                if ( ! $saved) {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not updated grabbs: ' 
                        . $mention_keyword->error->string);
                } else {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Not updated grabbs cleared');
                }
            }

            // get socials user has tokens for
            $socials_for_update = Access_token::inst()->get_user_socials($mention_keyword->user_id, $mention_keyword->profile_id, ['facebook']);

            // get socials that were already grabbed
            $grabbed_socials = $mention_keyword->get_grabbed_socials_as_array();

            // get socials that were not grabbed yet
            $socials = array_diff($socials_for_update, $grabbed_socials);

            if (count($socials)) {

                foreach ($socials as $social) {
              
                    $args = $mention_keyword->to_array();
                    $args['social'] = $social;

                    try {
                        $this->jobQueue->addJob('tasks/mentions_task/grabber', $args, array(
                            'thread' => self::MENTIONS_THREAD
                        ));
                        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Adding Mentions : ' . $social);



                    } catch(Exception $e) {
                        log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Failed mention : ' . $social 
                            . ' ; ' . $e->getMessage());
                        throw $e;    
                    }
                }

            } else {
                $mention_keyword->requested_at = $today;
                $mention_keyword->grabbed_socials = NULL;
                $saved = $mention_keyword->save();
                if ($saved) {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Marked as grabbed : mkwid '.
                        $mention_keyword->id);
                } else {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not marked as grabbed : mkwid '
                        . $mention_keyword->id . ' : ' . $mention_keyword->error->string);
                }
            }

        }

        $ids_str = implode(', ', array_values($mention_keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > Mention keywords for update ids: ' . $ids_str);
        return;
    }


    /**
     * Check mention keywords set for deletion
     * And move them to queue
     * 
     * minutely ?
     */
    public function queue_deleted_mention_keywords() {
        $keywords = Mention_keyword::inst()->get_by_is_deleted(1);

        if ( ! $keywords->exists() ) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No keywords for removal');
            return;
        }

        foreach($keywords as $keyword) {
            $args = $keyword->to_array();
            $this->jobQueue->addJob('tasks/mentions_task/remove_deleted',  $args, array(
                'thread' => self::MENTIONS_THREAD
            ));
        }

        $ids_str = implode(', ', array_values($keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Mention keywords for removal ids: ' . $ids_str);
        return;
    }


    /**
     * Check mentions not related to anything
     * And move them to queue
     * 
     * minutely ?
     */
    public function queue_unrelated_mentions() {
        $mentions_count = Mention::inst()
            ->where('user_id IS NULL')
            ->or_where('mention_keyword_id IS NULL')
            ->count();

        if ( ! $mentions_count) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No unrelated mentions');
            return;
        }

        $this->jobQueue->addJob('tasks/mentions_task/remove_unrelated', array(), array(
            'thread' => self::MENTIONS_THREAD
        ));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Unrelated mentions: ' . $mentions_count);
        return;
    }
}