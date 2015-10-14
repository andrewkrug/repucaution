<?php
/**
 * Interface for queue of jobs 
 * 
 * @author ajorjik
 */

namespace Core\Service\Job\Interfaces;

interface JobQueueInterface
{
    /**
     * Add job to database
     *
     * @param string $command
     * @param mixed $args arguments for executing of command
     * @param array $options options for running   
     */
    public function addJob($command, array $args = array(), array $options = array());
    
    /**
     * Add unique job to database
     *
     * @param string $command
     * @param mixed $args arguments for executing of command
     * @param array $options options for running   
     */
    public function addUniqueJob($command, array $args = array(), array $options = array());
    
    /**
     * Run job
     *
     * @param int $thread   
     */
    public function run($thread);
        
}