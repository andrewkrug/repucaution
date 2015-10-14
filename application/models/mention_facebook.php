<?php

class Mention_facebook extends DataMapper {

    var $table = 'mentions_facebook';
    
    var $has_one = array('mention');

    var $cascade_delete = false;
    
}