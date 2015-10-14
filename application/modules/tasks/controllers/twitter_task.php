<?php

class Twitter_task extends CLI_controller {

    /**
     * Update Twitter followers in DB.
     *
     * @access public
     * @param  array $args
     */
    public function updateFollowers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        
        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $user->twitter_follower
            ->where('still_follow', true)
            ->where('access_token_id', $access_token_id)
            ->update([
                'still_follow' => false
            ]);

        $answer = $twitter->get_followers();
        if ($answer->errors) {
            foreach($answer->errors as $err) {
                log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
            }
        } else {
            $followersIds = $answer->ids;
            Twitter_follower::create()
                ->where_in('follower_id', $followersIds)
                ->where('user_id', $user_id)
                ->where('access_token_id', $access_token_id)
                ->update([
                    'unfollow_time' => null,
                    'still_follow' => true
                ]);
            $exists_followers_ids = Twitter_follower::create()
                ->where_in('follower_id', $followersIds)
                ->where('user_id', $user_id)
                ->where('access_token_id', $access_token_id)
                ->get()->all_to_array('follower_id');
            $new_followers_ids = [];
            foreach($exists_followers_ids as $exists_followers_id) {
                if(!in_array($exists_followers_id['follower_id'], $followersIds)) {
                    $new_followers_ids[] = $exists_followers_id['follower_id'];
                }
            }
            foreach ($new_followers_ids as $new_followers_id) {
                $twitterFollower = new Twitter_follower();
                $twitterFollower->setFollowerId($new_followers_id);
                $twitterFollower->setUserId($user_id);
                if ($user->ifUserHasConfigValue('auto_send_welcome_message', $access_token_id)) {
                    $twitterFollower->setNeedMessage(true);
                }
                if ($user->ifUserHasConfigValue('auto_follow', $access_token_id)) {
                    $twitterFollower->setNeedFollow(true);
                }
                $twitterFollower->setStillFollow(true);
                $twitterFollower->setAccessTokenId($access_token_id);
                $twitterFollower->save();
            }

            $unfollowers_query = [
                'still_follow' => false,
                'unfollow_time' => null,
                'start_follow_time' => null,
                'end_follow_time' => null,
                'access_token_id' => $access_token_id
            ];

            $new_unfollowers_count = $user->twitter_follower
                ->where($unfollowers_query)
                ->count();

            $user->twitter_follower
                ->where($unfollowers_query)
                ->update([
                    'unfollow_time' => time()
                ]);
            Social_analytics::updateAnalytics(
                $access_token_id,
                Social_analytics::NEW_UNFOLLOWERS_ANALYTICS_TYPE,
                $new_unfollowers_count
            );
        }
    }

    /**
     * Random retweet followers tweet.
     *
     * @access public
     * @param $args
     */
    public function randomRetweet($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_retweet', $access_token_id)) {
            return;
        }

        log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start retweet.'.$this->getDebugInfo($user, $access_token_id));
        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $date = new DateTime('- 7 days UTC');

        $criteriaOr = [];

        $min_favourites = $user->ifUserHasConfigValue('auto_retweet_min_favourites_count', $access_token_id);
        if(!$min_favourites) {
            $min_favourites = 10;
        }
        $max_favourites = $user->ifUserHasConfigValue('auto_retweet_max_favourites_count', $access_token_id);
        $min_retweets = $user->ifUserHasConfigValue('auto_retweet_min_retweets_count', $access_token_id);
        if(!$min_retweets) {
            $min_retweets = 10;
        }
        $max_retweets = $user->ifUserHasConfigValue('auto_retweet_max_retweets_count', $access_token_id);
        if($min_favourites && $max_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => 'between',
                'value' => [$min_favourites, $max_favourites]
            ];
        } elseif($min_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => '>=',
                'value' => $min_favourites
            ];
        } elseif($max_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => '<=',
                'value' => $max_favourites
            ];
        }

        if($min_retweets && $max_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => 'between',
                'value' => [$min_retweets, $max_retweets]
            ];
        } elseif($min_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => '>=',
                'value' => $min_retweets
            ];
        } elseif($max_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => '<=',
                'value' => $max_retweets
            ];
        }

        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', true)
            ->where('access_token_id', $access_token_id)
            ->order_by('last_check', 'ASC')
            ->get(5);

        foreach($followers as $follower) {
            /* @var array $tweets */
            $tweets = $twitter->get_tweets(array(
                'user_id' => $follower->follower_id,
                'exclude_replies' => true,
                'trim_user' => true,
                'only_one' => true,
                'count' => 10,
                'criteriaOr' => $criteriaOr,
                'criteriaAnd' => array(
                    array(
                        'param_name' => 'retweeted',
                        'comparison_sign' => '=',
                        'value' => false
                    ),
                    array(
                        'param_name' => 'created_at',
                        'comparison_sign' => '>',
                        'value' => $date->getTimestamp()
                    )
                )
            ));
            if(!is_array($tweets)) {
                log_message('TASK_ERROR', __FUNCTION__ . 'Twitter: ' . $tweets);
            } else {
                $errors = false;
                $retweets_count = 0;
                foreach ($tweets as $tweet) {
                    $answer = $twitter->retweet($tweet->id);
                    if ($answer->errors) {
                        $errors = true;
                        foreach($answer->errors as $err) {
                            log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                        }
                    } else {
                        $retweets_count++;
                    }
                }
                if(!$errors) {
                    $follower->save();
                }
                Social_analytics::updateAnalytics(
                    $access_token_id,
                    Social_analytics::RETWEETS_ANALYTICS_TYPE,
                    $retweets_count
                );
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: end retweet. '
                    .$retweets_count.' tweets were retweeted. '."\n"
                    .$this->getDebugInfo($user, $access_token_id)
                );
            }
        }
    }

    /**
     * Random retweet followers tweet.
     *
     * @access public
     * @param $args
     */
    public function randomFavourite($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_favourite', $access_token_id)) {
            return;
        }

        log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start favourite.'.$this->getDebugInfo($user, $access_token_id));
        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $date = new DateTime('- 7 days UTC');

        $criteriaOr = [];

        $min_favourites = $user->ifUserHasConfigValue('auto_favourite_min_favourites_count', $access_token_id);
        $max_favourites = $user->ifUserHasConfigValue('auto_favourite_max_favourites_count', $access_token_id);
        $min_retweets = $user->ifUserHasConfigValue('auto_favourite_min_retweets_count', $access_token_id);
        $max_retweets = $user->ifUserHasConfigValue('auto_favourite_max_retweets_count', $access_token_id);
        if($min_favourites && $max_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => 'between',
                'value' => [$min_favourites, $max_favourites]
            ];
        } elseif($min_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => '>=',
                'value' => $min_favourites
            ];
        } elseif($max_favourites) {
            $criteriaOr[] = [
                'param_name' => 'favorite_count',
                'comparison_sign' => '<=',
                'value' => $max_favourites
            ];
        }

        if($min_retweets && $max_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => 'between',
                'value' => [$min_retweets, $max_retweets]
            ];
        } elseif($min_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => '>=',
                'value' => $min_retweets
            ];
        } elseif($max_retweets) {
            $criteriaOr[] = [
                'param_name' => 'retweet_count',
                'comparison_sign' => '<=',
                'value' => $max_retweets
            ];
        }

        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', true)
            ->where('access_token_id', $access_token_id)
            ->order_by('last_check', 'ASC')
            ->get(5);
        foreach($followers as $follower) {
            /* @var array $tweets */
            $tweets = $twitter->get_tweets(array(
                'user_id' => $follower->follower_id,
                'exclude_replies' => true,
                'trim_user' => true,
                'only_one' => false,
                'count' => 10,
                'criteriaOr' => $criteriaOr,
                'criteriaAnd' => [
                    [
                        'param_name' => 'favorited',
                        'comparison_sign' => '=',
                        'value' => false
                    ],
                    [
                        'param_name' => 'created_at',
                        'comparison_sign' => '>',
                        'value' => $date->getTimestamp()
                    ]
                ]
            ));
            if(!is_array($tweets)) {
                log_message('TASK_ERROR', __FUNCTION__ . 'Twitter: ' . $tweets);
            } else {
                $tweetsCount = count($tweets);
                $errors = false;
                $favourites_count = 0;
                if ($tweetsCount >= 2) {
                    $randomTweetIndex = rand(1, $tweetsCount) - 1;
                    $answer = $twitter->favorite($tweets[$randomTweetIndex]->id);
                    if ($answer->errors) {
                        $errors= true;
                        foreach($answer->errors as $err) {
                            log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                        }
                    } else {
                        $favourites_count++;
                    }
                }
                if(!$errors) {
                    $follower->save();
                }
                Social_analytics::updateAnalytics(
                    $access_token_id,
                    Social_analytics::FAVOURITES_ANALYTICS_TYPE,
                    $favourites_count
                );
                log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: end favourite. '
                    .$favourites_count.' tweets were favourited. '."\n"
                    .$this->getDebugInfo($user, $access_token_id)
                );
            }
        }
    }

    /**
     * Send welcome message to new followers in Twitter.
     *
     * @access public
     * @param $args
     */
    public function sendWelcomeMessage($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_send_welcome_message', $access_token_id)) {
            return;
        }

        $twitter = $this->inicializeTwitterSocializer($user_id, $args);
        
        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', true)
            ->where('need_message', true)
            ->where('access_token_id', $access_token_id)
            ->get();
        foreach($followers as $follower) {
            $answer = $twitter->direct_message(array(
                'user_id' => $follower->follower_id,
                'text' => $user->ifUserHasConfigValue('welcome_message_text', $access_token_id)
            ));
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $follower->setNeedMessage(false);
                $follower->save();
            }
        }
    }

    /**
     * Follow new follower in Twitter.
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

        log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start follow new followers.'.$this->getDebugInfo($user, $access_token_id));
        $twitter = $this->inicializeTwitterSocializer($user_id, $args);
        
        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', true)
            ->where('need_follow', true)
            ->where('start_follow_time', null)
            ->where('end_follow_time', null)
            ->where('access_token_id', $access_token_id)
            ->get();
        $new_followers_count = 0;
        foreach($followers as $follower) {
            $answer = $twitter->follow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $new_followers_count++;
                $follower->setNeedFollow(false);
                $follower->save();
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_FOLLOWING_ANALYTICS_TYPE,
            $new_followers_count
        );
        log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: finish follow new followers. Added '
            .$new_followers_count.' users.'."\n"
            .$this->getDebugInfo($user, $access_token_id)
        );

        $data = new DateTime('UTC');
        if (!$user->ifUserHasConfigValue('auto_follow_users_by_search', $access_token_id)) {
            $user->twitter_follower
                ->where('need_follow', true)
                ->where('start_follow_time IS NOT NULL')
                ->where('end_follow_time IS NOT NULL')
                ->where('access_token_id', $access_token_id)
                ->delete();
            $user->number_of_added_users_twitter
                ->where('date >= \''.$data->format('Y-m-d').'\'')
                ->delete();
        }
        /* @var Twitter_follower[] $followers */
        $time = $data->getTimestamp();
        $query = $user
            ->twitter_follower
            ->where([
                'need_follow' => true,
                'access_token_id' => $access_token_id
            ])
            ->where("((start_follow_time <= '{$time}' AND end_follow_time >= '{$time}') OR (start_follow_time = end_follow_time AND end_follow_time < '{$time}'))");
        $followers = $query->get();
        $new_followers_count = 0;
        foreach($followers as $follower) {
            sleep(1);
            $answer = $twitter->follow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $follower->setNeedFollow(false);
                $follower->setUnfollowTime($data->getTimestamp());
                $follower->setStillFollow(false);
                $follower->save();

                $new_followers_count++;
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_FOLLOWING_BY_SEARCH_ANALYTICS_TYPE,
            $new_followers_count
        );
        log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: finish follow new followers by search. Added '
            .$new_followers_count.' users.'."\n"
            .$this->getDebugInfo($user, $access_token_id)
        );
    }

    /**
     * Unfollow those who unsubscribed from your account in Twitter.
     *
     * @access public
     * @param $args
     */
    public function unfollowUnsubscribedUsers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_unfollow', $access_token_id)) {
            return;
        }
        $days_before_unfollow = $user->ifUserHasConfigValue('days_before_unfollow', $access_token_id);
        if(!$days_before_unfollow) {
            $days_before_unfollow = 3;
        }
        $date = new DateTime('- '.$days_before_unfollow.' days UTC');

        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $new_unfollowing_count = 0;
