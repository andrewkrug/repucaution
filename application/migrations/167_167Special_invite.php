<?php if ( ! defined('BASEPATH')) die('No direct script access alllowed');

class Migration_167Special_invite extends CI_Migration {

    private $table = 'special_invites';

    public function up()
    {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'plan_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'invite_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'end_date' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->table, TRUE);

    }

    public function down(){}

}