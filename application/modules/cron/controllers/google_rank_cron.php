<?php

class Google_rank_cron extends CLI_controller {

    /**
     * Check for not-updated-today, not-deleted  keywords
     * And set for rank update if exist
     * 
     * daily
     */
    public function queue_keywords_for_update() {

        $keyword_rank = new Keyword_rank;
        $keyword_rank_ids = $keyword_rank
            ->where('date', date('Y-m-d'))
            ->get()
            ->all_to_single_array('keyword_id');

        if ( empty($keyword_rank_ids) ) {
            $keyword_rank_ids = array(0);
        }

        $keywords = new Keyword;
        $keywords
            ->where('is_deleted', 0)
            ->where_not_in('id', $keyword_rank_ids)
            // ->group_by('keyword')
            ->get();

        if ( ! $keywords->exists() ) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No keywords for rank update');
            return;
        }

        $acc = $this->getAAC();

        foreach($keywords as $keyword) {

            $user = new User($keyword->user_id);
            if (!$user->exists()) {
                continue;
            }
            $acc->setUser($user);

            if (!$acc->isGrantedPlan('local_search_keyword_tracking')) {
                continue;
            }

            $args = $keyword->to_array();
            $this->jobQueue->addJob('tasks/google_rank_task/grabber',  $args, array(
                'thread' => self::GOOGLE_RANK_THREAD
            ));
        }

        $ids_str = implode(', ', array_values($keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Keywords for rank update ids: ' . $ids_str);
        return;
    }


    /**
     * Check keywords set for deletion
     * And move them to queue
     * 
     * minutely ?
     */
    public function queue_deleted_keywords() {
        $keywords = Keyword::inst()->get_by_is_deleted(1);

        if ( ! $keywords->exists() ) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No keywords for removal');
            return;
        }

        foreach($keywords as $keyword) {
            $args = $keyword->to_array();
            $this->jobQueue->addJob('tasks/google_rank_task/remove_deleted',  $args, array(
                'thread' => self::GOOGLE_RANK_THREAD
            ));
        }

        $ids_str = implode(', ', array_values($keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Keywords for removal ids: ' . $ids_str);
        return;
    }

}