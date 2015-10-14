<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_216Update_subscriptions  extends CI_Migration {

    private $_table = 'subscriptions';

    public function up() {
        $fields = array(
            'is_stripe_active' => array(
                'type' => 'bool',
                'default' => 1
            ),
        );
        $this->dbforge->add_column($this->_table, $fields);
    }

    public function down() {
        $this->dbforge->drop_column($this->_table, 'is_stripe_active');
    }

}