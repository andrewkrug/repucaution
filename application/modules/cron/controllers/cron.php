<?php

class Cron extends CLI_controller {

    public function daily() {

        //parse reviews
        $this->jobQueue->addJob('cron/reviews_cron/run');

        //clearing of queue of jobs
        $this->jobQueue->addUniqueJob('cron/job_queue_cron/clear');

    }

    public function minutely() {
        //Scheduled posts run
        $this->jobQueue->addJob('cron/scheduled_posts_cron/run', array(), array(
            'thread' => self::SCHEDULED_THREAD
        ));
    }

    public function tenminutely() {

        $this->jobQueue->addJob('cron/mentions_cron/queue_mention_keywords_for_update', array(), array(
            'thread' => self::MENTIONS_THREAD
        ));

        $this->jobQueue->addJob('cron/mentions_cron/queue_unrelated_mentions', array(), array(
            'thread' => self::MENTIONS_THREAD
        ));

        $this->jobQueue->addJob('cron/mentions_cron/queue_deleted_mention_keywords', array(), array(
            'thread' => self::MENTIONS_THREAD
        ));

        // get activities
        //$this->jobQueue->addJob('cron/activities_cron/queue_activities_for_update');

        // get crm activities
        $this->jobQueue->addJob('cron/crm_cron/queue_crm_directories_for_update', array(), array(
            'thread' => self::CRM_THREAD
        ));

        $this->jobQueue->addJob('cron/crm_cron/queue_unrelated_crm_activities', array(), array(
            'thread' => self::CRM_THREAD
        ));

        $this->jobQueue->addJob('cron/crm_cron/queue_deleted_crm_directories', array(), array(
            'thread' => self::CRM_THREAD
        ));

    }

    public function hourly() {

        $this->jobQueue->addJob('cron/twitter_cron/run', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));

        $this->jobQueue->addJob('cron/check_subscriptions_cron/run');
        $this->jobQueue->addJob('cron/stripe_subscriptions_cron/run');
    }

    public function fourhourly() {

        // remove deleted keywords with rank
        $this->jobQueue->addJob('cron/google_rank_cron/queue_deleted_keywords', array(), array(
            'thread' => self::GOOGLE_RANK_THREAD
        ));

        // google ranks
        $this->jobQueue->addJob('cron/google_rank_cron/queue_keywords_for_update', array(), array(
            'thread' => self::GOOGLE_RANK_THREAD
        ));

        $this->jobQueue->addJob('cron/social_reports_cron/run', array(), array(
            'thread' => self::SOCIAL_THREAD
        ));

        $this->jobQueue->addJob('cron/social_posts_cron/run', array(), array(
            'thread' => self::POST_CRON_THREAD
        ));

        //RSS
        $this->jobQueue->addJob('cron/rss_cron/run', array(), array(
            'thread' => self::RSS_THREAD
        ));
    }

    public function halfdaily() {

    }

}
