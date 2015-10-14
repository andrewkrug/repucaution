<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Rename_mentions_keyword_id  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {
        $fields = array(
            'keyword_id' => array(
                'name' => 'mention_keyword_id',
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
        $fields = array(
            'mention_keyword_id' => array(
                'name' => 'keyword_id',
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

}