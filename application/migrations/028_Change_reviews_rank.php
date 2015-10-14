<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_reviews_rank  extends CI_Migration {

    private $_table = 'reviews';

    public function up() {
        $fields = array(
            'rank' => array(
                'name' => 'rank',
                'type' => 'FLOAT( 2, 1 )',
                'unsigned' => TRUE,
                'null' => TRUE
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}