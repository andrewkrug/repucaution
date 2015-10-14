<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_keyword_rank_rank  extends CI_Migration {

    private $_table = 'keyword_rank';

    public function up() {
        $fields = array(
            'rank' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => FALSE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}