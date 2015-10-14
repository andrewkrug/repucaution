<?php

class Instagram_task extends CLI_controller {

    /**
     * Update Instagram followers in DB.
     *
     * @access public
     * @param $args
     */
    public function updateFollowers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];

        $instagram = $this->inicializeInstagramSocializer($user_id, $args);

        $instagramId = $instagram->getInstanceId();
        $followers = $instagram->userFollowedBy($instagramId);
        foreach($followers->data as $follower) {
            /* @var Instagram_follower $instagramFollower */
            $instagramFollower = Instagram_follower::create()
                ->where('follower_id', $follower->id)
                ->where('user_id', $user_id)
                ->where('access_token_id', $access_token_id)
                ->get();
            if (!$instagramFollower->id) {
                $instagramFollower = new Instagram_follower();
                $instagramFollower->setFollowerId($follower->id);
                $instagramFollower->setUserId($user_id);
                if ($user->ifUserHasConfigValue('auto_follow', $access_token_id)) {
                    $instagramFollower->setNeedFollow(true);
                }
                $instagramFollower->setAccessTokenId($access_token_id);
                $instagramFollower->save();
            }
        }
    }

    /**
     * Follow new follower in Instagram.
     *
     * @access public
     * @param $args
     */
    public function followNewFollowers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_follow', $access_token_id)) {
            return;
        }

        $instagram = $this->inicializeInstagramSocializer($user_id, $args);

        /* @var Instagram_follower[] $followers */
        $followers = $user
            ->instagram_follower
            ->where('need_follow', true)
            ->where('access_token_id', $access_token_id)
            ->get();
        foreach($followers as $follower) {
            $answer = $instagram->modifyUserRelationship($follower->follower_id, 'follow');
            if ($answer->meta->code != 200) {
                echo $answer->meta->error_message;
                if ($answer->meta->error_type == 'OAuthPermissionsException') {
                    break;
                }
            } else {
                $follower->setNeedFollow(false);
                $follower->save();
            }
        }
    }

    /**
     * @param $user_id
     * @param $token
     * @return Socializer_Instagram
     * @internal param $params
     */
    private function inicializeInstagramSocializer($user_id, $token) {
        $this->load->library('Socializer/socializer');
        /* @var Socializer_Instagram $instagram */
        $instagram = Socializer::factory('Instagram', $user_id, $token);
        return $instagram;
    }

}