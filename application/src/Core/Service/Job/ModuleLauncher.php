<?php
/**
 * Service for work with queue of jobs 
 * 
 * @author ajorjik
 */

namespace Core\Service\Job;

use Core\Service\Job\Interfaces\LauncherInterface;
use Modules;

class ModuleLauncher implements LauncherInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function execute($command, array $args = array())
    {
        Modules::run($command, $args);
    }    
    
}