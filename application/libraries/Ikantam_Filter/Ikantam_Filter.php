<?php
require_once  APPPATH.'libraries/Ikantam_Filter/Ikantam_Filter_Abstract.php';

class Ikantam_Filter extends Ikantam_Filter_Abstract
{
    
    protected function _setModel($model)
    { 
        if(is_array($model)) {
            $model = array_shift($model);
        }
        $given_class = get_class($model);
        if ($this->_acceptClass !== $given_class)
        {
            throw new Exception(' Classes missmatch. Only accept instance of "' . $this->_acceptClass .
                '" class, instance of "' . $given_class . '" class given.');
        }

        $this->_model = $model;

    }
    
    protected function _getDefaultFields ()
    { 
        return $this->getModel()->fields;        
    }
    
    protected function _getTable()
    {
        return $this->getModel()->table;
    }
    
    protected function _query($sql,  $binds)
    {
        return $this->getModel()->query($sql, $binds);
    }
    
    protected function _count($sql, $binds)
    {
        $result = $this->getModel()->db->query($sql, $binds)->result();
        return $result[0]->total_rows;
    }
    
        
}