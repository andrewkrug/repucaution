<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Drop_reviews_column  extends CI_Migration {

    private $_table = 'reviews';

    public function up() {
        $fields = array(
            'rank' => array(
                'name' => 'rank',
                'type' => 'FLOAT( 1, 1 )',
                'unsigned' => TRUE,
                'null' => TRUE
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
        $this->dbforge->drop_column($this->_table, 'post_id');
    }

    public function down() {

    }

}