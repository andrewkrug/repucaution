<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_192Create_social_groups_access_tokens extends CI_Migration {

    private $_table = 'social_groups_access_tokens';

    public function up() {
        $fields = array(
            'social_group_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'access_token_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
        );

        $this->dbforge->add_key(array('social_group_id', 'access_token_id'), TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}