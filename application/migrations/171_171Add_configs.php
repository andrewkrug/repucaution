<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_171Add_configs extends CI_Migration {

    private $_table = 'configs';

    public function up() {
        $data = array(
            array(
                'name' => 'Auto-send welcome message to new followers for twitter',
                'key' => 'auto_send_welcome_message_twitter',
            ),
            array(
                'name' => 'Welcome message text for twitter',
                'key' => 'welcome_message_text_twitter',
            ),
            array(
                'name' => 'Auto-follow new followers for instagram',
                'key' => 'auto_follow_instagram',
            )
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {
        $where = 'key = "welcome_message_text_twitter"';

        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_follow_instagram"';

        $this->db->delete($this->_table, $where);

        $where = 'key = "auto_send_welcome_message_twitter"';

        $this->db->delete($this->_table, $where);
    }

}