<?php

class Hard extends MX_Controller {

    public function minutely() {
        $this->minutely_posts();
        $this->minutely_queue_deleted_keywords();
    }

    public function minutely_posts() {
        $posts = Social_post::inst()
            ->where('posting_type', 'schedule')
            ->get();
        foreach($posts as $_post) {
            $post = $_post->to_array();

            $now = strtotime('now');

            $social_post = Social_post::inst((int)$post['id']);

            if( ($post['schedule_date'] > $now-60) && ($post['schedule_date'] < $now+60) ) {
                $post['post_to_social'] = array();
                if($post['posted_to_twitter']) {
                    array_push($post['post_to_social'], 'twitter');
                }
                if($post['posted_to_facebook']) {
                    array_push($post['post_to_social'], 'facebook');
                }

                $this->load->library('Socializer/Socializer');
                $attachment = $social_post->media;
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
            }
        }
    }

    public function minutely_queue_deleted_keywords() {
        $keywords = Keyword::inst()->get_by_is_deleted(1);

        if ( ! $keywords->exists() ) {
            // log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No keywords for removal');
            exit;
        }

        foreach($keywords as $keyword) {
            $args = $keyword->to_array();
            $this->minutely_remove_deleted($args);
        }

        $ids_str = implode(', ', array_values($keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Keywords for removal ids: ' . $ids_str);
        exit;
    }

    public function minutely_remove_deleted($keyword_array) {
        try {

            $keyword_id = isset($keyword_array['id']) ? $keyword_array['id'] : NULL;
            $keyword = new Keyword($keyword_id);

            if ( ! $keyword->exists()) {
                throw new Exception('kwid: ' . $keyword_id . ' doesn\'t exist.');
            }

            $keyword_rank = Keyword_rank::inst()->get_by_keyword_id($keyword_id);
            $keyword_rank->delete_all();

            $keyword->delete();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Keyword and keyword rank for kwid: ' . $keyword->id . ' deleted');

        } catch(Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
            throw $e;
        }
    }

    public function hourly() {
        $this->hourly_queue_keywords_for_update();
    }

    public function hourly_queue_keywords_for_update() {
        $keyword_rank = new Keyword_rank;
        $keyword_rank_ids = $keyword_rank
            ->where('date', date('Y-m-d'))
            ->get()
            ->all_to_single_array('keyword_id');

        if ( empty($keyword_rank_ids) ) {
            $keyword_rank_ids = array(0);
        }

        $keywords = new Keyword;
        $keywords
            ->where('is_deleted', 0)
            ->where_not_in('id', $keyword_rank_ids)
            // ->group_by('keyword')
            ->get();

        if ( ! $keywords->exists() ) {
            // log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No keywords for rank update');
            exit;
        }

        $all_args = array();
        foreach($keywords as $keyword) {
            $args = $keyword->to_array();
            $all_args[] = $args;
        }

        foreach ($all_args as $keyword_array) {
            
            try {
                $this->hourly_queue_keywords_for_update_single($keyword_array);
            } catch(Exception $e) {
                echo "Error " . $e->getMessage() . "\n";
            }

        }

        $ids_str = implode(', ', array_values($keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Keywords for rank update ids: ' . $ids_str);
        exit;
    }

    public function hourly_queue_keywords_for_update_single($keyword_array) {
        try {

            $keyword_id = isset($keyword_array['id']) ? $keyword_array['id'] : NULL;
            $keyword = new Keyword($keyword_id);

            if ( ! $keyword->exists()) {
                throw new Exception('kwid: ' . $keyword_id . ' doesn\'t exist.');
            }

            if ( $keyword->is_deleted) {
                throw new Exception('kwid: ' . $keyword->id . ' is set for deletion.');
            }

            $user_additional = User_additional::inst()->get_by_user_id($keyword->user_id);
            if ( ! $user_additional->exists()) {
                throw new Exception('No user additional model for user: ' . $keyword->user_id . ' ; kwid: ' . $keyword->id);
            }

            if ( ! $user_additional->address_id) {
                throw new Exception('No address id for user: ' . $keyword->user_id . '; kwid: ' . $keyword->id);
            }

            $this->load->config('site_config', TRUE);
            // $google_app_config = $this->config->item('google_app', 'site_config');
            $google_app_config = Api_key::build_config(
                'google',
                $this->config->item('google_app', 'site_config')
            );
            
            $this->load->library('gls');
            $gls = new Gls; // important
            $gls->set(array(
                // 'key' => $google_app_config['simple_api_key'],
                'key' => $google_app_config['developer_key'],
            ));

            $gls->request($keyword->keyword);

            if ($gls->success()) {      
                
                $rank = $gls->location_rank($user_additional->address_id);

                if (is_null($rank)) {
                    throw new Exception('no results for rank. kwid: ' . $keyword->id);
                }

                $keyword_rank = new Keyword_rank;
                $keyword_rank->where(array(
                    'keyword_id' => $keyword->id,
                    'date' => date('Y-m-d')
                ))->get(1);


                $keyword_rank->keyword_id = $keyword->id;
                $keyword_rank->date = date('Y-m-d');
                $keyword_rank->rank = intval($rank);
                $keyword_rank->save();

                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'GLS Rank grabbed for kwid: ' . $keyword->id . ' -> ' . $rank);
                
            } else {

                throw new Exception('Google Rank Grabber Error: ' . $gls->error());
            }

        } catch (Exception $e) {
            //echo 'error: '.$e->getMessage().PHP_EOL;
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
            throw $e;
        }
    }

    public function daily() {
        $this->add();
        $this->soc();
    }

    public function add($args=array()){
        $all_dir_users = Directory_User::get_all();
        $notify = true;

        if(isset($args['notify'])){
            $notify = (bool)$args['notify'];
        }

        foreach($all_dir_users as $_dir_user){
            $args = $_dir_user->to_array();
            $args['notify_status'] = $notify;

            try {
                $this->grabber($args);
            } catch(Exception $e) {}

        }

    }

    public function addByUser($user_id){
        $all_dir_users = Directory_User::get_by_user($user_id);

        foreach($all_dir_users as $_dir_user){
            $args = $_dir_user->to_array();
            try {
                $this->grabber($args);
            } catch(Exception $e) {}
            // Queue_Item::add('tasks/reviews_task/grabber',  $args);
        }
    }

    public function grabber(array $directory_user) {
        try {
            $directory = new DM_Directory($directory_user['directory_id']);
            if(!$directory->exists()) {
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' doesn\'t exist');
            }
            if(!$directory->status){
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' is disabled');
            }

            $link = !empty($directory_user['additional']) ? $directory_user['additional'] : $directory_user['link'];

            $directory_parcer = Directory_Parser::factory($directory->type)->set_url( $link );
            $reviews = $directory_parcer->get_reviews();
        }
        catch(Exception $e) {
            throw $e;
        }

        //$today_midnight = strtotime('-7 day midnight');
        $today_midnight = strtotime('-14 day midnight');

        foreach($reviews as $_review) {

            $review_model = new Review();
            $review_model->from_array($_review);
            $review_model->user_id = $directory_user['user_id'];
            $review_model->directory_id = $directory_user['directory_id'];
            $review_model->posted_date = date('Y-m-d', $_review['posted']);
            $review_model->save();

            // notify user about new review
            if(!empty($directory_user['notify_status']) &&
                $_review['posted'] > $today_midnight &&
                !empty($review_model->id) &&
                !empty($directory_user['user_id'])  &&
                !empty($directory_user['notify'])
            ){

                $obj = Reviews_notification::addOne($directory_user['user_id'], $review_model->id);
                if(!$obj->id){
                    echo 'Error notification: '.date('d-m-Y H:i').' - '.$obj->error->string.PHP_EOL;
                }
            }
        }

    }

    public function soc() {
        $types = array('facebook', 'twitter');
        foreach($types as $type) {
            $tokens = Access_token::getAllByType($type);
            $values_array = array();
            foreach($tokens as $_token) {
                $user_id = (int)$_token->user_id;
                $profiles = $_token->social_group->get();
                foreach($profiles as $profile) {
                    $profile_id = $profile->id;
                    // Queue_Item::add('tasks/social_reports_task/statistic',  $args);
                    $this->load->library('Socializer/socializer');
                    if(!isset($values_array[$type])) {
                        $values_array[$type] = array();
                    }
                    if(!isset($values_array[$type][$user_id][$profile_id])) {
                        $values_array[$type][$user_id][$profile_id] = 0;
                    }
                    if ($type == 'twitter') {
                        /* @var Socializer_Twitter $twitter */
                        $twitter = Socializer::factory('Twitter', $user_id, $_token);
                        $values_array[$type][$user_id][$profile_id] += $twitter->get_followers_count();
                    } elseif($type == 'facebook') {
                        /* @var Socializer_Facebook $facebook */
                        $facebook = Socializer::factory('Facebook', $user_id, $_token);
                        $values_array[$type][$user_id][$profile_id] += $facebook->get_page_likes_count();
                    }
                }

            }
            foreach($values_array as $type => $values) {
                foreach($values as $user_id => $value) {
                    foreach($value as $profile_id => $_value) {
                        $this->_save_values($user_id, $profile_id, $_value, $type);
                    }
                }
            }
        }
    }


    /**
     * @param $user_id
     * @param $profile_id
     * @param $count
     * @param $type
     */
    private function _save_values( $user_id, $profile_id, $count, $type ) {
        $social_value = Social_value::inst();
        $where = array(
            'user_id' => $user_id,
            'profile_id' => $profile_id,
            'date' => date('Y-m-d'),
            'type' => $type
        );
        if(!$social_value->where($where)->get()->id) {
            $social_value->user_id = (int)$user_id;
            $social_value->profile_id = (int)$profile_id;
            $social_value->date = date('Y-m-d');
            $social_value->value = $count;
            $social_value->type = $type;
            $social_value->save();
        }
    }


}