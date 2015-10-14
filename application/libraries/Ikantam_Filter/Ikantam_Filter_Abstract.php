<?php

	/**
     * @abstract _setModel 
     * @abstract _getDefaultFields
     * @abstract _getTable
     * @abstract _query
     * @abstract _count
     */
abstract class Ikantam_Filter_Abstract
{
    const MODE_SELECT = 'select';
    const MODE_COUNT = 'count';

    /**
     * @type string
     * sql query
     * [for internal user]
     * result query stored here
     */
    private $_sql = '{{*mode*}}';
    
    
    /**
     * @type array
     * [for internal user]
     * list of fields which will be always join   
     */
    private $_keep_always_joined = array ();
    
    

    /**
     * @type array
     * [for internal user]
     * Query based on this array     
     */
    protected $_filter = array();

    /**
     * @type array
     * Describe this field in inherit class
     * example:
     * $_joins = array('field' => 'INNER JOIN `other_table` AS `table`')
     * or depended fields
     * $_joins = array('field' => array('depends_on_it' => 'INNER JOIN `other_table` AS `table`'))
     * and then in rules
     * $_rules = array ('field' => '`table`.`some_field`')
     */
    protected $_joins = array();
    
	/**
     * @type array
     * Stores additional select parts i.e. sql code snippets
     * 
     */    
    protected $_select = array  ();
    
	/**
     * @type array 
     * [for internal use]
     * fields already joined
     */    
    private $__joined_fields = array();
    
	/**
     * @type array 
     * [for internal use]
     * available fields
     */    
    private $_all_fields;


    /**
     * Describe this field in inherit class.
     * @type array
     * 
     * Once we create filter it have default methods based on own table fields
     * for example table have 2 fields id and name, then filter have 2 magic methods:
     * $filter->id('<', 5)->name('=', 'Alex')
     * to add more fileds based on $_joins it needs to be described in this array
     * ...
     * $_joins = array('workplace' => 'INNER JOIN `workplaces`  ON  `workplaces`.`worker_id` = `workers`.`id`');
     * $_rules = array( 'workplace_id' => array('workplace' => '`workplaces`.`id`') );
     * so now we can use workplace_id in filter:
     * $filter->workplace_id('IN', array(1,2,7,9)) or
     * $filter->workplace_id('IN', 1,2,3) etc.
     */
    protected $_rules = array();
    
	/**
     * [for internal user]
     */    
    protected $_order;
	/**
     * [for internal user]
     */      
    protected $_group;
	/**
     * [for internal user]
     */      
    private $_group_actions = array(
        'opened' => 0,
        'closed' => 0,
        'map' => array  (
            '*open_group*' => '(',
            '*close_group*' => ')'
        )
    );
	/**
     * [for internal user]
     */      
    private $__joined_selects = array();
    protected $_model;
    private $_options = array();
    private $_modes = array('select' => 'SELECT `{{*table*}}`.* {{*additional_select*}} FROM `{{*table*}}`',
                            'count' => 'SELECT COUNT(*) AS `total_rows` FROM `{{*table*}}`',
                            'count_distinct' => 'SELECT COUNT({{*distinct*}}) AS `total_rows` FROM `{{*table*}}`',
                            );

    /**
     * Fill this field if you want to specify custom class which acceptable to this filter. 
     * If $_acceptClass empty then it will be filled automatically based on class name: Ikantam_Filter_*this will taken*
     */
    protected $_acceptClass;
    private $_fields;
    private $_stored;


    public function __construct($model)
    { 
        if(!isset($model)) {
            throw new Exception('Model required.');
        }
        $this->_checkRules();
        if (!$this->_acceptClass)
        {
            $this->_acceptClass = $this->_autoAcceptClass();
        }

        $this->_setModel($model);
        $this->_setFields();
        
    }
    
    protected function _autoAcceptClass()
    {
       return substr(get_called_class(), 15);      
    }
    
    protected function _checkRules()
    {
        foreach($this->_rules as $key => $value) {
            if(is_array($value) && in_array($key, array_keys($value))) {
                throw new Exception('Self referencing loop for '.$key.': '.$key.' => '.print_r($value, 1));
            }
        }
    }


