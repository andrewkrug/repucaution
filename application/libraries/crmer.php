<?php

class Crmer
{
    /**
     * @var int
     */
    private $limit = 30;

    /**
     * @var int
     */
    private $maxPerRequest = 150;

    /**
     * @var bool
     */
    private $test = false;

    /**
     * @var bool
     */
    private $disableRetweets = true;

    /**
     * @var CodeIgniter
     */
    protected $ci;

    /**
     * @var Socializer
     */
    protected $socializer;

    /**
     * @var int
     */
    protected $userId;

    protected $profileId;

    public function __construct($userId = null, $profileId = null)
    {
        $this->ci = &get_instance();
        $container = $this->ci->container;
        $this->socializer = $this->ci->load->library('Socializer/socializer');
        $this->jobQueue = $container->get('core.job.queue.manager');
        $this->userId = $userId;
        $this->profileId = $profileId;
        log_message('TASK_SUCCESS', 'Crmer test mode:' . ($this->test ? 'on' : 'off'));
    }

    /**
     * Get Crmer instance
     *
     * @param $userId
     * @param $profileId
     *
     * @return Crmer
     */
    public static function factory($userId, $profileId)
    {
        return new self($userId, $profileId);
    }

    /**
     * Get url query params
     *
     * @param $url
     * @return array|mixed
     */
    public function urlQueryParams($url)
    {
        $out = parse_url($url);
        if (!isset($out['query'])) {
            return array();
        }
        parse_str($out['query'], $out);

        return $out;
    }

    

    // ------------------
    // TWITTER
    // ------------------

