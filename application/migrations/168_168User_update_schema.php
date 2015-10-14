<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_168User_update_schema  extends CI_Migration {

    private $table = 'users';

    public function up()
    {
        $add = array(
            'stripe_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE
            )
        );

        $this->dbforge->add_column($this->table, $add);

    }

    public function down(){
        $this->dbforge->drop_column($this->table, 'stripe_id');
    }

}