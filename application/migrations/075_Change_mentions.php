<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_mentions  extends CI_Migration {

    private $_table = 'mentions';

    public function up() {
        $fields = array(
            'social' => array(
                'type' => 'ENUM("facebook", "twitter", "google")',
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }

}