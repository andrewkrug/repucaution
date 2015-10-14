<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_122Features_fixtures extends CI_Migration {

    private $table = 'features';

    public function up()
    {


        $data = array(
            array(
                'name' => 'Social media management',
                'description' => null,
                'slug' => 'social_media_management',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Scheduled posts',
                'description' => null,
                'slug' => 'scheduled_posts',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Brand reputation monitoring',
                'description' => null,
                'slug' => 'brand_reputation_monitoring',
                'type' => 'numeric',
                'validation_rules' => json_encode(array('or' => array('lt', 'eq' => 0))),
                'countable_keyword' => 'keyword',
            ),
            array(
                'name' => 'Brand influencers watch',
                'description' => null,
                'slug' => 'brand_influencers_watch',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Social activity',
                'description' => null,
                'slug' => 'social_activity',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Website traffic monitoring',
                'description' => null,
                'slug' => 'website_traffic_monitoring',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Reviews monitoring',
                'description' => null,
                'slug' => 'reviews_monitoring',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Local search keyword tracking',
                'description' => null,
                'slug' => 'local_search_keyword_tracking',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => '24/7 Tech support',
                'description' => null,
                'slug' => '24_7_tech_support',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Email notifications',
                'description' => null,
                'slug' => 'email_notifications',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Free updates',
                'description' => null,
                'slug' => 'free_updates',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
            array(
                'name' => 'Training and assistance',
                'description' => null,
                'slug' => 'training_and_assistance',
                'type' => 'bool',
                'validation_rules' => null,
                'countable_keyword' => null,
            ),
        );

        $this->db->insert_batch($this->table, $data);

    }

    public function down()
    {
    }

}