    /**
     * Get crm tweets
     *
     * @param $directoryArray
     * @return array
     */
    public function getCrmTweets($directoryArray)
    {
        log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'called in crmer');
        if (empty($directoryArray['twitter_link'])) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'no link for directory id='.$directoryArray['id']);

            return;
        }
        $access_token = Access_token::getOneByTypeAndUserIdAndProfileIdAsArray('twitter', $this->userId, $this->profileId);
        $twitter = $this->socializer->factory('Twitter', $this->userId, $access_token);

        $params = array(
            'count' => $this->limit,
            'since' => strtotime('yesterday'),
            'until' => strtotime('tomorrow'),
        );

        $username = $twitter->getUserFromLink($directoryArray['twitter_link']);
        // if it is not the first task, but continuation of another
        // add offset param to request
        if (isset($directoryArray['continue_from'])) {
            $key = key($directoryArray['continue_from']);
            $value = current($directoryArray['continue_from']);
            $params[$key] = $value;
        }

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );
        $result = array();

        while (true) {
            sleep(3);
            try {
                $data = $twitter->search_tweets('from:'.$username, $params);
            } catch (Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                $data = null;
            }

            if (!is_object($data)) {
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; break 1');
                break;
            }

            list($parsed, $parsed_count) = $this->parseTweets($data, $dates);
            $result = array_merge($result, $parsed);

            // check if incoming page of results is full, so the next page may exist
            if ((count($data->statuses) < $this->limit) || (! isset($data->search_metadata->next_results))) {
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; break 2');
                break;
            }

            // check if per task limit reached
            if (count($result) >= $this->maxPerRequest - $this->limit) {
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; limit reached');
                // if reached create another task
                $lastResult = end($result);
                $directoryArray['continue_from'] = array(
                    'max_id' => $lastResult['original_id']
                );
                if (!$this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $directoryArray, array(
                        'thread' => CLI_controller::CRM_THREAD
                    ));
                }
                break;
            }
            // retrieve params from next page url as array
            $params = $this->urlQueryParams($data->search_metadata->next_results);
        }
        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    /**
     * Parse crm tweets
     *
     * @param $data
     * @param $dates
     * @return array
     */
    public function parseTweets($data, $dates)
    {
        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

        $statusesCount = 0;
        $statuses = array();

        foreach ($data->statuses as $status) {
            $statusesCount += 1;
            if ($this->disableRetweets && isset($status->retweeted_status)) {
                continue;
            }
            $created_at = date('U', strtotime($status->created_at));
            if ($created_at > $until || $created_at < $since) {
                continue;
            }

            $statuses[] = array(
                'original_id' => $status->id_str,
                'created_at' => $created_at,
                'message' => $status->text,
                'creator_id' => $status->user->id_str,
                'creator_name' => $status->user->screen_name,
                'creator_image_url' => isset($status->user->profile_image_url)
                    ? $status->user->profile_image_url
                    : '',
                'source' => isset($status->source) ? $status->source : '',
                'other_fields' => array(
                    'creator_followers_count' => $status->user->followers_count,
                    'retweet_count' => $status->retweet_count,
                    'favorited' => $status->favorited,
                    'retweeted' => $status->retweeted,
                    'is_original' => ! isset($status->retweeted_status),
                    'retweets_count' => $status->retweet_count
                ),
            );
        }

        return array($statuses, $statusesCount);
    }

    // ------------------
    // FACEBOOK
    // ------------------

    /**
     * Get crm posts
     *
     * @param $directoryArray
     * @return array
     */
    public function getCrmPosts($directoryArray)
    {
        log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . ' called in mentioner');
        if (empty($directoryArray['facebook_link'])) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'no link for directory id='.$directoryArray['id']);
            return;
        }
        $access_token = Access_token::getOneByTypeAndUserIdAndProfileIdAsArray('facebook', $this->userId, $this->profileId);
        /* @var Socializer_Facebook $facebook */
        $facebook = $this->socializer->factory('Facebook', $this->userId, $access_token);

        $params = array(
            'limit' => $this->limit,
            'since' => strtotime('yesterday'),
            'until' => strtotime('tomorrow'),
        );
        $username = $facebook->getUserFromLink($directoryArray['facebook_link']);

        if (isset($directoryArray['continue_from'])) {
            $key = key($directoryArray['continue_from']);
            $value = current($directoryArray['continue_from']);
            $params[$key] = $value;
        }

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );
        $result = array();
        
        while (true) {
            sleep(3);
            try {
                $data = $facebook->getUserPosts($username);
            } catch (Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                $data = null;
            }

            if (!is_array($data)) {
                break;
            }

            list($parsed, $parsed_count) = $this->parsePosts($data, $dates);
            foreach ($parsed as $key => $item) {
                if (!empty($item['creator_id'])) {
                    sleep(3);
                    $parsed[$key]['other_fields']['friends_count'] = $facebook->getUserFriendsCount(
                        $item['creator_id']
                    );
                }

            }
            $result = array_merge($result, $parsed);

            // check if per task limit reached
            if (count($result) >= $this->maxPerRequest - $this->limit) {
                // if reached create another task
                $lastResult = end($result);
                $directoryArray['continue_from'] = array(
                    'until' => $lastResult['created_at']
                );

                if (!$this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $directoryArray, array(
                        'thread' => CLI_controller::CRM_THREAD
                    ));
                }
                break;
            }

            if ((count($data['data']) < $this->limit) || ( ! isset($data['paging']['next']))) {
                break;
            }
            // retrieve params from next page url as array
            $params = $this->urlQueryParams($data['paging']['next']);
        }
        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    /**
     * Parse crm posts
     *
     * @param $data
     * @param $dates
     * @return array
     */
    public function parsePosts($data, $dates)
    {
        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

        $posts_count = 0;
        $posts = array();

        foreach ($data['data'] as $post) {
            $posts_count += 1;
            $created_at = date('U', strtotime($post['created_time']));
            if ($created_at > $until || $created_at < $since) {
                continue;
            }

            $posts[] = array(
                'original_id' => $post['id'],
                'created_at' => $created_at,
                'message' => Arr::get($post, 'message', ''),
                'creator_id' => $post['from']['id'],
                'creator_name' => $post['from']['name'],
                'creator_image_url' => Arr::get($post, 'image', ''),
                'other_fields' => array(
                    'comments' => isset($post['comments']['data']) ? count($post['comments']['data']) : 0,
                    'likes' => isset($post['likes']['data']) ? count($post['likes']['data']) : 0,
                    'i_like' => 0,
                    'type' => Arr::get($post, 'type'),
                    'story' => Arr::get($post, 'story'),
                    'link' => Arr::get($post, 'link'),
                    'picture' => Arr::get($post, 'picture'),
                ),
            );
        }

        return array($posts, $posts_count);
    }


    // ------------------
    // INSTAGRAM
    // ------------------

    /**
     * Get crm activities
     *
     * @param $directoryArray
     * @return array
     */
    public function getCrmActivities($directoryArray)
    {
        log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . ' called in mentioner');
        if (empty($directoryArray['instagram_link'])) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'no link for directory id='.$directoryArray['id']);
            return;
        }
        $access_token = Access_token::getOneByTypeAndUserIdAndProfileIdAsArray('instagram', $this->userId, $this->profileId);
        /* @var Socializer_Instagram $instagram */
        $instagram = $this->socializer->factory('Instagram', $this->userId, $access_token);

        $params = array(
            'limit' => $this->limit,
            'since' => strtotime('yesterday'),
            'until' => strtotime('tomorrow')
        );

        $params['continue_from'] = (isset($directoryArray['continue_from'])) ? $directoryArray['continue_from'] : null;
        $userId = $instagram->getUserFromLink($directoryArray['instagram_link']);
        if (!$userId) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'invalid username in directory id='.$directoryArray['id']);
            return;
        }

        $result = array();
        while (true) {
            sleep(1);
            try {
                $data = $instagram->activities($userId, $params);
            } catch (Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                $data = null;
            }

            if (!is_array($data)) {
                break;
            }
            list($parsed, $parsed_count) = $this->parseActivities($data);
            $result = array_merge($result, $parsed);

            // check if per task limit reached
            if (count($result) >= $this->maxPerRequest - $this->limit) {
                // if reached create another task
                $lastResult = end($result);
                $directoryArray['continue_from'] = $lastResult->id;

                if (!$this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $directoryArray, array(
                        'thread' => CLI_controller::CRM_THREAD
                    ));
                }
                break;
            }

            if ((count($data) < $this->limit)) {
                break;
            }
        }
        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    /**
     * Parse crm activities
     *
     * @param $data
     * @return array
     */
    public function parseActivities($data)
    {
        $activitiesCount = 0;
        $activities = array();

        foreach ($data as $image) {
            $activitiesCount += 1;
            $activities[] = array('social' => 'instagram',
                                'created_at' => $image->created_time,
                                'original_id' => $image->id,
                                'message' => $image->caption->text,
                                'creator_image_url' => $image->user->profile_picture,
                                'other_fields' =>array(
                                    'i_like' => $image->user_has_liked,
                                    'low_resolution' => $image->images->low_resolution->url,
                                    'thumbnail' => $image->images->thumbnail->url,
                                    'standard_resolution' => $image->images->standard_resolution->url
                                    )
            );
        }

        return array($activities, $activitiesCount);
    }
}
