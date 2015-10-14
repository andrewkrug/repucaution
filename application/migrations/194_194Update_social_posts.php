<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_194Update_social_posts extends CI_Migration {

    private $_table = 'social_posts';

    public function up() {
        $fields = array(
            'post_to_groups' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            )
        );

        $this->dbforge->add_column($this->_table, $fields);

        $this->dbforge->drop_column($this->_table, 'posted_to_facebook');
        $this->dbforge->drop_column($this->_table, 'posted_to_twitter');
        $this->dbforge->drop_column($this->_table, 'posted_to_linkedin');
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'post_to_groups');

        $fields = array(
            'posted_to_facebook' => array(
                'type' => "bool",
                'default' => 0
            ),
            'posted_to_twitter' => array(
                'type' => "bool",
                'default' => 0
            ),
            'posted_to_linkedin' => array(
                'type' => "bool",
                'default' => 0
            ),
        );

        $this->dbforge->add_column($this->_table, $fields);
    }

}