//        $followersIds = $twitter->get_friends();
//        foreach($followersIds->ids as $followerId) {
//            If(!$user->isUserHasTwitterFollower($followerId, $access_token_id)) {
//                $answer = $twitter->unfollow($followerId);
//                if ($answer->errors) {
//                    foreach($answer->errors as $err) {
//                        log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
//                    }
//                } else {
//                    $new_unfollowing_count++;
//                }
//            }
//        }

        /* @var Twitter_follower[] $followers */
        $followers = $user
            ->twitter_follower
            ->where('still_follow', false)
            ->where('access_token_id', $access_token_id)
            ->where('unfollow_time < '.$date->getTimestamp())
            ->get();
        foreach($followers as $follower) {
            $answer = $twitter->unfollow($follower->follower_id);
            if ($answer->errors) {
                foreach($answer->errors as $err) {
                    log_message('TASK_ERROR', __FUNCTION__ . 'Twitter error: code: '.$err->code.'. Message: ' . $err->message);
                }
            } else {
                $follower->delete();
                $new_unfollowing_count++;
            }
        }
        Social_analytics::updateAnalytics(
            $access_token_id,
            Social_analytics::NEW_UNFOLLOWING_ANALYTICS_TYPE,
            $new_unfollowing_count
        );
    }

    /**
     * Search users and follow them
     *
     * @param $args
     */
    public function searchUsers($args) {
        $user_id = (int)$args['user_id'];
        $user = new User($user_id);
        $access_token_id = $args['id'];
        if (!$user->ifUserHasConfigValue('auto_follow_users_by_search', $access_token_id)) {
            return;
        }

        log_message('TASK_DEBUG', __FUNCTION__ . 'Twitter: start search users to follow.'.$this->getDebugInfo($user, $access_token_id));

        $date = new DateTime('UTC 00:00:00');
        $user_timezone = new DateTimeZone(User_timezone::get_user_timezone($user_id));
        $timezone_offset = $user_timezone->getOffset($date) / 3600;

        $twitter = $this->inicializeTwitterSocializer($user_id, $args);

        $user_search_keywords = $user->getUserSearchKeywords($args['profile_id']);
        $number_of_added_users = $user->getDateToAddUserTwitter($access_token_id);
        $max_daily_auto_follow_users_by_search = (int)$user->ifUserHasConfigValue('max_daily_auto_follow_users_by_search', $access_token_id);
        $old_date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
        if (!$number_of_added_users->id) {
            $number_of_added_users->date = $date->format('Y-m-d');
            $number_of_added_users->setUserId($user_id);
            $number_of_added_users->token_id = $access_token_id;
            $number_of_added_users->count = 0;
        } elseif ($old_date < $date) {
            $number_of_added_users = new Number_of_added_users_twitter();
            $number_of_added_users->date = $date->format('Y-m-d');
            $number_of_added_users->setUserId($user_id);
            $number_of_added_users->count = 0;
            $number_of_added_users->token_id = $access_token_id;
            $number_of_added_users->save();
        } elseif($old_date > $date) {\
            log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                . 'Twitter followers already added.'."\n"
                .$this->getDebugInfo($user, $access_token_id)
            );
            return;
        }
        unset($old_date);

        $age_of_account = $user->ifUserHasConfigValue('age_of_account', $access_token_id);
        if(!$age_of_account) {
            $age_of_account = 0;
        } else {
            $age_of_account_splited = preg_split('/,/', $age_of_account);
            if(count($age_of_account_splited)) {
                if(count($age_of_account_splited) > 1) {
                    $age_of_account =  $age_of_account_splited;
                }
            }
        }

        $tweets_count = $user->ifUserHasConfigValue('number_of_tweets', $access_token_id);
        if(!$tweets_count) {
            $tweets_count = 0;
        } else {
            $tweets_count_splited = preg_split('/,/', $tweets_count);
            if(count($tweets_count_splited)) {
                if(count($tweets_count_splited) > 1) {
                    $tweets_count =  $tweets_count_splited;
                }
            }
        }

        foreach($user_search_keywords as $user_search_keyword) {
            $other_field = $user_search_keyword->get_other_fields();
            $query = $twitter->create_query(
                $user_search_keyword->keyword,
                $other_field['include'],
                $other_field['exclude'],
                $user_search_keyword->exact
            );
            $queryArgs = [
                'min_followers' => $user_search_keyword->min_followers,
                'max_followers' => $user_search_keyword->max_followers,
                'max_id' => $user_search_keyword->max_id,
                'age_of_account' => $age_of_account,
                'tweets_count' => $tweets_count
            ];
            $users = $twitter->search_users($query, $queryArgs);
            foreach($users['users'] as $twitter_user_id) {
                if (!$user->isUserHasTwitterFollower($twitter_user_id, $access_token_id)) {
                    if (($max_daily_auto_follow_users_by_search
                        && $max_daily_auto_follow_users_by_search > $number_of_added_users->count) ||
                    !$max_daily_auto_follow_users_by_search) {
                        $date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
                        $number_of_added_users->count += 1;
                        $number_of_added_users->save();
                    } else {
                        $date = DateTime::createFromFormat('!Y-m-d', $number_of_added_users->date);
                        $date->modify('+1 days');
                        $number_of_added_users = new Number_of_added_users_twitter();
                        $number_of_added_users->date = $date->format('Y-m-d');
                        $number_of_added_users->setUserId($user_id);
                        $number_of_added_users->token_id = $access_token_id;
                        $number_of_added_users->count = 1;
                        $number_of_added_users->save();
                    }
                    $twitter_follower = new Twitter_follower();
                    $twitter_follower->setFollowerId($twitter_user_id);
                    $twitter_follower->setUserId($user_id);
                    $twitter_follower->setAccessTokenId($access_token_id);

                    $start_date = clone $date;
                    $start_date->modify(($timezone_offset*-1).' hours');

                    $end_date = clone $date;
                    $end_date->modify(($timezone_offset*-1).' hours');

                    if ($end_date <= $start_date) {
                        $end_date->modify('1 days');
                    }

                    $twitter_follower->setStartFollowTime(
                        $user_search_keyword
                            ->getStartDateTime($start_date)
                            ->getTimestamp()
                    );
                    $twitter_follower->setEndFollowTime(
                        $user_search_keyword
                            ->getEndDateTime($end_date)
                            ->getTimestamp()
                    );
                    unset($start_date);
                    unset($end_date);

                    $twitter_follower->setNeedFollow(true);
                    $twitter_follower->save();
                }
            }
            log_message('TASK_SUCCESS', __FUNCTION__ . 'Twitter: '
                .'By keywords '.$query.' add '.count($users['users']).' users.'."\n"
                .$this->getDebugInfo($user, $access_token_id)
            );
            if($user_search_keyword->max_id != $users['max_id']) {
                $user_search_keyword->max_id = $users['max_id'];
            } else {
                $user_search_keyword->max_id = null;
            }
            $user_search_keyword->save();
        }
    }

    private function getDebugInfo($user, $access_token_id) {
        $info = [
            'User ID' => $user->id,
            'Username' => $user->username,
            'Token ID' => $access_token_id
        ];
        $text = '(';
        foreach($info as $key => $value) {
            $text.=$key.':'.$value.';';
        }
        $text.=')';
        return $text;
    }

    /**
     * @param $user_id
     * @param $token
     * @return Socializer_Twitter
     * @internal param $params
     */
    private function inicializeTwitterSocializer($user_id, $token) {
        $this->load->library('Socializer/socializer');
        /* @var Socializer_Twitter $twitter */
        $twitter = Socializer::factory('Twitter', $user_id, $token);
        return $twitter;
    }

}