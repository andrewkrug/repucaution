<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_163Add_influencers_conditions  extends CI_Migration {

    private $_table = 'influencers_conditions';

    public function up() {

        $data = array(

            array(
                'option' => 'instagram_likes_count',
                'option_name' => 'Instagram likes',
                'value' => 100,
            ),
            array(
                'option' => 'instagram_comments_count',
                'option_name' => 'Instagram comments',
                'value' => 100,
            )
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {

    }

}