<?php

class Social_posts_cron extends CLI_controller {

    /**
     * @access public
     * @return void
     */
    public function run() {
        try {
            log_message('CRON_START', __FUNCTION__ . ' > Start post cron');
            $date = new DateTime('UTC');
            $start_of_day = new DateTime('00:00:00 UTC');
            $end_of_day = new DateTime('23:59:59 UTC');
            /** @var Cron_day $day */
            $day = Cron_day::inst()->where('day', $date->format('l'))->get();
            $cron_posts = $day->social_post_cron->get();
            /** @var Social_post_cron $cron_post */
            foreach($cron_posts as $cron_post) {
                $times = $cron_post->getTimeInUtc();
                $social_posts = $cron_post->social_post
                    ->where(array(
                        'schedule_date >=' => $start_of_day->getTimestamp(),
                        'schedule_date <=' => $end_of_day->getTimestamp(),
                    ))->get();
                if( count($times) &&
                    count($times) != $social_posts->count()) {
                    $timezone = new DateTimeZone($cron_post->timezone);
                    $date->setTimezone($timezone);
                    foreach($times as $time) {
                        $_date = new DateTime($time);
                        $_date->setTimezone($timezone);
                        if(!$date->diff($_date)->invert) {
                            $social_post = new Social_post();
                            $social_post->schedule_date = $_date->getTimestamp();
                            $social_post->user_id = $cron_post->user_id;
                            $social_post->description = $cron_post->description;
                            $social_post->post_cron_id = $cron_post->id;
                            $social_post->post_to_groups = serialize([$cron_post->profile_id]);
                            $social_post->post_to_socials = $cron_post->post_to_socials;
                            $social_post->posting_type = 'schedule';
                            $social_post->timezone = $cron_post->timezone;
                            $social_post->url = $cron_post->url;
                            $social_post->profile_id = $cron_post->profile_id;
                            $social_post->category_id = 0;
                            $social_post->save($cron_post->media->get(1), 'media');
                            log_message('CRON_START', __FUNCTION__ . ' > Schedule post was added. '
                                . 'Socials: ' . implode(', ', $cron_post->post_to_socials) . '; '
                                . 'User id: ' . $cron_post->user_id . '; '
                                . 'Date: ' . $_date->format('Y-m-d H:i') . '; '
                            );
                        }
                    }
                }
            }
        } catch(Exception $e) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }

}