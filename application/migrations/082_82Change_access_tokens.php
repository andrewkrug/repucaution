<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_82Change_access_tokens  extends CI_Migration {

    private $_table = 'access_tokens';

    public function up() {
        $fields = array(
            'type' => array(
                 'type' => 'ENUM("facebook", "twitter", "youtube", "google", "googlea", "linkedin")',
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {
		
		 $fields = array(
            'type' => array(
                 'type' => 'ENUM("facebook", "twitter", "youtube", "google", "googlea")',
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
		
    }

}