<?php

class Scheduled_posts_task extends CLI_controller {

    /**
     * Collect info from socials
     * (get twitter followers / facebook likes count)
     *
     * @access public
     * @param $post
     */
    public function check_for_sending( $post ){

        try {
            $date_now = new DateTime('UTC');
            $now = $date_now->getTimestamp();
            $post['schedule_date'] = (int)$post['schedule_date'];

            $social_post = Social_post::inst((int)$post['id']);

            log_message('TASK_DEBUG', __FUNCTION__ . ' > ' . 'ID - '.$post['id'].'; sch_date - '.$post['schedule_date'].'; now - '.$now);

            if( $post['schedule_date'] <= $now ) {

                $this->load->library('Socializer/Socializer');
                $attachment = $social_post->media;
                if(!is_array($post['post_to_socials'])) {
                    $post['post_to_socials'] = unserialize($post['post_to_socials']);
                }
                if(!is_array($post['post_to_groups'])) {
                    $post['post_to_groups'] = unserialize($post['post_to_groups']);
                }
                if($attachment->id) {
                    $post['image_name'] = basename($attachment->path);
                    if($attachment->type == 'video') {
                        Social_post::inst()->_send_video_to_socials($post, $post['user_id']);
                    } else {
                        Social_post::inst()->_send_to_social($post, $post['user_id']);
                    }
                } else {
                    Social_post::inst()->_send_to_social($post, $post['user_id']);
                }
                $social_post->delete();
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'ID - '.$post['id']);
            }
        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'ID - '.$post['id'] . "\n" . $e->getMessage());
        }
    }
}