<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_132Create_System_Settings extends CI_Migration {

    private $table = 'system_settings';

    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => 255,
            ),
            'data' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => 500,
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->table, TRUE);
        $this->db->query('ALTER TABLE `'.$this->db->dbprefix . $this->table.'` ADD UNIQUE (`slug`);');
    }

    public function down(){}

}