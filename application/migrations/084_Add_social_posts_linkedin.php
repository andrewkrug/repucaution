<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Add_social_posts_linkedin extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $add = array(
            'posted_to_linkedin' => array(
                'type' => "bool",
                'default' => 0
            )
        );

        $modify = array(
            'title'=> array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
        );

        $this->dbforge->add_column($this->_table, $add);
        $this->dbforge->modify_column($this->_table, $modify);
    }

    public function down() {
		$this->dbforge->drop_column($this->_table, 'posted_to_linkedin');
		$this->dbforge->drop_column($this->_table, 'title');
    }

}