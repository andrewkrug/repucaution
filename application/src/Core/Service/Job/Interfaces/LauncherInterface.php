<?php
/**
 * Interface for queue of jobs 
 * 
 * @author ajorjik
 */

namespace Core\Service\Job\Interfaces;

interface LauncherInterface
{
    /**
     * Execute command with arguments
     *
     * @param string $command
     * @param array $args arguments for executing of command
     */
    public function execute($command, array $args = array());
    
       
}