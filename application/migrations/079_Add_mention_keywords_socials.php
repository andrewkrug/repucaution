<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_mention_keywords_socials  extends CI_Migration {

    private $_table = 'mention_keywords';

    public function up() {

        $fields = array(
            'grabbed_socials' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => 255,
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'grabbed_socials');
    }
}