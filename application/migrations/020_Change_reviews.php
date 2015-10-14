<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_reviews  extends CI_Migration {

    private $_table = 'reviews';

    public function up() {
        $fields = array(
            'review_uniq' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {

    }

}