    /**
     * Set order query
     * you can pass multiple pairs of order based on rule (field, direction)
     * @param  string $field
     * @param  string $direction 
     * @return self
     */
    public function order($field, $direction = '')
    {        
        if(is_array($field)) {
            $params = $field;
        } else {
            $params = func_get_args();
        }
        
        $orders = array();
        
        $count = count($params);
        $i = 0;
        
        for($i; $i< $count; $i++) {
            
            $str = strtoupper($params[$i]);
            if(strpos($str, ' ASC') !== false || strpos($str, ' DESC') !== false) { 
                $parts = explode(' ', $params[$i]); 
                if(count($parts > 1)) {
                  $field = $this->_checkField($parts[0]);
                  if($field) {
                    
                        if(is_array($field)) {
                            reset($field);
                            $this->_join(key($field));
                            $field = $field[key($field)];
                        }
                                            
                        $direction = strtoupper($parts[1]);
                        $direction = (in_array(trim($direction), array('ASC', 'DESC'), true)) ? ' ' . $direction: ' ASC';
                            if (!$this->_order)
                            {
                                $this->_join($parts[0]);
                                $this->_order = ' ORDER BY ' . $field . ' ' . $direction;
                            }
                            else
                            {
                                $this->_join($parts[0]);
                                $this->_order .= ', ' . $field . ' ' . $direction;
                            }

                  } else if(!in_array(strtoupper($parts[0]), array('ASC', 'DESC'))) {
                    throw new Exception('Unknown field "' . $parts[0] . '"');
                  }  
                } 
                    
                
            } else {
                if($field = $this->_checkField($params[$i])) {
                        
                        if(is_array($field)) {
                            reset($field);
                            $this->_join(key($field));
                            $field = $field[key($field)];
                        }
                        
                        $direction = (isset($params[$i+1])) ?  strtoupper($params[$i+1]) : '';
                        $direction = (in_array(trim($direction), array('ASC', 'DESC'), true)) ? ' ' . $direction: ' ASC';
                            if (!$this->_order)
                            {
                                $this->_join($params[$i]);
                                $this->_order = ' ORDER BY ' . $field . ' ' . $direction;
                            }
                            else
                            {
                                $this->_join($params[$i]);
                                $this->_order .= ', ' . $field . ' ' . $direction;
                            }

                   
                } else if(!in_array(strtoupper($params[$i]), array('ASC', 'DESC'))) {
                    throw new Exception('Unknown field "' . $params[$i] . '"');
                }
            }
            
            if(in_array(trim(strtoupper($params[$i])), array('ASC', 'DESC'))) {
                unset($params[$i]);
            }
        }

        return $this;

    }

    /**
     * Group by query
     * @param string $fields 
     * @return 
     */
    public function group($fields)
    {
        $fields = func_get_args();

        foreach ($fields as $field)
        {

            $ofield = $field; //original field
            $field = $this->_checkField($field); // this may change value
            if (!$field)
            {
                throw new Exception('Unknown field "' . $ofield . '"');
            }

            if (!$this->_group)
            {
                $this->_group = ' GROUP BY ' . $field;
            }
            else
            {
                $this->_group .= ', ' . $field;
            }


        }

        return $this;
    }

    
	/**
     * @abstract 
     * Implement this method to set model
     */    
    abstract protected function _setModel($model);
    
	/**
     * @abstract 
     * Implement this method to get table
     */    
    abstract protected function _getTable();    
    
	/**
     * @abstract  
     * Implement this method to define default fields (table fields)
     */    
    abstract protected function _getDefaultFields(); 
    
    abstract protected function _query($sql, $binds);
    
    abstract protected function _count($sql, $binds);
    
    protected function _setFields()
    {
        $this->_fields = array_merge($this->_getDefaultFields(), array_keys($this->_rules)); //!
        
        foreach ($this->_getDefaultFields() as $mfield)
        {
            if (!in_array($mfield, $this->_rules))
            {
                $this->_rules[$mfield] = '`' . $this->_getTable() . '`.`' . $mfield . '`';
            }
        }
    }   
    


