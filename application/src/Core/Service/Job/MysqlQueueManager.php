<?php
/**
 * Service for work with queue of jobs 
 * 
 * @author ajorjik
 */

namespace Core\Service\Job;

use Core\Service\Job\Interfaces\JobQueueInterface;
use Core\Service\Job\Interfaces\LauncherInterface;
use Job_queue;
use Exception;
use DateTime;
use Modules;

class MysqlQueueManager implements JobQueueInterface
{
    
    /**
     * @var int
     */
    private $thread = 0;
    
    /**
     * @var int
     */
    private $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * @var int
     */
    private $maxExecutionTime;
    
    /**
     * @var int
     */
    private $maxJobs;
    
    /**
     * @var int
     */
    private $failRetryTime;
    
    /**
     * @var int
     */
    private $runTimeout;
    
    /**
     * @var int
     */
    private $lifeTime;
    
    /**
     * @var object CI
     */
    private $ci;

    /**
     * @var Launcher
     */
    private $launcher;    
    
    /**
     * Load jobs_queue config
     *
     * @param LauncherInterface $launcher   
     */
    public function __construct(LauncherInterface $launcher)
    {
        $this->ci = get_instance();
        $this->ci->config->load('jobs_queue');
        $this->maxExecutionTime = $this->ci->config->config['max_execution_time'];
        $this->maxJobs = $this->ci->config->config['max_jobs'];
        $this->failRetryTime = $this->ci->config->config['fail_retry_time'];
        $this->runTimeout = $this->ci->config->config['run_timeout'];
        $this->lifeTime = $this->ci->config->config['life_time'];
        $this->launcher = $launcher;    
    }
    
    /**
     * Set default thread
     *
     * @param int $thread   
     */
    public function setThread($thread)
    {
        $this->thread = $thread;
    }
        
    /**
     * {@inheritdoc}
     */
    public function addUniqueJob($command, array $args = array(), array $options = array())
    {
        $options['unique'] = true;
        $this->addJob($command, $args, $options);
    }
    
    /**
     * {@inheritdoc}  
     */
    public function addJob($command, array $args = array(), array $options = array())
    {
        $createdAt = new DateTime();
        $job = new Job_queue();
        $argsStr = json_encode($args);
        
        if (!empty($options['unique']) && $job->isExists($command, $argsStr)) {
            return;
        }
        $job->command = $command;
        $job->state = $job::STATUS_PENDING;
        $job->args = $argsStr;
        $job->created_at = $createdAt->format($this->dateFormat);
        
        $maxRetries = (!empty($options['max_retries'])) ?
                            $options['max_retries'] :
                            1;
        $job->max_retries = $maxRetries;
        
        $thread = (!empty($options['thread'])) ?
                            $options['thread'] :
                            $this->thread;
        $job->thread = $thread;
        
        $executeAfter = (!empty($options['execute_after'])) ?
                            $options['execute_after'] :
                            $createdAt;
        $job->execute_after = $executeAfter->format($this->dateFormat);
        
         
        try {
            if (!$job->save()) {
                throw new Exception($job->error->string);
            }
        } catch (Exception $e) {
            log_message('QUEUE_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
            throw $e;    
        }
    
        
    }
    
    /**
     * {@inheritdoc}   
     */
    public function run($thread = 0)
    {
        //run params
        $startRunTime = time();
        $maxRunTime = $startRunTime + $this->maxExecutionTime;
        $countExecutedJobs = 0;
        
        //params for get item of queue
        $threadRun = ($thread) ?: $this->thread;
        $queue = new Job_queue();
        
        do {
            //set timeout for next iteration
            usleep(1000000*$this->runTimeout);
            $countExecutedJobs++;            
            try {

                //getting item
                $item = $queue->getItem($this->failRetryTime, $threadRun);

                if (!$item->exists()) {
                    throw new Exception("No actually items for run at ".date($this->dateFormat, time()));
                }
                //get params for execute 
                $command = $item->command;
                $argsStr = $item->args;
                $args = json_decode($argsStr, true);
                if(empty($command)){
                    $item->state = $item::STATUS_FAILED;
                    $item->save();
                    throw new Exception("Can't run item - data is missing!");
                }
                //start executing
                $startExecute = new DateTime();
                $item->state = $item::STATUS_RUNNING;
                $item->started_at = $startExecute->format($this->dateFormat);
                if(!$item->save()){
                    throw new Exception($item->error->string);
                }
            } catch (Exception $e) {
                continue;
            }    
            
            try{
                $this->launcher->execute($command, $args);    
                $item->state = $item::STATUS_FINISHED;
            } catch (Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__  . $e->getMessage());
                $item->state = ($item->retries <= $item->max_retries) ?
                                       $item::STATUS_INCOMPLETE :
                                       $item::STATUS_FAILED;
            }   
            
            //end executing
            $endExecute = new DateTime();
            $item->retries++;
            $item->closed_at = $endExecute->format($this->dateFormat);
            $item->runtime = $startExecute->diff($endExecute)->format('%s');
            $item->memory_usage = memory_get_usage(false);
            $item->memory_usage_real = memory_get_usage(true);
                       
            if(!$item->save()){
                log_message('TASK_ERROR', 'Item not saved'.$item->error->string);
            }
            
        } while ($countExecutedJobs < $this->maxJobs &&
                    time() < $maxRunTime);
    }
    
    /**
     * Remove jobs that were created earlier specified time
     */
    public function clear()
    {
        $queue = new Job_queue();
        $queue->removeOld($this->lifeTime);
    }
         
}