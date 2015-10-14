<?php

class Mention_twitter extends DataMapper {

    var $table = 'mentions_twitter';
    
    var $has_one = array('mention');

    
}