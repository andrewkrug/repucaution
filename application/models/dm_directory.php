<?php

class DM_Directory extends DataMapper {

    public $table = 'directories';

    var $has_one = array();

    var $has_many = array(
        'directory_user' => array(
            'other_field' => 'directory',
        ),
        'review' => array(
            'other_field' => 'directory',
        )
    );

    var $validation = array();


    /**
     * Get all active directories sorted by weight - ASC
     *
     * @return Directory
     */
    static public function get_all_sorted(){
       $obj = new self();
       return $obj->where('status',1)->order_by('weight', 'ASC')->get();
    }

    /**
     * Generate css class using directory type
     *
     * @return string
     */
    public function cssClass(){
        if(!$this->exists()){
            return '';
        }
        return strtolower($this->type);
    }

    /**
     * check directory status by type
     *
     * @param $type
     *
     * @return int
     */
    static public function  isActiveByType($type){
        $obj = new self();
        return $obj->where('type', $type)->where('status', 1)->count();
    }

}