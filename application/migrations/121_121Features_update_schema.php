<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_121Features_update_schema  extends CI_Migration {

    private $_table = 'features';

    public function up()
    {


        $this->dbforge->drop_table($this->_table);

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => true,
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => false,
            ),
            'validation_rules' => array(
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => true,
            ),
            'countable_keyword' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);
        $sql = 'ALTER TABLE '.$this->db->dbprefix .$this->_table.' ADD UNIQUE INDEX(slug)';
        $this->db->query($sql);

    }

    public function down() {

    }

}