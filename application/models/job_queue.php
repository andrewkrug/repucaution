<?php

class Job_queue extends DataMapper {

    const STATUS_PENDING = 0;
    const STATUS_CANCELLED = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FINISHED = 3;
    const STATUS_FAILED = 4;
    const STATUS_TERMINATED = 5;
    const STATUS_INCOMPLETE = 6;
    
    var $table = 'job_queue';
    
    var $validation = array();
    
    /**
     * @var string
     */
    private $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * Check job with same args and command allready exist
     *
     * @param string $command
     * @param string $args
     * @return bool    
     */
    public function isExists($command, $args)
    {
        return (bool)$this->where('command', $command)
                    ->where('args', $args)
                    ->where_in('state', array(
                                              self::STATUS_PENDING,
                                              self::STATUS_RUNNING,
                                              self::STATUS_TERMINATED,
                                              self::STATUS_INCOMPLETE,
                                              ))
                    ->count();
    }
    
    /**
     * Get job actually for run
     *
     * @param int $thread 
     * @param int $failRetryTime   
     */
    public function getItem($failRetryTime = 0 ,$thread = 0)
    {
        $now = new DateTime();
        $retryTime = new DateTime('-'.$failRetryTime.'sec');
        
        return $this->group_start()
                        ->where('state',self::STATUS_INCOMPLETE)
                        ->where('closed_at <=', $retryTime->format($this->dateFormat))                       
                    ->group_end()
                    ->or_group_start()
                        ->or_where('state', self::STATUS_PENDING)
                    ->group_end()
                    ->where('execute_after <=', $now->format($this->dateFormat))
                    ->where('thread', $thread)
                    ->order_by('created_at', 'asc')
                    ->get(1);   
    }
    
    /**
     * Remove jobs that have created more than $lifeTime param time ago
     *
     * @param int $lifeTime time of job's actuality    
     */
    public function removeOld($lifeTime)
    {
        $limit = new DateTime('-'.$lifeTime.'sec');
        $result = $this->where('created_at <', $limit->format($this->dateFormat))->get();
            foreach($result as $job) {
               $job->delete();
            }
             
    }
}
