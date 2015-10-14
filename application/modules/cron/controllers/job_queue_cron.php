<?php

class Job_queue_cron extends CLI_controller {

    /**
     * Clearing outdated jobs in queue
     */
    public function clear() 
    {
        $this->jobQueue->clear();
    }

}