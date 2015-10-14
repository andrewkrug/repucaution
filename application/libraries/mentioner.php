<?php

class Mentioner{

    // number of items on requested page
    private $limit = 30;

    // number of max items grabbed during one task
    private $max_per_request = 90;
    
    private $since = 'today';
    private $until = 'tomorrow';

    // set to false if testing
    // not to use task manager queue
    private $test = false;

    private $disable_retweets = true;


	protected $ci;
	protected $socializer;
   // protected $jobQueue;
	protected $user_id;
	//if response start contains created time< yesterday
	private $google_stop;

    /**
     * @param null $user_id
     */
	public function __construct($user_id = NULL) {
		$this->ci = &get_instance();
        $container = $this->ci->container;
		$this->socializer = $this->ci->load->library('Socializer/socializer');
        $this->jobQueue = $container->get('core.job.queue.manager');
		$this->user_id = $user_id;

        log_message('TASK_SUCCESS', 'Mentioner test mode:' . ($this->test ? 'on' : 'off'));
	}

    /**
     * @param $user_id
     * @return Mentioner
     */
	public static function factory($user_id) {
		return new self($user_id);
	}

    /**
     * @param $url
     * @return array|mixed
     */
    function url_query_params($url) {
        $out = parse_url($url);
        if ( ! isset($out['query'])) {
            return array();
        }
        parse_str($out['query'], $out);
        return $out;
    }

    /**
     * @param $keyword
     * @return array
     */
    public function get_verification_config($keyword) {
        $include = array();
        $exclude = array();
        //'other_fields' is serialized storage of include/exclude params
        if( ! empty($keyword['other_fields']) ) {
            $other_fields = unserialize($keyword['other_fields']);
            foreach ( $other_fields['include'] as $_include ) {
                if(!empty ($_include)) {
                    $include[] = $_include;
                }
            }
            foreach ( $other_fields['exclude'] as $_exclude ) {
                if(!empty ($_exclude)) {
                    $exclude[] = $_exclude;
                }
            }
        }
        //create config for 'VerificationMention' library
        $config = array (
            'keywords' => $keyword['keyword'],
            'exact' => intval($keyword['exact']),
            'include' => $include,
            'exclude' => $exclude
        );
        return $config;
    }

    // ------------------
    // TWITTER
    // ------------------

    /**
     * @param $keyword
     * @param $mention_keyword_array
     * @param $access_token
     *
     * @return array
     */
	public function tweets($keyword, $mention_keyword_array, $access_token) {

        log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'called in mentioner');

        /** @var Socializer_Twitter $twitter */
        $twitter = $this->socializer->factory('Twitter', $this->user_id, $access_token);

        $params = array(
            'count' => $this->limit,
            'since' => strtotime($this->since),
            'until' => strtotime($this->until),
        );

