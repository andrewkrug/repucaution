<?php

class Reviews_notification extends DataMapper {


    var $table = 'reviews_notification';

    var $has_one = array(
        'review',
    );

    public $validation = array(
        'user_id' => array(
            'rules' => array(
                'required',
                'unique_pair' => 'review_id'
            )
        ),
        'review_id' => array(
            'rules' => array(
                'required',
            )
        ),
    );

    /**
     * Add new notification
     *
     * @param $user_id
     * @param $review_id
     */
    static public function addOne($user_id, $review_id){
        $obj = new self();
        $obj->from_array(array(
            'user_id' => $user_id,
            'review_id' => $review_id,
            'created' => time()
        ));
        $obj->save();
        return $obj;
    }

    /**
     *
     * @return mixed
     */
    static public function getUniqUsers(){
        $obj = new self();
        return $obj->select('user_id')->distinct()->get()->all_to_array('user_id');
    }

    /**
     * Delete all by user_id
     *
     * @param $user_id
     */
    static public function deleteAllByUser($user_id){
        $obj = new self();
        $sql = 'DELETE * FROM `'.$obj->table.'` WHERE user_id = '.$user_id;
        $obj->query($sql);
    }


    static function getReviewsByUser($user_id){
       $obj = new self();
       return $obj->where('user_id', $user_id)
           ->include_related('review', '*', TRUE, TRUE)
           ->order_by_related('review', 'directory_id')
           ->get_iterated();
    }
    
}