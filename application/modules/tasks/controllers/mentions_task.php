<?php

class Mentions_task extends CLI_controller
{

    /**
     * Grab results for single mention keyword
     *
     * @param array $mention_keyword_array
     * @internal array $keyword_array - mention keyword model arrayed
     *
     */
    public function grabber($mention_keyword_array)
    {
        try {

            $mention_keyword_id = Arr::get($mention_keyword_array, 'id');
            $mention_keyword = new Mention_keyword($mention_keyword_id);

            $error_info = 'mkwid: ' . Arr::get($mention_keyword_array, 'social', 'no soc') . '/' . $mention_keyword_id;

            if (!$mention_keyword->exists()) {
                throw new Exception($error_info . ' doesn\'t exist.');
            }

            if ($mention_keyword->is_deleted) {
                throw new Exception($error_info . ' is set for deletion.');
            }

            if (!$mention_keyword->user_id) {
                throw new Exception($error_info . ' has no user id.');
            }

            $user = new User($mention_keyword->user_id);
            if (!$user->exists()) {
                throw new Exception($error_info . ' has no user');
            }

            $social = Arr::get($mention_keyword_array, 'social');
            if (is_null($social)) {
                throw new Exception($error_info . ' invalid social');
            }

            $user_socials = Access_token::inst()->get_user_socials(
                $user->id,
                $mention_keyword->profile_id
            );

            if (!in_array($social, $user_socials)) {
                throw new Exception($error_info . ' invalid social');
            }

            $this->load->library('mentioner');
            $mentioner = Mentioner::factory($user->id);

            $mention_keyword_data = array_merge($mention_keyword_array, array(
                'keyword'      => $mention_keyword->keyword,
                'exact'        => $mention_keyword->exact,
                'other_fields' => $mention_keyword->other_fields,
            ));

            $access_tokens = Access_token::getAllByTypeAndUserIdAndProfileIdAsArray(
                $social,
                $user->id,
                $mention_keyword->profile_id
            );

            foreach($access_tokens as $access_token) {
                if ($social === 'facebook') {
                    $data = $mentioner->posts($mention_keyword_data, $mention_keyword_array, $access_token);
                } else if ($social === 'twitter') {
                    $data = $mentioner->tweets($mention_keyword_data, $mention_keyword_array, $access_token);
                } else if ($social === 'google') {
                    $data = $mentioner->activities($mention_keyword_data, $mention_keyword_array, $access_token);
                } else if ($social === 'instagram') {
                    $data = $mentioner->tags($mention_keyword_data, $mention_keyword_array, $access_token);
                } else {
                    $data = array();
                }

                if (!is_array($data)) {
                    throw new Exception($error_info . ' no results for mentions, not an array. mkwid: ');
                }

                if ($user->ifUserHasConfigValue('auto_follow', $access_token['id'])) {
                    $autoFollowTwitter = true;
                    /* @var Core\Service\Radar\Radar $radar */
                    $radar = $this->get('core.radar');
                    $conditions = Influencers_condition::allToOptionsArray();
                } else {
                    $autoFollowTwitter = false;
                }

                foreach ($data as $original_id => $row) {

                    $mention = new Mention;
                    $mention->where(array(
                        'mention_keyword_id' => $mention_keyword->id,
                        'original_id'        => $original_id,
                    ))->get(1);

                    $mention->social = $social;
                    $mention->original_id = Arr::get($row, 'original_id');
                    $mention->created_at = Arr::get($row, 'created_at');

                    $message = Arr::get($row, 'message');
                    $trimMessage = (strlen($message)>4000) ? substr($message, 0, 4000) : $message;
                    $mention->message = $trimMessage;

                    $mention->creator_id = Arr::get($row, 'creator_id');
                    $mention->creator_name = Arr::get($row, 'creator_name');
                    $mention->creator_image_url = Arr::get($row, 'creator_image_url');
                    $mention->other_fields = serialize(Arr::get($row, 'other_fields', array()));
                    $mention->source = Arr::get($row, 'source');

                    $mention->access_token_id = $access_token['id'];
                    $mention->profile_id = $mention_keyword->profile_id;

                    $relations = array(
                        'user'            => $user,
                        'mention_keyword' => $mention_keyword,
                    );

                    $saved = $mention->save($relations);


                    if (!$saved) {
                        log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Mention not saved for mkwid: ' . $error_info . ' grabbed: ' . $mention->error->string);
                    } else {
                        switch ($social) {
                            case 'twitter':
                                $followers = (int)Arr::path($row, 'other_fields.creator_followers_count');
                                $retweetCount = (int)Arr::path($row, 'other_fields.retweet_count');

                                if ($autoFollowTwitter
                                    && Arr::path($row, 'other_fields.following') == false
                                    && ($conditions['twitter_followers'] <= $followers
                                        || $conditions['twitter_tweet_retweets'] <= $retweetCount)) {
                                    /* @var Core\Service\Radar\Radar $radar */
                                    $radar->twitterMentionFollow($mention, $access_token);
                                }

                                if ($followers || $retweetCount) {
                                    $mentionTwitter = new Mention_twitter();
                                    $mentionTwitter->followers_count = $followers;
                                    $mentionTwitter->retweet_count = $retweetCount;
                                    $mentionTwitter->mention_id = $mention->id;
                                    $mentionTwitter->save();
                                }


                                break;
                            case 'facebook':
                                $friendsCount = (int)Arr::path($row, 'other_fields.friends_count');
                                $commentsCount = (int)Arr::path($row, 'other_fields.comments');
                                $likesCount = (int)Arr::path($row, 'other_fields.likes');

                                if ($friendsCount || $commentsCount || $likesCount) {
                                    $mentionFacebook = new Mention_facebook();
                                    $mentionFacebook->friends_count = $friendsCount;
                                    $mentionFacebook->comments_count = $commentsCount;
                                    $mentionFacebook->likes_count = $likesCount;
                                    $mentionFacebook->mention_id = $mention->id;
                                    $mentionFacebook->save();
                                }

                                break;
                            case 'google':
                                $peopleCount = (int)Arr::path($row, 'other_fields.people_count');
                                $commentsCount = (int)Arr::path($row, 'other_fields.comments');
                                $plusonersCount = (int)Arr::path($row, 'other_fields.plusoners');
                                $resharersCount = (int)Arr::path($row, 'other_fields.resharers');

                                if ($peopleCount || $commentsCount || $plusonersCount || $resharersCount) {
                                    $mentionGoogle = new Mention_google();
                                    $mentionGoogle->people_count = $peopleCount;
                                    $mentionGoogle->comments_count = $commentsCount;
                                    $mentionGoogle->plusoners_count = $plusonersCount;
                                    $mentionGoogle->resharers_count = $resharersCount;
                                    $mentionGoogle->mention_id = $mention->id;
                                    $mentionGoogle->save();
                                }

                                break;
                            case 'instagram':
                                $commentsCount = (int)Arr::path($row, 'other_fields.instagram_comments');
                                $likesCount = (int)Arr::path($row, 'other_fields.instagram_likes');

                                if ($commentsCount || $likesCount) {
                                    $mentionInstagram = new Mention_instagram();
                                    $mentionInstagram->instagram_comments = $commentsCount;
                                    $mentionInstagram->instagram_likes = $likesCount;
                                    $mentionInstagram->mention_id = $mention->id;
                                    $mentionInstagram->save();
                                }

                                break;
                        }
                    }
                }

                // get socials that were already grabbed
                $grabbed_socials = $mention_keyword->get_grabbed_socials_as_array();

                if (!in_array($social, $grabbed_socials)) {

                    $grabbed_socials[] = $social;
                    $now = date('U');

                    $mention_keyword->grabbed_socials = implode(',', $grabbed_socials);
                    $mention_keyword->grabbed_at = $now;

                    $saved = $mention_keyword->save();

                    if (!$saved) {
                        log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Mention keyword not saved for mkwid: ' . $error_info . ' grabbed: ' . $mention->error->string);
                    }
                }
            }


            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Mentions for mkwid: ' . $error_info . ' grabbed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }

    }

    /**
     * Remove single mention keyword with all collected mentions
     *
     * @param array $mention_keyword_array - mention keyword model arrayed
     *
     * @throws Exception
     */
    public function remove_deleted($mention_keyword_array)
    {
        try {

            $mention_keyword_id = isset($mention_keyword_array['id']) ? $mention_keyword_array['id'] : null;
            $mention_keyword = new Mention_keyword($mention_keyword_id);

            if (!$mention_keyword->exists()) {
                throw new Exception('mkwid: ' . $mention_keyword_id . ' doesn\'t exist.');
            }

            $mention_keyword_mentions = Mention::inst()->get_by_mention_keyword_id($mention_keyword_id);
            $mention_keyword_mentions->delete_all();

            $mention_keyword->delete();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Mention keyword and mentions for mkwid: ' . $mention_keyword_array['id'] . ' deleted');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }

    public function remove_unrelated()
    {
        try {

            $limit = 500;

            $mentions = Mention::inst()->where('user_id IS NULL')->or_where('mention_keyword_id IS NULL')->get($limit)->delete_all();

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Unrelated mentions removed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }


}