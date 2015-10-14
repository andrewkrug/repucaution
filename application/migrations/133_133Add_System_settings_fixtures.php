<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_133Add_System_settings_fixtures extends CI_Migration {

    private $table = 'system_settings';

    public function up()
    {
        $data = array(
            array(
                'slug' => 'payment_enable',
                'data' => 0,
            ),
            array(
                'slug' => 'payment_sandbox_mode',
                'data' => 1,
            ),
        );

        $this->db->insert_batch($this->table, $data);
    }

    public function down(){}

}