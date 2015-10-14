<?php
/**
 * User: Dred
 * Date: 14.03.13
 * Time: 11:36
 */
namespace ScriptNS\Formatter;
use Template;

abstract class Review{

    protected $review;

    public function __construct($review){
        $this->review = $review;
    }

    /**
     * Convert Review to format
     */
    abstract public function output();

}

class EmailReview extends Review{


    public function output()
    {
        $review = $this->review->review;
        $review->directory = $review->directory->get()->name;
        $review->rank = (!empty($review->rank)) ? ' '.$review->rank : ' n/a';
        $review->author = (!empty($review->author)) ? ' '.$review->author : '';
        return get_instance()->template
                             ->block(
                                     'notify',
                                     '/templates/email/notify_review',
                                     array('review' => $review)
                                     );
    }
}