    /**
     * $filter->price('<', 10); to get items with price less then 10
     * $filter->price('between', 10, 20); to get between...
     * $filter->price('=',10,20); price 10 or 20 
     * $filter->and_price('=', 10, 20); next condition after price will be connected using AND
     */
    public function __call($method, $args)
    {
        if(strpos($method, 'include') === 0 && !count($args)) {
            
        if (!in_array($method, $this->_select))
        {
            throw new Exception("Invalid method " . get_class($this) . "::" . $method . "(" .
                print_r($args, 1) . ")");
        }            
            $this->alwaysJoin($method);
            return $this;
        }
        
        $mode = substr($method, 0, 4);
        $use_and = false;
        $use_or = false;

        if ($mode == 'and_')
        {
            $method = substr($method, 4);
            $use_and = true;

        }
        else
            if ($mode == 'rem_')
            {
                $method = substr($method, 4);

                unset($this->_filter[$method]);
                unset($this->_filter['AND_'.$method]);
                return $this;
            } else {
              $mode = substr($method, 0, 3);
                if ($mode == 'or_')
                {
                    $method = substr($method, 3);
                    $use_or = true;
        
                }               
            }

        //$args = array_filter($args);
        if (!in_array($method, $this->_fields))
        {
            throw new Exception("Invalid method " . get_class($this) . "::" . $method . "(" .
                print_r($args, 1) . ")");
        }

        if ($use_and)
        {
            $method = 'AND_' . $method;
        }
        
        if ($use_or)
        {
            $method = 'OR_' . $method;
        }        

        $operator = strtoupper(array_shift($args));

        if (!in_array($operator, array(
            '<',
            '>',
            '=',
            'IN',
            'BETWEEN',
            '!=',
            '<=',
            '>=',
            'LIKE',
            'NOT IN',
            'NOT LIKE',
            'IS_NULL')))
        {
            throw new Exception(__method__ . ' invalid operator "' . $operator . '"');
        }

        if ($args[0] === 'and' || $args[0] === 'or')
        {
           $connector = array_shift($args);
            if(isset($args[0]) && is_array($args[0])) {
                $this->_filter[$method][][$connector . $operator][] = $args[0];
            } else {
                $this->_filter[$method][][$connector . $operator][] = $args;
            }
            
        }
        else
        {   if(isset($args[0]) && is_array($args[0])) {
                $this->_filter[$method][][$operator][] = $args[0];
            } else {
                $this->_filter[$method][][$operator][] = $args; 
            }           
             
        }

        return $this;

    }
    
    public function startGroup ()
    {   
        $this->_group_actions['opened'] += 1;
        $this->_filter[] = '*open_group*'; 
        return $this;
    }
    
    public function endGroup ()
    {
        if( $this->_group_actions['opened'] > $this->_group_actions['closed'] )
        {
            $this->_filter[] = '*close_group*';
            $this->_group_actions['closed'] += 1;  
        }
        
        return $this;
    }

    protected function _join($fields)
    {   
        if (!$this->_all_fields)
        {
            $all_fields = array_keys($this->_filter);
            $this->_all_fields = $all_fields;
        }
        else
        {
            $all_fields = $this->_all_fields;
        } 
        
        $all_fields = array_values(array_filter($all_fields, function($v){return !is_integer($v);}));

        if (is_string($fields) && isset($this->_joins[$fields]))
        { 
            $this->_all_fields[] = $fields;
            if (is_array($this->_joins[$fields]))
            {
                $k = key($this->_joins[$fields]);
                $this->_join($k);
                $this->_join(array($fields => $this->_joins[$fields][$k]));
            }
            else
            {
                $this->_join(array($fields => $this->_joins[$fields]));
            }
            return;
        } else if (is_string($fields)) {
            
            if(isset($this->_rules[$fields]) && is_array($this->_rules[$fields])) {
                $key = key($this->_rules[$fields]);
                $this->_join($key);
            }
            
            return ;
        }


        foreach ($fields as $key => $value)
        {
            if ((in_array($key, $all_fields) || in_array('AND_'.$key, $all_fields) || in_array('OR_'.$key, $all_fields) ) && !in_array($key, $this->__joined_fields))
            {
                if (is_string($value))
                {
                    $this->_sql .= ' ' . $value;
                    $this->__joined_fields[] = $key;
                }
                elseif (is_array($value))
                {
                    $to_add = key($value);
                    $this->_all_fields[] = $to_add;
                    if (!isset($this->_joins[$to_add]))
                    {
                        throw new Exception('To join field "' . $key . '" need join rule for field "' . $to_add . '"');
                    }
                    $this->_join(array($to_add => $this->_joins[$to_add]));
                    $this->_join(array($key => $value[$to_add]));

                }
            }
        } 
    }
    
