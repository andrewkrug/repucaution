<?php
/**
 * User: alkuk
 * Date: 28.05.14
 * Time: 16:29
 */

class Console extends CLI_controller
{

    public function add_job()
    {
        $command = implode('/', func_get_args());
        $this->jobQueue->addJob($command);
        echo "Job added.\n";
    }

}
