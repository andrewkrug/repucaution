<?php if ( ! defined('BASEPATH')) die('No direct script access alllowed');

class Migration_164Configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $data = array(
            array(
                'name' => 'Auto-follow for twitter',
                'key' => 'auto_follow_twitter',
            )
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}