<?php


class Social_posts_category extends DataMapper {

    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $table = 'social_posts_categories';

    var $has_one = array();

    var $has_many = array(
        'social_post' => array(
            'join_self_as' => 'category',
        ),
    );

    function __construct($id = NULL) {
        parent::__construct($id);
    }

    /**
     * Return categories as array
     * Used in filter-by-category select
     * (Select on 'Scheduled Posts' Page
     *
     * @access public
     * @return array
     */
    public static function get_as_array() {
        $result = array();
        $self = new Social_posts_category();
        $categories = $self->get();
        foreach($categories as $_category) {
            $result[$_category->id] = $_category->name;
        }
        return $result;
    }

    /**
     * Get id by category slug
     *
     * @access public
     * @param $slug
     * @return int
     */
    public static function get_id_by_slug($slug) {
        $self = new Social_posts_category();
        $found_category = $self->where(array('slug' => $slug))
            ->get();
        return $found_category->id ? $found_category->id : 0;
    }

    /**
     * Get id by category slug
     *
     * @access public
     * @param $id
     * @return string
     */
    public static function get_slug_by_id($id) {
        $self = new Social_posts_category();
        $found_category = $self->where(array('id' => $id))
            ->get();
        return $found_category->id ? $found_category->slug : '';
    }
    
    /**
     * Get name by category id
     *
     * @access public
     * @param $id
     * @return string
     */
    public static function get_name_by_id($id) {
        $self = new Social_posts_category();
        $found_category = $self->where(array('id' => $id))
            ->get();
        return $found_category->id ? $found_category->name : '';
    }
}
