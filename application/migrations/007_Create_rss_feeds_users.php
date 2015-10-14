<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Create_rss_feeds_users  extends CI_Migration {

    private $_table = 'rss_feeds_users';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
            'rss_feed_id' => array(
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $sql = "CREATE UNIQUE INDEX rss_feeds_users_user_id_rss_feed_id_UNIQUE ON " 
            . $this->db->dbprefix . $this->_table . "(user_id ASC, rss_feed_id ASC);";
        $this->db->query($sql);
    }

    public function down() {
        $this->dbforge->drop_table($this->_table);

        $sql = "DROP INDEX rss_feeds_users_user_id_rss_feed_id_UNIQUE ON " 
            . $this->db->dbprefix . $this->_table . ";";
        $this->db->query($sql);
    }

}