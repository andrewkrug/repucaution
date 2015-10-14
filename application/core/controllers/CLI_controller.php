<?php
/**
 * User: dev
 * Date: 16.01.14
 * Time: 15:07
 */

require_once __DIR__.'/Base_Controller.php';

class CLI_controller extends Base_Controller
{
    /**
     * @var \Core\Service\Job\MysqlQueueManager
     */
    protected $jobQueue;

    const SCHEDULED_THREAD = 2;
    const MENTIONS_THREAD = 3;
    const SOCIAL_THREAD = 4;
    const GOOGLE_RANK_THREAD = 5;
    const CRM_THREAD = 6;
    const RSS_THREAD = 7;
    const POST_CRON_THREAD = 8;

    public function __construct(){
        // command line only

        if(!$this->input->is_cli_request()){
            exit;
        }

        parent::__construct();

        $this->jobQueue = $this->get('core.job.queue.manager');
    }

}