<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_190Drop_info_socials  extends CI_Migration {

    private $_table = 'info_socials';

    public function up() {
        $this->dbforge->drop_table($this->_table);
    }

    public function down() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ),
            'type' => array(
                'type' => 'ENUM("facebook", "twitter")',
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
    }

}