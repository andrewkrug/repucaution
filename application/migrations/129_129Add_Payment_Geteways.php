<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_129Add_Payment_Geteways extends CI_Migration {

    private $table = 'payment_geteways';

    public function up()
    {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => 255,
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => 255,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => false,
                'unsigned' => true,
                'constraint' => 1,
            ),
            'data' => array(
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => 500,
            ),
            'created_at' => array(
                'type' => 'INT',
                'null' => true,
                'unsigned' => true,
                'constraint' => 11,
            ),
            'updated_at' => array(
                'type' => 'INT',
                'null' => true,
                'unsigned' => true,
                'constraint' => 11,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->table, TRUE);
        $this->db->query('ALTER TABLE `'.$this->db->dbprefix . $this->table.'` ADD UNIQUE (`slug`);');
    }

    public function down(){}

}