	/** 
     * Check if not closed groups exist an close it 
     * @return voi
     */    
    protected function _completeCloseGroup()
    {
        while($this->_group_actions['opened'] > $this->_group_actions['closed']) {
            $this->endGroup();
        }
    }
    
    
    protected function _getGroupConnector($index)
    {
        $result = ' AND ';
        $started = false;
        
        foreach($this->_filter as $key => $value) {
            if($key === $index) {
                $started = true;
                continue;
            }            
            
            if($started && is_string($key)) {
                if (strpos($key, 'OR_') === 0)
                {    
                    $result = ' OR ';
                } 
                
                break;              
            }
        }
        
        return $result;
    }

    public function apply($limit = null, $offset = null, $mode = 'select')
    { 
        if (!$this->_model)
        {
            return;
        }        

        $this->_completeCloseGroup();
        
        $binds = array();

        $this->_join($this->_joins);

        // join additional fields
        foreach ($this->_filter as $field => $v)
        {
            if(is_integer($field) && in_array($v, array('*open_group*', '*close_group*'))) {
                    
            }
            
            if (strpos($field, 'AND_') === 0)
            {

                $field = substr($field, 4);
            } else  if(strpos($field, 'OR_') === 0) {

                $field = substr($field, 3);                
            }

            if (isset($this->_rules[$field]))
            {
                if (is_array($this->_rules[$field]))
                {
                    $k = key($this->_rules[$field]);
                    $this->_join($k);
                }
            }
        }
        
        foreach($this->_keep_always_joined as $keep_join) {
            $this->_join($keep_join);
        }

        $this->_sql .= ' WHERE ';
        $group_connector = '';
        $filter_count = count($this->_filter);
        
        
        $aux_sql = array(); // sql parts for each fields
       
        foreach ($this->_filter as $field => $qpart)
        {  
           
            if(is_integer($field) && in_array($qpart, array_keys($this->_group_actions['map']))) {
                
                 $group_str = $this->_group_actions['map'][$qpart];
                 if($qpart == '*open_group*') {
                    
                    $group_str = $this->_getGroupConnector($field).$group_str;
                 }

                 $aux_sql[] =  $group_str;
                 continue;  
            } else if(!isset($aux_sql[$field])) {
                $aux_sql[$field] = '';
                
            }
            
            $value_connector = ' OR ';
                                 
            foreach($qpart as $operator_value) {
                
                foreach($operator_value as $operator => $values_set) {
                    
                        if (strpos(strtolower($operator), 'and') === 0)
                        {
                            $value_connector = ' AND ';
                            $operator = substr($operator, 3);
                        } else if (strpos(strtolower($operator), 'or') === 0) {
                            $value_connector = ' OR ';
                            $operator = substr($operator, 2);                            
                        } else { 
                            $value_connector = ' OR ';
                        }
                        
                    if (strtoupper($operator) == 'BETWEEN')
                    { 
                       $values_set = array_shift($values_set);
                       if(count($values_set) < 2) { 
                            throw new Exception('Operator "BETWEEN" needs 2 values');
                       }
                       $values_set = array_values($values_set);
                       $aux_sql[$field] .=  $value_connector.$operator.' ? |AND| ?';
                       $binds[] = $values_set[0];
                       $binds[] = $values_set[1];
                       continue;     
                    } else
                    
                    if (strtoupper($operator) == 'IN' || strtoupper($operator) == 'NOT IN')
                    {
                        $values_set = array_shift($values_set);  
                        $aux_sql[$field] .=  $value_connector.$operator.' (' . str_pad('?', (count($values_set)) * 3 - 2, ', ?', STR_PAD_RIGHT) . ')';

                        foreach($values_set as $bind) {
                            $binds[] = $bind;
                        } 
                        continue;   
                    } elseif (strtoupper($operator) == 'IS_NULL'){                        
                        $aux_sql[$field] .= $value_connector.'IS NULL';                        
                    } else {                                                               

                    foreach((array)$values_set as $value) {

                        if(is_array($value)) {

                            $aux_sql[$field] .= $value_connector.$operator.' '.implode($value_connector.$operator, array_fill(0, count($value), ' ?'));
                            foreach($value as $bind) {
                                $binds[] = $bind;
                            }
                          
                          continue;
 
                        } else {
                            $aux_sql[$field] .= $value_connector.$operator.' ?';
                            $binds[] = $value; 
                        }
                       

                    } }
                     
                }
                
               
            }
            
             
        }
 
 $connect = false;       
 foreach($aux_sql as $field => $ncsql) {
    $group_connector = ' AND ';
    
    if(is_integer($field)) {
        $this->_sql .= $ncsql; 
        if($ncsql !== $this->_group_actions['map']['*close_group*']) {
            $connect = false;  
        }
        
        continue;
    }
    
    if(strpos($field, 'OR_') === 0) {
        $field = substr($field, 3);
        $group_connector = ' OR ';         
    }
    
    
            if (isset($this->_rules[$field]))
            {
                if (is_array($this->_rules[$field]))
                {
                    $k = key($this->_rules[$field]);
                    $field = $this->_rules[$field][$k];
                }
                else
                {
                    $field = $this->_rules[$field];
                }

            }    
    
    
    $ncsql = trim($ncsql);
    if(strpos($ncsql, 'OR') === 0) { $a = 'OR';
        $ncsql = $field.substr($ncsql, 2);   
    } else if(strpos($ncsql, 'AND') === 0) {
        $ncsql = $field.substr($ncsql, 3);
    }
    
    $ncsql = str_replace(' AND ', ' AND '.$field.' ', $ncsql);
    $ncsql = str_replace(' OR ', ' OR '.$field.' ', $ncsql);
    $ncsql = str_replace('|AND|', 'AND', $ncsql); //between
    

    
    if(!$connect) {
        $this->_sql .= '('.$ncsql.')';
        $connect = true;
    } else {
        $this->_sql .= $group_connector.'('.$ncsql.')';
    }
    
 }     

        if (trim(substr($this->_sql, -6)) == 'WHERE')
        {
            $this->_sql .= '1 ';
        }

        $this->_sql .= $this->_group;
        $this->_sql .= $this->_order;

        if ($limit && $offset)
        {
            $this->_sql .= " LIMIT {$offset}, {$limit}";
        }
        elseif ($limit)
        {
            $this->_sql .= " LIMIT {$limit}";
        }
        
        $sql = str_replace('{{*mode*}}', $this->_getModeSql($mode), $this->_sql); //_log($sql);
        $sql = str_replace('{{*additional_select*}}', $this->_getAdditionalSelectFields(), $sql);
  
        $this->clear();

        if ($mode == self::MODE_COUNT)
        {
            $result = $this->_count($sql, $binds);
            unset($this->_options['count_distinct']);
            //_log($this->_model->check_last_query('', '', true));
            return $result;
        }  
        $this->_query($sql, $binds); 
       //_log($this->_model->check_last_query('', '', true));
        return $this->_model;
    }

