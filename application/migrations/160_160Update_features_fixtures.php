<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_160Update_features_fixtures extends CI_Migration {

    private $table = 'features';

    public function up()
    {
        $data = array(
            array(
                'name' => 'Collaboration Team',
                'description' => null,
                'slug' => 'collaboration_team',
                'type' => 'numeric',
                'validation_rules' => json_encode(array('or' => array('lt', 'eq' => 0))),
                'countable_keyword' => 'collaborator',
            ),
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down()
    {
    }

}