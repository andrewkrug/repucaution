<?php

class Image extends DataMapper {
    
    var $table = 'image';
    
    var $has_one = array('file');
    
    var $cascade_delete = FALSE;
    
    var $validation = array(

    );
    

    
    
}