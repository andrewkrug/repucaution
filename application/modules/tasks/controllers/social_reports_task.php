<?php

class Social_reports_task extends CLI_controller {

    /**
     * Collect info from socials
     * (get twitter followers / facebook likes count)
     *
     * @access public
     * @param $user_ids
     */
    public function statistic( $user_ids ) {
        $this->load->library('Socializer/socializer');
        foreach($user_ids as $user_id) {
            $user = new User($user_id);
            $profiles = $user->social_group->get();
            foreach($profiles as $profile) {
                $tokens = $profile->access_token->where_in(
                    'type', array('twitter', 'facebook', 'linkedin', 'google')
                )->get()->all_to_array();
                foreach($tokens as $token) {
                    $result = 0;
                    try {
                        if ($token['type'] == 'twitter') {
                            /* @var Socializer_Twitter $twitter */
                            $twitter = Socializer::factory('Twitter', $user_id, $token);
                            $result += $twitter->get_followers_count();
                            unset($twitter);
                        } elseif($token['type'] == 'facebook') {
                            /* @var Socializer_Facebook $facebook */
                            $facebook = Socializer::factory('Facebook', $user_id, $token);
                            $result += $facebook->get_page_likes_count();
                            unset($facebook);
                        } elseif($token['type'] == 'linkedin') {
                            /* @var Socializer_Linkedin $linkedin */
                            $linkedin = Socializer::factory('Linkedin', $user_id, $token);
                            $result += $linkedin->get_conns_count();
                            unset($linkedin);
                        } elseif($token['type'] == 'google') {
                            /* @var Socializer_Google $google */
                            $google = Socializer::factory('Google', $user_id, $token);
                            $result += $google->getPeopleCount();
                            unset($google);
                        }
                    } catch(Exception $e) {
                        log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                    }
                    $this->_save_values($user_id, $profile->id, $result, $token['type']);
                }
            }
        }
    }

    /**
     * Insert data into 'Social Values' table
     *
     * @access private
     *
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
        $social_value = $social_value->where($where)->get();
        $social_value->user_id = (int)$user_id;
        $social_value->profile_id = (int)$profile_id;
        $social_value->date = date('Y-m-d');
        $social_value->value = $count;
        $social_value->type = $type;
        $social_value->save();
    }

}