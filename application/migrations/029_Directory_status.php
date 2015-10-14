<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Directory_status  extends CI_Migration {

    private $_table = 'directories';

    public function up() {
        $fields = array(
            'status' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 1
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);

        $this->db->query("UPDATE `" . $this->db->dbprefix . $this->_table . "` SET status = 1;");

    }

    public function down() {

    }

}