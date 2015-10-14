<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_social_values  extends CI_Migration {

    private $_table = 'social_values';

    public function up() {
        $fields = array(
           
            'type' => array(
                'type' => 'ENUM("facebook", "twitter", "google", "linkedin")',    
                'null' => TRUE,
            )           
        );

       $this->dbforge->modify_column($this->_table, $fields);
    }

}