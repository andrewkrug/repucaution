<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_api_keys  extends CI_Migration {

    private $_table = 'api_keys';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'social' => array(
                'type' => 'ENUM("facebook", "twitter", "youtube", "google")',
                'null' => TRUE,
            ),
            'key' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
            ),
            'value' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $this->fill_with_defaults();
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

    protected function fill_with_defaults() {

        $data = array(
            array('social' => 'facebook', 'key' => 'appId', 'name' => 'App Id'),
            array('social' => 'facebook', 'key' => 'secret', 'name' => 'App Secret'),
            array('social' => 'twitter', 'key' => 'consumer_key', 'name' => 'Consumer Key'),
            array('social' => 'twitter', 'key' => 'consumer_secret', 'name' => 'Consumer Secret'),
            array('social' => 'google', 'key' => 'client_id', 'name' => 'Client Id'),
            array('social' => 'google', 'key' => 'secret', 'name' => 'Client Secret'),
            array('social' => 'google', 'key' => 'developer_key', 'name' => 'Api key (Simple API access)'),
        );

        $this->db->insert_batch($this->_table, $data);
    }

}