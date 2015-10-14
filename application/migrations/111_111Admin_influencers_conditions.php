<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_111Admin_influencers_conditions  extends CI_Migration {

    private $_table = 'influencers_conditions';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'option' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'collate' => 'utf_general_ci',
                'null' => FALSE,
            ),
            'option_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'collate' => 'utf_general_ci',
                'null' => FALSE,
            ),
            'value' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'collate' => 'utf_general_ci',
                'null' => FALSE,
            ),
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($this->_table, TRUE);

        $this->db->query('ALTER TABLE `'.$this->db->dbprefix . $this->_table.'` ADD UNIQUE (`option`);');


        $data = array(
            array(
                'option' => 'twitter_followers',
                'option_name' => 'Twitter followers',
                'value' => 500,
            ),
            array(
                'option' => 'facebook_friends',
                'option_name' => 'Facebook friends',
                'value' => 200,
            ),
            array(
                'option' => 'google+_people',
                'option_name' => 'Google+ people',
                'value' => 200,
            ),
            array(
                'option' => 'twitter_tweet_retweets',
                'option_name' => 'Tweet retweets',
                'value' => 100,
            ),
            array(
                'option' => 'facebook_post_likes',
                'option_name' => 'Facebook post likes',
                'value' => 100,
            ),
            array(
                'option' => 'facebook_post_comments',
                'option_name' => 'Facebook post comments',
                'value' => 100,
            ),
            array(
                'option' => 'google+_post_likes',
                'option_name' => 'Google+ post likes',
                'value' => 100,
            ),
            array(
                'option' => 'google+_post_shares',
                'option_name' => 'Google+ post shares',
                'value' => 30,
            ),
            array(
                'option' => 'google+_post_comments',
                'option_name' => 'Google+ post comments',
                'value' => 50,
            )
        );

        $this->db->insert_batch($this->_table, $data);

    }

    public function down() {
        $this->dbforge->drop_table($this->_table);
    }

}