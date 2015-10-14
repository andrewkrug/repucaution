<?php

class Mention_instagram extends DataMapper {

    var $table = 'mentions_instagram';
    
    var $has_one = array('mention');

    var $cascade_delete = false;
    
}