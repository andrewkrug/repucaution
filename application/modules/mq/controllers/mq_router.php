<?php
/**
 * User: Dred
 * Date: 26.02.13
 * Time: 16:45
 */

class mq_router extends CLI_controller{

    public function index(){

        $this->jobQueue->run(0);
        // echo "Stop MQ Router..." . date('Y-m-d H:i:s') . " started at " .  $start . " \n";
        // log_message('MQ_ROUTER', "Stop MQ Router..." . date('Y-m-d H:i:s') . " started at " .  $start);
    }

    public function convert(){

        $this->jobQueue->run(1);
        // echo "Stop MQ Converter..." . date('Y-m-d H:i:s') . " started at " .  $start . " \n";
        // log_message('MQ_ROUTER', "Stop MQ Converter..." . date('Y-m-d H:i:s') . " started at " .  $start);
    }

    public function scheduled() {
        $this->jobQueue->run(self::SCHEDULED_THREAD);
    }

    public function mentions() {
        $this->jobQueue->run(self::MENTIONS_THREAD);
    }

    public function social() {
        $this->jobQueue->run(self::SOCIAL_THREAD);
    }

    public function google_rank() {
        $this->jobQueue->run(self::GOOGLE_RANK_THREAD);
    }

    public function crm() {
        $this->jobQueue->run(self::CRM_THREAD);
    }

    public function rss() {
        $this->jobQueue->run(self::RSS_THREAD);
    }

    public function post_cron() {
        $this->jobQueue->run(self::POST_CRON_THREAD);
    }
}