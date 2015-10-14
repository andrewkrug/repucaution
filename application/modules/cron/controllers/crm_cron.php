<?php

class Crm_cron extends CLI_controller {

    /**
     * Check for not-updated-today, not-deleted  crm directories
     * And set for crm activities update if exist
     */
    public function queue_crm_directories_for_update() {

        // get all crm directories, that are not set for deletion
        // and they have a date of last request more then one day ago
        $crmDirectories = Crm_directory::inst()->getForUpdate();


        if (!$crmDirectories->exists()) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No crm directories for update');
            return;
        }

        $today = date('U', strtotime('today'));
        $yesterday = date('U', strtotime('yesterday'));

        $users_cache = array();
        $aac = $this->getAAC();

        foreach ($crmDirectories as $directory) {
            $user = new User($directory->user_id);

            if (!$user->exists()) {
                continue;
            }

            $aac->setUser($user);

            if (!$aac->planHasFeature('crm')) {
                continue;
            }

            if (!isset($users_cache[$directory->user_id])) {
                $users_cache[$directory->user_id] = 0;
            }

            $usersCrm = $aac->getPlanFeatureValue('crm');

            //$aac->isGrantedPlan('crm')
            if ($usersCrm &&
                $users_cache[$directory->user_id] >= $usersCrm) {
                break;
            }

            $users_cache[$directory->user_id]++;

            // if directory has some socials set as grabbed, but also has non-requested date
            // clear all socials to try to grab activities again
            if ($directory->grabbed_socials
                && $directory->requested_at
                && $directory->requested_at < $yesterday
            ) {
                $directory->grabbed_socials = NULL;
                $saved = $directory->save();
                if ( ! $saved) {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not updated grabbs: '
                        . $directory->error->string);
                } else {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Not updated grabbs cleared');
                }
            }

            // get socials user has tokens for
            $socials_for_update = Access_token::inst()->get_crm_user_socials($directory->user_id, $directory->profile_id, ['facebook']);

            // get socials that were already grabbed
            $grabbed_socials = $directory->get_grabbed_socials_as_array();

            // get socials that were not grabbed yet
            $socials = array_diff($socials_for_update, $grabbed_socials);
            if (count($socials)) {

                foreach ($socials as $social) {

                    $args = $directory->to_array();
                    $args['social'] = $social;

                    try {
                        $this->jobQueue->addJob('tasks/crm_directory_task/grabber', $args, array(
                            'thread' => self::CRM_THREAD
                        ));
                        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Adding directories : ' . $social);



                    } catch(Exception $e) {
                        log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Failed directory : ' . $social
                            . ' ; ' . $e->getMessage());
                        throw $e;
                    }
                }

            } else {
                $directory->requested_at = $today;
                $directory->grabbed_socials = NULL;
                $saved = $directory->save();
                if ($saved) {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Marked as grabbed : mkwid '.
                        $directory->id);
                } else {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not marked as grabbed : mkwid '
                        . $directory->id . ' : ' . $directory->error->string);
                }
            }

        }

        $ids_str = implode(', ', array_values($directory->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > Crm directories for update ids: ' . $ids_str);
        return;
    }


    /**
     * Check crm directories set for deletion
     * And move them to queue
     * 
     * minutely ?
     */
    public function queue_deleted_crm_directories() {
        $directories = Crm_directory::inst()->get_by_is_deleted(1);

        if ( ! $directories->exists() ) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No crm directories for removal');
            return;
        }

        foreach($directories as $directory) {
            $args = $directory->to_array();
            $this->jobQueue->addJob('tasks/crm_directory_task/remove_deleted',  $args, array(
                'thread' => self::CRM_THREAD
            ));
        }

        $ids_str = implode(', ', array_values($directories->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Crm directories for removal ids: ' . $ids_str);
        return;
    }


    /**
     * Check activities not related to anything
     * And move them to queue
     * 
     * minutely ?
     */
    public function queue_unrelated_crm_activities() {
        $activities = Crm_directory_activity::inst()
            ->where('crm_directory_id IS NULL')
            ->count();

        if ( ! $activities) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No unrelated activities');
            return;
        }

        $this->jobQueue->addJob('tasks/crm_directory_task/remove_unrelated', array(
            'thread' => self::CRM_THREAD
        ));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Unrelated activities: ' . $activities);
        return;
    }
}