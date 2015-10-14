<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_198Features_fixtures extends CI_Migration {

    private $table = 'features';

    public function up() {
        $data = array(
            array(
                'name' => 'Profiles count',
                'description' => null,
                'slug' => 'profiles_count',
                'type' => 'numeric',
                'validation_rules' => json_encode(array('or' => array('lt', 'eq' => 0))),
                'countable_keyword' => 'profile',
            ),
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down() {

    }

}