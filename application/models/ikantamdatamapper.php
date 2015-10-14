<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
/** Extended DataMapper
 *  Author Alexey Poliushkin
 */

abstract class IkantamDataMapper extends DataMapper
    implements ArrayAccess
{
    protected $_conf; //contains settings from file configs/model_settings.php for each model
    private static $_tmp_cache = array();
    private $__class__ = null; //called class
    private $_flash_data;

    public $error_prefix = '';
    public $error_suffix = '';

    /** Explain how to get instance of other model from this model
     *  use in instance_factory method
     * @type array  
     * @example
     * Structure : 'Model' => 'methodName'
     */
    public $instance_factory_rules = array();

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->__init__();
    }

    private final function __init__()
    {

        //Define config for models
        $this->__class__ = get_called_class();
        $class = strtolower($this->__class__);
        $this->_conf = ($aux = $this->cache('my_settings')) ? $aux:call_user_func(function
            ($obj, $class)
        {
            if ($obj->config->load('model_settings', true, true))
            {
                $msetting = $obj->config->item('model_settings'); if (isset($msetting[$class]))
                {
                    return $obj->cache('my_settings', $msetting[$class]); }
                return array(); }
        }
        , $this, $class);
        // /config

        $this->_init();
    }

    private static $_instances = array();

    public static function instance($param = null)
    {
        $called_class = get_called_class();

        if (!isset(self::$_instances[$called_class]))
        {
            self::$_instances[$called_class] = new $called_class($param);
        }

        return self::$_instances[$called_class];
    }

    protected function _before_save()
    {
    }
    protected function _after_save()
    {
    }
    protected function _init()
    {
    }

    public function save($object = '', $related_field = '')
    {
        if ($this->_before_save() === false)
        {
            return false;
        }
        $result = parent::save($object, $related_field);
        if ($result)
        {
            $this->_after_save();
        }
        return $result;
    }

    /** Create object
     * @param mixed $id - can be int | array | object | numeric string 
     * @return object
     */
    public static function instance_factory($id = null)
    {
        $called_class = get_called_class();

        if (is_null($id))
        {
            return new $called_class();
        }

        if (is_numeric($id))
        {
            return new $called_class($id);
        }
        elseif ($id instanceof $called_class)
        {
            return $id;
        }
        elseif (is_array($id) || $id instanceof stdClass)
        {
            $inst = new $called_class();
            $id = (array )$id;
            $isAllCorrectId = true;
            foreach ($id as $item)
            {
                if ((int)$item === 0)
                {
                    $isAllCorrectId = false;
                    break;
                }
            }
            if ($isAllCorrectId)
            {
                if(empty($id)) {
                    return $inst;
                }
                $inst->where_in('id', $id); 
                return $inst->get();
            }
            $inst->assignArray($id);
            return $inst;
        }
        elseif ($id instanceof self)
        {
            $class = strtolower($called_class);
            $field = $class . '_id';
            if (isset($id->{$field}))
            {
                return new $called_class($id->{$field});
            }
            else
            {
                $inst = new $called_class();
                $class = get_class($id);

                if (isset($id->instance_factory_rules[$called_class]))
                {
                    $method = $id->instance_factory_rules[$called_class];

                    return $id->{$method}();
                }
                $class = strtolower($class);
                foreach (array('has_many', 'has_one') as $type)
                {
                    foreach ($inst->{$type} as $field => $alias)
                    {

                        if ($alias['class'] === $class)
                        {
                            $ofield = $alias['other_field'];
                            $inst = $id->{$ofield}->get();
                            //$inst->where($field.'_id', $id->id)->get();
                            return $inst;
                        }
                    }
                }

            }
            throw new Exception($called_class .
                '::instance_factory no way to create instance of ' . $called_class . ' from an ' .
                get_class($id));
        }

        return new $called_class();
    }

    /** Retreives item from config or return whole config array
     * @param optional string $item - return config array if empty 
     * @return mixed
     */
    public function getConfig($item = null)
    {
        if (is_null($item))
        {
            return $this->_conf;
        }
        if ($item && isset($this->_conf[$item]))
        {
            return $this->_conf[$item];
        }
        return null;
    }


    /** Fill fields from array  ('field' => 'value')
     * @param array $data 
     * @return self
     */
    public function assignArray(array $data)
    {
        foreach ($data as $field => $value)
        {
            $this->{$field} = $value;
        }
        return $this;
    }
    
    
    /** Fill fields from mixed object  ('field' => 'value')
     * @param mixed $data 
     * @return self
     */
  /*  public function assignData($data)
    {
        $data = (array) $data;
        foreach ($data as $field => $value)
        { 
            if(is_array($value) || is_object($value))  {
                if(!$this->exists()) {
                    $this->assignData($value);
                    $this->all[] = $this; 
                } else {
                    $item = new static();
                    $this->all[] = $item;                
                    $item->assignData($value);                    
                }

            }
            $this->{$field} = $value;
        }
        return $this;
    }    */

    /** CACHE **/

    /** cache function
     * @param  string $key
     * @param mixed $value
     * @return mixed
     * If both params provided, value will be save
     * If first param provided function try to return stored value or return null if nothing found
     */
    public function cache($key, $value = null)
    {
        $key = (string )($key);
        $class = strtolower($this->__class__);
        $key = md5($key . $class);
        if ($key && !is_null($value))
        {
            self::$_tmp_cache[$this->__class__][$key] = $value;
        }
        elseif ($key)
        {
            if (isset(self::$_tmp_cache[$this->__class__][$key]))
            {
                return self::$_tmp_cache[$this->__class__][$key];
            }
            else
            {
                return null;
            }
        }

        return $value;
    }


    public function flushCache($key = null)
    {
        $class = strtolower($this->__class__);

        if (is_string($key))
        {
            $key = md5($key . $class);
        }

        if ($key)
        {
            if (isset(self::$_tmp_cache[$this->__class__][$key]))
            {
                unset(self::$_tmp_cache[$this->__class__][$key]);
            }
        }
        else
        {
            if (isset(self::$_tmp_cache[$this->__class__]))
            {
                unset(self::$_tmp_cache[$this->__class__]);
            }
        }

        return $this;
    }


    /** Retreives cached item form specified model
     * @param string $key
     * @param string $model - class name 
     * @return mixed
     * @example
     * $u = new User(1); $u->cache('uData', 'user value');
     * ...
     * class OtherModel extends IkantamDataMapper {}
     *      public function __construct()  {
     *          $udata  = $this->getCahedItemFromModel('uData', 'user'); // $udata = 'user value'
     * }
     * }
     */
    public function getCachedItemFromModel($key, $model)
    {
        $key = (string )$key;
        $model = (string )$model;
        if ($key && $model)
        {
            $key = md5($key . strtolower($model));
            if (isset(self::$_tmp_cache[$model][$key]))
            {
                return self::$_tmp_cache[$model][$key];
            }
        }

        return null;
    }

    /*public function logCache ()
    * {
    * Logger::log(self::$_tmp_cache);
    * } 
    */

    /**
     * Override parent method. 
     * All relations depends on DB relation rules (CASCADE|RESTRICT|NO ACTION|SET NULL) 
     */
    public function delete()
    {
        if (!$this->exists())
        {
            return false;
        }

        $ids = $this->all_to_single_array('id');

        $sql = "DELETE FROM `" . $this->table . "` WHERE `id` = ?";
        $this->db->query($sql, array($this->id));

        $this->id = null;

        return true;
    }


    //extended validation
    public function validate($object = '', $related_field = '')
    {
        if (is_array($this->validation))
        {
            foreach ($this->validation as $key => $field_validation)
            {
                if (isset($field_validation['rules']['one_of_two']))
                {
                    $this->validation[$key]['rules'][] = 'always_validate';

                }
            }
        }

        return parent::validate($object, $related_field);
    }

    /** !Notice! this is not session storage
     * Model registry
     * @param  string $key
     * @param  mixed $data
     * @return self
     */
    public function setFlashData($key, $data)
    {
        $this->_flash_data[$key] = $data;
        return $this;
    }

    public function getFlashData($key = null, $default = null)
    {
        if (is_null($key))
        {
            $data = $this->_flash_data;
            unset($this->_flash_data);
            return $data;
        }
        if (isset($this->_flash_data[$key]))
        {
            $data = $this->_flash_data[$key];
            unset($this->_flash_data);
            return $data;
        }

        return $default;
    }
    
   /**
    * Return currently loaded items count
    * 
    * @return int
    */
    public function currentCount () 
    {
        return count($this->all);
    }

    public function getErrorString($prefix = '<p>', $sufix = '</p>', $amount = null)
    {
        $result = '';
        $limit = is_int($amount) ? $amount:count($this->error->all);
        if ($limit < 0)
            $limit = 0;
        foreach ($this->error->all as $err)
        {
            if (!$limit--)
            {
                break;
            }
            $result .= $prefix . $err . $sufix;
        }
        return $result;
    }