	/**
     * @param string $distinct - use distinct counting by field 
     * @return int
     */
    public function count($distinct = '')
    {  
        if($distinct) {
           $this->option('count_distinct', $distinct);
        }
        
        $okey = __method__.crc32(serialize($this->_filter).$distinct); 
        
        if(!$count = $this->option($okey)) {
             $count = $this->apply(null, null, self::MODE_COUNT);
             $this->option($okey, $count);
        }
        
        return $count;
    }

    protected function _checkField($field)
    {
        if (!in_array($field, $this->_fields))
        {
            return false;
        }

        if (isset($this->_rules[$field]))
        {
            return $this->_rules[$field];
        }

        return $field;
    }

    protected function _getModeSql($mode)
    {
        
        if($mode == self::MODE_COUNT) {
            if($distinct = $this->option('count_distinct')) {
                $mode = 'count_distinct';                
            }
        }
        
        $source = $this->_modes[$mode];
        
        if(isset($distinct)) {
            $source = str_replace('{{*distinct*}}', 'DISTINCT('.$distinct.')', $source);
        }
        
        if (!$this->_model || !isset($this->_modes[$mode]))
        {
            return '';
        } 
        return str_replace('{{*table*}}', $this->_getTable(), $source);
    }
    
    protected function _getAdditionalSelectFields() {
        $result = "";
        $tmp_joins = array();
        foreach($this->__joined_fields as $jfield) {
            if(array_key_exists($jfield, $this->_select) && $this->_select[$jfield]) {                
                if(!in_array($this->_select[$jfield], $tmp_joins)) {
                    $result .= ", ".$this->_select[$jfield];
                    $tmp_joins[] = $this->_select[$jfield];                   
                }
            }    
        }
        
        
        foreach($this->_keep_always_joined as $jfield) {
            if(array_key_exists($jfield, $this->_select) && $this->_select[$jfield]) {
                
                if(!in_array($this->_select[$jfield], $tmp_joins)) {
                    $result .= ", ".$this->_select[$jfield];
                    $tmp_joins[] = $this->_select[$jfield];                   
                }                
                
            }    
        }        

        return $result;
    }

