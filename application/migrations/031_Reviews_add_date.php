<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Reviews_add_date  extends CI_Migration {

    private $_table = 'reviews';

    public function up() {
        $fields = array(
            'posted_date' => array(
                'type' => 'DATE'
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}