        // if it is not the first task, but continuation of another
        // add offset param to request
        if (isset($keyword['continue_from'])) {

            $key = key($keyword['continue_from']);
            $value = current($keyword['continue_from']);

            $params[$key] = $value;
        }

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );

        $result = array();

        $config = $this->get_verification_config($keyword);
		
        while(true) {

            sleep(3);

            try {
                $data = $twitter->search_tweets($keyword['keyword'], $params);
            } catch(Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());                
                $data = null;
            }

            if ( ! is_object($data)) {
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; break 1');
				break;
            }

            list($parsed, $parsed_count) = $this->parse_tweets($data, $dates, $config);            
            $result = array_merge($result, $parsed);

            // check if incoming page of results is full, so the next page may exist
            if ((count($data->statuses) < $this->limit) || (! isset($data->search_metadata->next_results))) {
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; break 2');
				break;
            }

            // check if per task limit reached
            if (count($result) >= $this->max_per_request - $this->limit) {

                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; limit reached');

                // if reached create another task
                $last_result = end($result);
                $mention_keyword_array['continue_from'] = array(
                    'max_id' => $last_result['original_id']
                );

                if ( ! $this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $mention_keyword_array, array(
                        'thread' => CLI_controller::MENTIONS_THREAD
                    ));
                }
				
                break;
            }

            // retrieve params from next page url as array
            $params = $this->url_query_params($data->search_metadata->next_results);
        }

        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    /**
     * @param $data
     * @param $dates
     * @param $config
     * @return array
     */
    public function parse_tweets($data, $dates, $config) {

        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

        $verificationMention = $this->ci->load->library('VerificationMention', $config);

        $statuses_count = 0;
        $statuses = array();

        foreach ($data->statuses as $status) {

            $statuses_count += 1;

            if ($this->disable_retweets && isset($status->retweeted_status)) {
                continue;
            }

            $created_at = date('U', strtotime($status->created_at));
            if ($created_at > $until || $created_at < $since) {
                continue;
            }

            if ( ! $verificationMention->verificate($status->text)) {
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
                    'retweets_count' => $status->retweet_count,
                    'following' => $status->user->following,
                ),
            );
        }

        return array($statuses, $statuses_count);
    }

    // ------------------
    // FACEBOOK
    // ------------------

    public function posts($keyword, $mention_keyword_array, $access_token) {

        log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . ' called in mentioner');

        /* @var Socializer_Facebook $facebook */
        $facebook = $this->socializer->factory('Facebook', $this->user_id, $access_token);

        $params = array(
            'limit' => $this->limit,
            'since' => strtotime($this->since),
            'until' => strtotime($this->until),
        );

        if (isset($keyword['continue_from'])) {
            $key = key($keyword['continue_from']);
            $value = current($keyword['continue_from']);

            $params[$key] = $value;
        }

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );

        $config = $this->get_verification_config($keyword);

        $result = array();
        
        while(true) {

            sleep(3);

            try {
                $data = $facebook->search_posts($keyword['keyword'], $params);
            } catch(Exception $e) {
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                $data = null;
            }


            if ( ! is_array($data)) {
                break;
            }

            list($parsed, $parsed_count) = $this->parse_posts($data, $dates, $config);

            foreach ($parsed as $key => $item) {
                if (!empty($item['creator_id'])) {
                    sleep(3);
                    $parsed[$key]['other_fields']['friends_count'] = $facebook->getUserFriendsCount($item['creator_id']);
                }
            }

            $result = array_merge($result, $parsed);

            // check if per task limit reached
            if (count($result) >= $this->max_per_request - $this->limit) {

                // if reached create another task
                $last_result = end($result);

                $mention_keyword_array['continue_from'] = array(
                    'until' => $last_result['created_at']
                );

                if ( ! $this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $mention_keyword_array, array(
                        'thread' => CLI_controller::MENTIONS_THREAD
                    ));
                }

                break;
            }

            if ((count($data['data']) < $this->limit) || ( ! isset($data['paging']['next']))) {
                break;
            }

            // retrieve params from next page url as array
            $params = $this->url_query_params($data['paging']['next']);
        }

        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    public function parse_posts($data, $dates, $config) {

        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

        $verificationMention = $this->ci->load->library('VerificationMention', $config);

        $posts_count = 0;
        $posts = array();

        foreach ($data['data'] as $post) {

            $posts_count += 1;

            $created_at = date('U', strtotime($post['created_time']));
            if ($created_at > $until || $created_at < $since) {
                continue;
            }

            $text = '';
            if ($message = Arr::get($post, 'message', '')) {
                $text = $message;
            } else if($story = Arr::get($post, 'story', '')) {
                $text = $story;
            }

            if ( ! $verificationMention->verificate($text)) {
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
    // GOOGLE
    // ------------------

    /**
     * @param $keyword
     * @param $mention_keyword_array
     * @return array
     */
	public function activities($keyword, $mention_keyword_array, $access_token) {

        log_message('TASK_SUCCESS', __FUNCTION__ . '> called in mentioner');

		sleep(1);
		$this->google_stop = false;
        $google = $this->socializer->factory('Google', $this->user_id, $access_token);
		if($this->limit)
        $params = array(
            'count' => $this->limit,
            'since' => strtotime($this->since),
            'until' => strtotime($this->until),
        );

        // if it is not the first task, but continuation of another
        // add offset param to request
        

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );

        $result = array();

        $config = $this->get_verification_config($keyword);
		$search_params = array('orderBy' => 'recent', 'maxResults' => 20);
		if (isset($keyword['continue_from'])) {

            $search_params['pageToken'] = $keyword['continue_from']; 
        }
		
		$i = 0;

        while(true) {

            sleep(3);

            try {
                $data = $google->search_activities($keyword['keyword'], $search_params);
            } catch(Exception $e) {

                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());                
                $data = null;
            }

			if (! ($data || is_array($data) && is_array($data['items']))) {
				break;
			}

            list($parsed, $parsed_count) = $this->parse_activities($data, $dates, $config);


            if (!empty($parsed)) {
                foreach ($parsed as &$item) {
                    $count = 0;
                    if (empty($item['creator_id'])) {
                        continue;
                    }
                    try {
                        $count = (int)$google->getPeopleCount($item['creator_id']);
                    } catch(Exception $e) {

                    }
                    $item['other_fields']['people_count'] = $count;

                }

            }

            if(!empty($result) && !empty($parsed)){
				
				$endr = end($result);
				$endp = end($parsed);
				if($endr['original_id'] == $endp['original_id']){
					break;
			   }
			}
           
		   
            $result = array_merge($result, $parsed);

            // check if incoming page of results is full, so the next page may exist
            if ($this->google_stop) {
                break;
            }
			
			/* if ( count($data['items'])<$search_params['maxResults']) {
                break;
            } */
            // check if per task limit reached
             if (count($result) >= $this->max_per_request - $search_params['maxResults']) {

                // if reached create another task
                $last_result = end($result);
                $mention_keyword_array['continue_from'] = $data['nextPageToken'];

                if ( ! $this->test) {
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'loop; extra queue');
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $mention_keyword_array, array(
                        'thread' => CLI_controller::MENTIONS_THREAD
                    ));
                }
                break;
            } 

            // retrieve params from next page url as array
			
             if(isset($data['nextPageToken'])){
				$search_params['pageToken'] = $data['nextPageToken'];
			}else{
				break;
			}
			
        }

        log_message('TASK_SUCCESS', __FUNCTION__ . ' fetched - ' . count($result));

        return $result;
    }

    /**
     * 
     */
    public function parse_activities($data, $dates, $config) {
		
        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

        $verificationMention = $this->ci->load->library('VerificationMention', $config);

        $activities_count = 0;
        $activities = array();

        foreach ($data['items'] as $item) {

            $created_at = date('U', strtotime($item['published']));
            if ($created_at > $until || $created_at < $since) {
               if($created_at < $since){
					$this->google_stop = true;
					return array($activities, $activities_count);
			   }
				continue;
            }

            if ( ! $verificationMention->verificate($item['title']) && !$verificationMention->verificate($item['object']['content'])) {
                //echo "verific";
				continue;
            }

            $activities[] = array(
                'original_id' => $item['id'],
                'created_at' => $created_at,
                'message' => $item['object']['content'],
                'creator_id' => $item['actor']['id'],
                'creator_name' => $item['actor']['displayName'],
                'creator_image_url' => isset($item['actor']['image']['url']) 
                    ? $item['actor']['image']['url']
                    : '',
                
                'other_fields' => array(
					'comments' => $item['object']['replies']['totalItems'],
					'comments_uri' => $item['object']['replies']['selfLink'],
					'plusoners' => $item['object']['plusoners']['totalItems'],
					'plusoners_uri' => $item['object']['plusoners']['selfLink'],
                    'resharers' => $item['object']['resharers']['totalItems'],
                    'resharers_url' => $item['object']['resharers']['selfLink'],
					'url'=>$item['url'],
                    'picture' => isset($item['object']['attachments'][0]['image']['url']) 
                    ? $item['object']['attachments'][0]['image']['url']
                    : '',
					'type' => isset($item['object']['attachments'][0]['objectType']) 
                    ? $item['object']['attachments'][0]['objectType']
                    : '',
                    //'story' => Arr::get($post, 'story'),
                    'link' =>  isset($item['object']['attachments'][0]['url']) 
                    ? $item['object']['attachments'][0]['url']
                    : ''
                ),
            );
			$activities_count += 1;
        }

        return array($activities, $activities_count);
    }

    // ------------------
    // INSTAGRAM
    // ------------------

    /**
     * Get instagram's tags
     *
     * @param array $keyword
     * @param array $mentionKeywordArray
     * @param array $access_token
     *
     * @return array
     */
    public function tags($keyword, $mentionKeywordArray, $access_token) {

        /* @var Socializer_Instagram $instagram */
        $instagram = $this->socializer->factory('Instagram', $this->user_id, $access_token);
        $maxId = (empty($keyword['continue_from'])) ? null : $keyword['continue_from'];

        $params = array(
            'count' => $this->limit,
            'since' => strtotime($this->since),
            'until' => strtotime($this->until),
        );

        $dates = array(
            'since' => $params['since'],
            'until' => $params['until'],
        );

        $result = array();

        $config = $this->get_verification_config($keyword);

        while(true) {

            $data = $instagram->tagsRecent($keyword['keyword'], $this->limit, $maxId);

            if ( ! is_object($data)) {
                break;
            }

            list($parsed, $parsedCount) = $this->parseTags($data, $dates, $config);

            $result = array_merge($result, $parsed);


            // check if incoming page of results is full, so the next page may exist
            if ((count($data->data) < $this->limit) || (! isset($data->pagination->next_max_id))) {
                break;
            }

            $maxId = $data->pagination->next_max_id;

            // check if per task limit reached
            if ($parsedCount >= $this->max_per_request) {

                // if reached create another task
                $keyword['continue_from'] = $maxId;
                $this->test = false;
                if (!$this->test) {
                    $this->jobQueue->addJob('tasks/mentions_task/grabber', $keyword, array(
                        'thread' => CLI_controller::MENTIONS_THREAD
                    ));
                }
                break;
            } else {
                $this->max_per_request -= $parsedCount;
            }

        }

        return $result;
    }

    /**
     * Parse grabbed tags data
     *
     * @param $data
     * @param array $dates
     * @param array $config
     * @return array
     */
    public function parseTags($data, $dates, $config) {

        $until = date('U', $dates['until']);
        $since = date('U', $dates['since']);

//        $verificationMention = $this->ci->load->library('VerificationMention', $config);

        $statusesCount = 0;
        $statuses = array();

        foreach ($data->data as $status) {

            $statusesCount += 1;

            $created_at = $status->created_time;
            if ($created_at > $until || $created_at < $since) {
                continue;
            }

            /*if ( ! $verificationMention->verificate($status->text)) {
                            continue;
            }*/

            $statuses[] = array(
                'original_id' => $status->id,
                'created_at' => $created_at,
                'message' => $status->caption->text,
                'creator_id' => $status->user->id,
                'creator_name' => $status->user->username,
                'creator_image_url' => $status->user->profile_picture,
                'other_fields' => array(
                    'i_like' => $status->user_has_liked,
                    'instagram_comments' => $status->comments->count,
                    'instagram_likes' => $status->likes->count,
                    'tags' => $status->tags,
                    'low_resolution' => $status->images->low_resolution->url,
                    'thumbnail' => $status->images->thumbnail->url,
                    'standard_resolution' => $status->images->standard_resolution->url
                )
            );
        }

        return array($statuses, $statusesCount);
    }
}