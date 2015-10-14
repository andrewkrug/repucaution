<?php

class Reviews_cron extends CLI_controller {

    //daily
    public function run() {
        $this->jobQueue->addJob('tasks/reviews_task/add');
    }

}