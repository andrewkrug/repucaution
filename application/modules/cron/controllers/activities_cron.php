<?php

class Activities_cron extends CLI_controller {

    /**
     * Check for updates of user activity
     * And set for mentions update if exist
     * 
     * daily
     */
    public function queue_activities_for_update() {
        
        $this->jobQueue->addJob('tasks/activity_task/grabber');
            
    }
}