<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_231Add_System_settings_fixtures extends CI_Migration {

    private $table = 'system_settings';

    public function up()
    {
        $data = array(
            array(
                'slug' => 'trial_enabled',
                'data' => 1,
            )
        );

        $this->db->insert_batch($this->table, $data);
    }

    public function down(){}

}