<?php

class Rss_task extends CLI_controller {

    const RSS_LIMIT = 3;

    /**
     * @access public
     *
     * @param $args
     *
     * @throws Exception
     */
    public function send($args) {
        $this->load->library('Simplepie');
        $this->load->library('Socializer/socializer');

        $now = new DateTime('UTC');
        $now_timestamp = $now->getTimestamp();
        $now->modify('-10 minutes');

        $cache_location = APPPATH . 'cache/rss';
        if (!file_exists($cache_location) && !is_writable($cache_location)) {
            $old = umask(0);
            mkdir($cache_location, 0777);
            umask($old);
        }

        $post_date = new \DateTime('UTC');
//        $post_date->modify('30 minutes');
        foreach ($args as $token) {
            try {
                log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'Rss send'.$this->createAddidationalData($token));
                $user = new User($token['user_id']);
                $rss_feeds = Rss_feed::inst()->user_custom_feeds($token['user_id'], $token['profile_id']);
                foreach ($rss_feeds as $rss) {

                    if (!$rss->last_check) {
                        $last_check = $now->getTimestamp();
                    } else {
                        $last_check = $rss->last_check;
                    }

                    $this->simplepie->set_feed_url($rss->link);
                    $this->simplepie->set_cache_location($cache_location);
                    $this->simplepie->init();
                    $this->simplepie->handle_content_type();
                    $rss_feed = $this->simplepie->get_items(0, self::RSS_LIMIT);

                    foreach ($rss_feed as $rss_post) {
                        $title = $rss_post->get_title();
                        $link = $rss_post->get_link();

                        $date = new DateTime($rss_post->get_date());
                        if ($date->getTimestamp() >= $last_check) {
                            $social_post = new Social_post();
                            $social_post->schedule_date = $post_date->getTimestamp();
                            $social_post->user_id = $token['user_id'];
                            $social_post->description = $title;
                            $social_post->post_to_groups = serialize([$token['profile_id']]);
                            $social_post->post_to_socials = serialize([$token['type']]);
                            $social_post->posting_type = 'schedule';
                            $social_post->timezone = $user->timezone;
                            $social_post->url = $link;
                            $social_post->profile_id = $token['profile_id'];
                            $social_post->category_id = 0;
                            $social_post->save();
                            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'RSS: Will posted in '.$post_date->format('d/m/Y h:i:s').' to '.ucfirst($token['type']).'.'.$this->createAddidationalData($token));
                            $post_date->modify('1 minutes');
                        } else {
                            break;
                        }
                    }
                }
            } catch(Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'RSS: ' . $this->createAddidationalData($token) . "\n" . $e->getMessage());
            }
        }
        foreach ($args as $token) {
            $rss_feeds = Rss_feed::inst()->user_custom_feeds($token['user_id'], $token['profile_id']);
            foreach ($rss_feeds as $rss) {
                $rss->last_check = $now_timestamp;
                $rss->save();
            }
        }
    }

    private function createAddidationalData($args) {
        return "\n".'User id: '.$args['user_id'].' Token id: '.$args['id'];
    }
}