//*VALIDATION*FUNCTIONS*/

    public function _reg_exp($field, $pattern)
    {
        $fieldname = isset($this->validation[$field]['label']) ? $this->validation[$field]['label']:
            $field;

        return preg_match($pattern, $this->$field) ? true:'The ' . $fieldname .
            ' not match pattern "' . $pattern . '".';
    }

    public function _one_of_two($field, $orfield)
    {
        $fieldname = isset($this->validation[$field]['label']) ? $this->validation[$field]['label']:
            $field;
        $orfieldname = isset($this->validation[$orfield]['label']) ? $this->validation[$orfield]['label']:
            $orfield;

        return (isset($this->{$field}) || isset($this->{$orfield})) ? true:
            "At least one of fields ({$fieldname} or {$orfieldname}) must be specified.";
    }
    //to clean string from html tags
    public function _strip_html_tags($field)
    {
        $this->{$field} = preg_replace('/<[^<|>]+?>/', '', htmlspecialchars_decode($this->{$field}));
        $this->{$field} = htmlentities($this->{$field}, ENT_QUOTES, "UTF-8");
        return true;
    }    

//**********************


    
    


    /**
     * Find all rows
     *
     * @return $this
     */
    static public function findAll()
    {
        $obj = new static;
        return $obj->get_iterated();
    }

    /**
     * Find one entity by id
     *
     * @param $id
     *
     * @return null|static
     */
    static public function findById($id)
    {
        $obj = new static($id);
        if ($obj->exists())
        {
            return $obj;
        }

        return null;
    }

    /**
     * Delete one or more records from DB
     * @param mixed $id - can be numeric or array 
     * @return bool
     */
    public static function deleteById($id)
    {
        if (is_array($id) && array_product($id) === 0)
        {
            return false;
        }
        else
            if ((int)$id === 0)
            {
                return false;
            }

        //$obj = new static();
        //$obj->instance_factory($id)->delete_all();
        static::instance_factory($id)->delete_all();

        return true;
    }
    
	/**
     * Return field value if field exist and not null otherwise return passed default value
     * @param string $field - field name
     * @param mixed $default - default value to return 
     * @return mixed
     */    
    public function fieldOrDefault ($field, $default = null)
    {
        if(isset($this->{$field}) && !is_null($this->{$field}))  {
            return $this->{$field};
        }
                
        return $default;
    }
    
    public function getSession ()
    {
        return $this->CI()->session ;
    }
    
    // ArrayAccess
    
    public function offsetSet($offset, $value) {
        if(!$value instanceof $this){
            throw new Exception('Only accept instance of '.get_called_class());
        } elseif(!$value->exists()){
            throw new Exception('Non existing object');
        }
        
        if (is_null($offset)) {
            $this->all[] = $value;
        } else {
            $this->all[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->all[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->all[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->all[$offset]) ? $this->all[$offset] : null;
    }    

}
