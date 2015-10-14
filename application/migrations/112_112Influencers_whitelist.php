<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_112Influencers_whitelist  extends CI_Migration {

    private $_table = 'influencers_whitelist';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            ),
            'creator_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'social' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
        $sql = 'ALTER TABLE '.$this->db->dbprefix .$this->_table.' ADD UNIQUE INDEX(user_id, creator_id, social)';
        $this->db->query($sql);

    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}