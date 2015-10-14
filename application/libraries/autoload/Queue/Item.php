<?php
/**
 * User: Dred
 * Date: 26.02.13
 * Time: 17:36
 */

/**
 * Class Queue_Item
 *
 * Usage examples
 *
 * Add new 'task' to queue
 * Queue_Item::add('reviews/get/from', array('arg1', 'arg2') )
 *
 */
class Queue_Item {

    protected $body = '',
    $errors = array(),
    $returns = 0,
    $changed = 0,
    $created = 0;


    public function __construct(){
        $construct_time = time();
        $this->from_array( array(
            'created' => $construct_time,
            'changed' => $construct_time,
        ) );
    }

    /**
     * Check item existing in queue
     *
     * @return bool
     */
    static public function hasItems(){
        return get_instance()->activemq->hasItems();
    }

    /**
     * Get Queue_Item from queue
     *
     * @return Queue_Item
     */
    static public function getItem(){
        $item_data = get_instance()->activemq->getItem();

        // hide error when unserialization fails
        $error_reporting = error_reporting(error_reporting() ^ E_NOTICE);
        $item = unserialize( base64_decode($item_data) );
        error_reporting($error_reporting);

        if ( ! $item) {
            // echo "  Can not unserialize. False is now returned\n";
        }

        return $item;
    }

    /**
     * set object's options from array
     *
     * @param $options
     *
     * @return Queue_Item
     */
    protected function from_array($options){
        $access_options = array(
            'created',
            'changed',
            'returns',
            'errors',
            'body',
        );

        foreach($options as $key => $value){
            if(in_array($key, $access_options)){
                $this->{$key} = $value;
            }
        }


        return $this;
    }

    /**
     * Convert this object to array
     *
     * @return array
     */
    protected function to_array(){
        $options = array(
            'created',
            'changed',
            'returns',
            'errors',
            'body',
        );

        $object_array = array();

        foreach($options as $key){
            if(isset($this->{$key})){

                $_value = $this->{$key};

                switch($key){
                    case 'body':
                        $object_array[$key] = serialize($_value);
                        break;
                    case 'errors':
                            $object_array[$key] = '';
                            foreach($_value as $error){
                                $object_array[$key] .= $error.PHP_EOL;
                            }
                        break;
                    default:
                        $object_array[$key] = $_value;
                        break;
                }

            }

        }



        return $object_array;

    }

    /**
     * Try to start item's function
     *
     * @throws Error
     */
    public function run(){
        if(empty($this->body) || empty($this->body['method'])  ){
            throw new Exception("Can't run item - data is missing!");
        }
        if(empty($this->body['args'])){
            $this->body['args'] = array();
        }

        // echo "  Body: " . $this->body['method'] . ' , ' . serialize($this->body['args']) . "\n";
        // log_message('QUEUE_ITEM',  "  Body: " . $this->body['method'] . ' , ' . serialize($this->body['args']));

        $result = Modules::run($this->body['method'], $this->body['args']);

        if(isset($result)){
            // echo "  Result: " . gettype($result) . " - '" . $result . "'\n";
            // log_message('QUEUE_ITEM',  "  Result: " . gettype($result) . " - '" . $result);
        }

    }

    /**
     * Send current Queue_Item to queue
     */
    protected function send(){
        $data = base64_encode(serialize($this));
        $header = array(
            'persistent'=>'true',
            'suppress_content_length' => 'true',
        );
        // log_message('QUEUE_ITEM',  "  THIS SERIALIZED: " . serialize($this));
        get_instance()->activemq->addItem($data, $header);
    }

    /**
     * Add new Queue_Item to queue
     *
     * @param string $method
     * @param array $args = array()
     */
    static public function add($method,$args = array()){
        $new_item = new self();
        $new_item->from_array( array(
            'body' => array(
                'method' => $method,
                'args' => $args
            )
        ) )->send();
    }

    /**
     * Catch Exception and send item back to queue or save to db
     *
     * @param Exception $e
     */
    public function catchException(Exception $e){
        $this->returns += 1;
        $this->errors[] = $e->getFile().':'.$e->getLine().' - '.$e->getMessage();
        // log_message('QUEUE_ITEM',  $this->returns . '<' . get_instance()->activemq->get_max_attempts());
        if($this->returns < get_instance()->activemq->get_max_attempts()){
            $this->send();
            return;
        }

        $this->save_error();

    }

    /**
     * Save item and errors to DB
     */
    protected function save_error(){
        $mq_error = new MQerror();
        $mq_error->from_array( $this->to_array() );
        $mq_error->save();
    }


}