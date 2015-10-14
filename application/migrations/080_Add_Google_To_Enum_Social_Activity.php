<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_Google_To_Enum_Social_Activity extends CI_Migration
{

    private $_table = 'social_activity';

    public function up() {
        $fields = array(
            'social' => array(
                'type' => 'ENUM("facebook", "twitter", "instagram", "google")',
                'null' => TRUE,
            ),
        );
        $this->dbforge->modify_column($this->_table, $fields);
    }

    public function down() {

    }
}