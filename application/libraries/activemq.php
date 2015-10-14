<?php
/**
 * User: Dred
 * Date: 26.02.13
 * Time: 16:54
 */

/*
    using Stomp library from http://stomp.fusesource.org/
    instead of php stomp.so extension
    to overwrite function "send" 
    because of situation described here: http://www.juretta.com/log/2009/05/24/activemq-jms-stomp/
*/

require_once("stomp/Stomp.php");

class activemq {

    protected $CI,
              $config,
              $stomp;

    public function __construct(){
        $this->CI = get_instance();
        $this->config = $this->CI->config->load('activemq', true);
        try {
            $this->stomp = new Stomp($this->config['server']);
            $this->stomp->connect();
            $this->stomp->subscribe( $this->config['channel_1'] );
        } catch(StompException $e) {
            $this->log_exception($e);
        }
    }

    /**
     * Check item existing in queue
     *
     * @return bool
     */
    public function hasItems(){
        try {
            return $this->stomp->hasFrameToRead();
        } catch(StompException $e) {
            $this->log_exception($e);
        }
    }

    /**
     * Get item from queue
     *
     * @return string
     * @throws Exception
     */
    public function getItem(){
        try {
            $item = $this->stomp->readFrame();
        } catch(StompException $e) {
            throw new Exception($e->getMessage());
        }
        if ( empty($item)) {
            throw new Exception('Channel is empty!');
        }
        $this->stomp->ack($item);
        return $item->body;
    }

    /**
     * Add item to queue
     *
     * @param array $data
     * @param array $headers = array()
     */
    public function addItem($data, $headers = array() ){
        try {
            $res = $this->stomp->send($this->config['channel_1'], $data, $headers);
        } catch(StompException $e) {
            $this->log_exception($e);
        }
    }


    /**
     * Return max attempts count (max fail count)
     *
     * @return int
     */
    public function get_max_attempts(){
        return (int)$this->config['max_attempts'];
    }

    /**
     * Write exception(error) to php log file
     * 
     * @param Exception $e
     */
    protected function log_exception($e) {
        // error_log(date('Y-m-d H:i:s') . 'ACTIVEMQ.PHP:' . $e->getMessage());
        log_message('ACTIVEMQ', $e->getMessage());
    }

}