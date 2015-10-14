<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_154Update_features_fixtures extends CI_Migration {

    private $table = 'features';

    public function up()
    {
        $data = array(
            array(
                'name' => 'CRM',
                'description' => null,
                'slug' => 'crm',
                'type' => 'numeric',
                'validation_rules' => json_encode(array('or' => array('lt', 'eq' => 0))),
                'countable_keyword' => 'directory',
            ),
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down()
    {
    }

}