    public function clear()
    {
        $this->_sql = '{{*mode*}}';
        $this->_all_fields = array();
        $this->__joined_fields = array(); 
        $this->_order = null;
        $this->_group = null;       
        return $this;
    }

    public function getModel()
    {
        return $this->_model;
    }
    
    public function save ($name = 'default') {
        $this->_stored[$name] = array(
            'sql' => $this->_sql,
            'all_fields' => $this->_all_fields,
            'filter' => $this->_filter,
            'joined_fields' => $this->__joined_fields,
            'order' => $this->_order,
            'group' => $this->_group,
            'options' => $this->_options,
            'keep_always_joined' => $this->_keep_always_joined,
        );
        
        return $this;
    }
    
    public function option ($key, $value = null, $default = null)
    {
        if(is_null($value)) {
            if(isset($this->_options[$key])) {
                return $this->_options[$key];
            } else {
                return $default;
            }            
        } else {
            
            $this->_options[$key] = $this->_option_hook($key, $value);
        }
        return $this;

    }
    
    protected function _option_hook($key, $value) { 
        switch($key) {
            case 'count_distinct' :
                return $this->_checkField($value);
            break;
            
            default:
                return $value;
            break;        
        }
    }
    
    public function restore ($name = 'default') {
        if(!empty($this->_stored[$name])) {
            $this->_sql = $this->_stored[$name]['sql'];
            $this->_all_fields = $this->_stored[$name]['all_fields'];
            $this->_filter = $this->_stored[$name]['filter'];
            $this->__joined_fields = $this->_stored[$name]['joined_fields'];
            $this->_order = $this->_stored[$name]['order'];
            $this->_group = $this->_stored[$name]['group'];
            $this->_options = $this->_stored[$name]['options'];
            $this->_keep_always_joined = $this->_stored[$name]['keep_always_joined'];
        }

        
        return $this;
    }
    
    public function alwaysJoin ($table_name)
    {
        if(!in_array($table_name, $this->_keep_always_joined)) {
            $this->_keep_always_joined[] = $table_name;
        }
        return $this;
    }
    
    public function log ($name = '_filter')
    {
        _log($this->{$name});
    }

}
