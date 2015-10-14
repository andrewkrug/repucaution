<?php

class Google_rank_task extends CLI_controller {

    /**
     * Grab results for single keyword
     * 
     * @param $keyword_array (array) - keyword model arrayed
     * @throws Exception
     */
    public function grabber($keyword_array) {

        try {

            $keyword_id = isset($keyword_array['id']) ? $keyword_array['id'] : NULL;
            $keyword = new Keyword($keyword_id);

            if ( ! $keyword->exists()) {
                throw new Exception('kwid: ' . $keyword_id . ' doesn\'t exist.');
            }

            if ( $keyword->is_deleted) {
                throw new Exception('kwid: ' . $keyword->id . ' is set for deletion.');
            }

            $user_additional = User_additional::inst()->get_by_user_and_profile($keyword->user_id, $keyword->profile_id);
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

    /**
     * Remove single keyword with all collected rank
     * 
     * @param $keyword_array (array) - keyword model arrayed
     * @throws Exception
     */
    public function remove_deleted($keyword_array) {
        try {

            $keyword_id = isset($keyword_array['id']) ? $keyword_array['id'] : NULL;
            $keyword = new Keyword($keyword_id);

            if ( ! $keyword->exists()) {
                throw new Exception('kwid: ' . $keyword_id . ' doesn\'t exist.');
            }

            $keyword_rank = Keyword_rank::inst()->get_by_keyword_id($keyword_id);
            $keyword_rank->delete_all();

            $keyword->delete();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Keyword and keyword rank for kwid: ' . $keyword_id . ' deleted');

        } catch(Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
            throw $e;
        }
    }


}