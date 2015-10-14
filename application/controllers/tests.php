<?php defined('BASEPATH') OR exit('No direct script access allowed');



class tests extends MY_Controller {

    protected $website_part = 'dashboard';

    public function index(){
        $directory_parcer = Directory_Parser::factory('Insider_Pages')->set_url( 'http://www.insiderpages.com/b/3719002852/cafe-wha-new-york' );
        $reviews = $directory_parcer->get_reviews();
        var_Dump($reviews); exit;
    }

    public function kw() {
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

        foreach ($keywords as $keyword) {
            var_dump($keyword->id);
        }
    }


    public function grank($keyword_id) {
        $keyword = new Keyword($keyword_id);

        $user_additional = User_additional::inst()->get_by_user_id($keyword->user_id);

        $this->load->config('site_config', TRUE);
        // $google_app_config = $this->config->item('google_app', 'site_config');
        $google_app_config = Api_key::build_config(
            'google',
            $this->config->item('google_app', 'site_config')
        );


        $gls = $this->load->library('gls');
        $gls->set(array(
            // 'key' => $google_app_config['simple_api_key'],
            'key' => $google_app_config['developer_key'],
        ));

        $gls->request($keyword->keyword);

        if ($gls->success()) {

            $rank = $gls->location_rank($user_additional->address_id);

            if (is_null($rank)) {
                throw new Exception('No results for rank');
            }

            echo 'Rank: ' . $rank;

        } else {

            throw new Exception('Google Rank Grabber Error: ' . $gls->error());
        }
        echo '<hr>';
        echo '<pre>';
        var_dump($gls->dirty());
    }

    public function twitter_mentions() {

        $this->load->library('mentioner');

        $keyword = array(
            'keyword' => 'machinarium',
            'exact' => 0,
            'other_fields' => serialize(array(
                'include' => array(''), 'exclude' => array('')
            )),
        );

        //$data = Mentioner::factory($this->c_user->id)->tweets($keyword, array('bathman'));
        echo '<pre>';
        //var_dump($data);

    }

    public function facebook_mentions() {

        $this->load->library('mentioner');

        $keyword = array(
            'keyword' => 'qwe',
            'exact' => 0,
            'other_fields' => serialize(
                array('include' => array(), 'exclude' => array()
                )
            ),
        );

        $mentioner = Mentioner::factory($this->c_user->id);

        $data = $mentioner->posts($keyword, array());
        echo '<pre>';
        var_dump(1,$data);
    }

    public function lnkd(){

        // $this->load->library('Socializer/socializer');
        // $linkedin = Socializer::factory('Linkedin', $this->c_user->id);

        //$updates = $linkedin->getUpdates();
        //$linkedin->get_conns_count();
        // $this->statistic($this->c_user->id);

        $this->load->library('activitioner');
        try{
            $user = new User();
            $users = $user->get()->all;
            $socials = array('linkedin');

            foreach($users as $u){
                $act = Activitioner::factory($u->id);
                foreach($socials as $social){
                    if($social == 'linkedin'){
                        $act->getLinkedinUpdates();
                    }
                    log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Activities for '.$social.' mkwid: grabbed');
                }

            }

        } catch (Exception $e) {

            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

        }
    }

    public function statistic( $user_id ){
        $this->load->library('Socializer/socializer');
        $twitter = Socializer::factory('Twitter', (int)$user_id['user_id']);
        $facebook = Socializer::factory('Facebook', (int)$user_id['user_id']);
        $linkedin = Socializer::factory('Linkedin', (int)$user_id['user_id']);
        $followers_count = $twitter->get_followers_count();
        $likes_count = $facebook->get_page_likes_count();
        $conns_count = $linkedin->get_conns_count();

        $this->_save_values((int)$user_id['user_id'], $followers_count, 'twitter');
        $this->_save_values((int)$user_id['user_id'], $likes_count, 'facebook');
        $this->_save_values((int)$user_id['user_id'], $conns_count, 'linkedin');
    }

    private function _save_values( $user_id, $count, $type ) {
        $social_value = Social_value::inst();
        $where = array(
            'user_id' => $user_id,
            'date' => date('Y-m-d'),
            'type' => $type
        );
        if(!$social_value->where($where)->get()->id) {
            $social_value->user_id = (int)$user_id;
            $social_value->date = date('Y-m-d');
            $social_value->value = $count;
            $social_value->type = $type;
            $social_value->save();
        }
    }

    public function rels() {
        $mention = Mention::inst()->get();

        foreach ($mention as $key => $value) {
            var_dump($value->user->id);
        }

    }

    public function get_verification_config($keyword) {
        $include = array();
        $exclude = array();
        //'other_fields' is serialized storage of include/exclude params
        if( !empty($keyword['other_fields']) ) {
            $other_data = unserialize($keyword['other_fields']);
            foreach ( $other_data['include'] as $_include ) {
                if(!empty ($_include)) {
                    $include[] = $_include;
                }
            }
            foreach ( $other_data['exclude'] as $_exclude ) {
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


    private function _parse_grabbed($grabbed_object, $keyword) {
        $user_id = $keyword['user_id'];
        $config = $this->_get_verification_config($keyword);
        //f*** library don't want to connect with $this->ci->load->library() :(
        require_once(dirname(__FILE__).'/VerificationMention.php');
        $verifier = new VerificationMention($config);
        $result_array = array();
        if( !isset($grabbed_object->items) ) {
            // log_message('error', 'No items: [' . $keyword['keyword']  . ']');
            return false;
        }
        // get database name for keyword
        $result_array['database'] = $keyword['database'];

        for($i = 0; $i < count($grabbed_object->items); $i++) {

            //check mention with our checker
            if ( $verifier->verificate($grabbed_object->items[$i]->title) ) {
            }
        }
        return $result_array;
    }

    public function geta(){
        $this->load->library('activitioner');
        try{
            $user = new User();
            $users = $user->get()->all;
            $socials = array('linkedin');



            foreach($users as $u){
                $act = Activitioner::factory($u->id);
                foreach($socials as $social){
                    if($social == 'linkedin'){
                        $act->getLinkedinUpdates();
                        continue;
                    }


                }
                log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Activity for '.$social.' mkwid: grabbed');
            }

        } catch (Exception $e) {
            //echo 'error: '.$e->getMessage().PHP_EOL;
            echo($e->getMessage());
            //throw $e;
        }
    }

    public function doo(){
        echo"ok";
    }

    public function twfollowers()
    {
        $this->load->library('Socializer/socializer');

        $twitter = Socializer::factory('Twitter', 1);
        $followers_count = $twitter->get_followers_count();
        var_Dump($followers_count); exit;
    }
    public function fsqr(){

        /* $url = 'https://ru.foursquare.com/v/enzo-cafe/4f1d97f5e4b0bf762b749b87';
        $dp = Directory_Parser::factory('Foursquare');
        $dp->set_url($url);
        $dp->get_reviews(); */

        if(isset($_GET['id'])){
            $id=$_GET['id'];
        }else{
            $id=7;
        }

        $directory_user = Directory_User::get_user_dir($this->c_user->id, $id)->to_dir_array();

        $this->grabb($directory_user[$id]);


    }

    protected function grabb($directory_user) {

        log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Reviews grabber');
        try {
            $directory = new DM_Directory($directory_user['directory_id']);
            if(!$directory->exists()) {
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' doesn\'t exist');
            }
            if(!$directory->status){
                throw new Exception('Directory id:' . $directory_user['directory_id'] . ' is disabled');
            }



            $link = (!empty($directory_user['additional'])&& !($directory->type == 'Foursquare')) ? $directory_user['additional'] : $directory_user['link'];

            $directory_parcer = Directory_Parser::factory($directory->type)->set_url( $link );

            //For fousquare only


            $directory_parcer->set_directory_user($directory_user);


            $reviews = $directory_parcer->get_reviews();
        }
        catch(Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Reviews: ' . $e->getMessage());
            throw $e;
        }

        //$today_midnight = strtotime('-7 day midnight');
        $today_midnight = strtotime('-34 day midnight');


        if (is_array($reviews) && ! empty($reviews)) {
            foreach($reviews as $_review) {

                $review_model = new Review();
                $review_model->from_array($_review);
                $review_model->user_id = $directory_user['user_id'];
                $review_model->directory_id = $directory_user['directory_id'];
                $review_model->posted_date = date('Y-m-d', $_review['posted']);

                $review_model->save();
                if(!$review_model->id){
                    var_dump($review_model->error);
                }
                log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Review saved');

                // notify user about new review
                if($_review['posted'] > $today_midnight &&
                    !empty($review_model->id) &&
                    !empty($directory_user['user_id']) &&
                    !empty($directory_user['notify'])
                ){

                    $obj = Reviews_notification::addOne($directory_user['user_id'], $review_model->id);
                    if(!$obj->id){
                        echo 'Error notification: '.date('d-m-Y H:i').' - '.$obj->error->string.PHP_EOL;
                    }
                }
            }
        }
    }

    public function notf(){

        $this->load->helper('review_format');

        $subject = 'New reviews';
        $users = Reviews_notification::getUniqUsers();
        foreach($users as $user){
            $dap_user = new User($user['user_id']);
            if(!$dap_user){
                //Reviews_notification::deleteAllByUser($user['user_id']);
                continue;
            }

            $email = $dap_user->email;

            $reviews = Reviews_notification::getReviewsByUser($user['user_id']);
            if ($reviews->count()) {
                $notification = array(
                    'to' => $email,
                    'subject' => $subject,
                    'body' => array()
                );

                $notification['body']['body'] = $this->template->block('notify_review',
                    'templates/email/notify_review',
                    array('reviews'=> $reviews)
                );
                $notification['body']['content_type'] = 'text/html';

                $sender = $this->get('core.mailer');
                $sender->sendMail($notification['subject'], $notification['body'], $notification['to']);


                // $reviews->delete_all();
            }

        }

    }

    public function google_mentions() {

        $this->load->library('mentioner');

        $keyword = array(
            'keyword' => 'qwe',
            'exact' => 0,
            'other_fields' => serialize(array(
                'include' => array(''), 'exclude' => array('')
            )),
        );

        $data = Mentioner::factory($this->c_user->id)->activities($keyword, array());
        echo '<pre>';
        var_dump($data);

    }

    public function image()
    {
        CssJs::getInst()->add_js(array(
            'controller/tests/index.js',
            'ui/jquery.ui-1.9.2.min.js',
            'libs/jq.file-uploader/jquery.iframe-transport.js',
            'libs/jq.file-uploader/jquery.fileupload.js',
            'libs/jquery.imgareaselect/scripts/jquery.imgareaselect.pack.js',
        ))->add_css(array(
                'ui/jquery.imgareaselect/css/imgareaselect-default.css',
                'test.css'

            ));
        $this->template->render();
    }

    public function jr()
    {
        $j = $this->get('core.job.queue.manager');
        $j->run(1);
    }

    public function conv()
    {

        var_Dump(44); exit;
    }

    public function flash()
    {
        $this->addFlash(TRUE, 'commit');
        redirect('tests/gflash');
    }
    public function gflash()
    {
        var_dump($this->getFlash('commit'));die;
    }

    public function mail()
    {
        $sender = $this->get('core.mail.sender');
        $sender->sendRegistrationMail(array('user' => new User($this->c_user->id)));

    }

    public function rnf()
    {
        $jobQueue = $this->get('core.job.queue.manager');
        $jobQueue->addJob('tasks/reviews_task/notify_send');
        $jobQueue->run(0);
    }

    public function gateway($gateway)
    {
        $gateway = Payment_gateways::findOneBySlug($gateway);
        $transactionManager = $this->get('core.payment.transactions.manager');

        $transaction = $transactionManager->createForSubscription(array(
            'amount' => 1000,
            'description' => 'test 111'
        ), $this->getUser(), $gateway);


        echo $transaction->id;
    }

    public function trs()
    {
        /*$subscriber = $this->get('core.subscriber');
        $subscriber->setUser($this->c_user)->;*/
        $s = new Subscription();
        $s->getByPaymentTransactionId(13);
    }

    public function ments() {
set_time_limit(0);
        // get all keywords, that are not set for deletion
        // and they have a date of last request more then one day ago
        $mention_keywords = Mention_keyword::inst()->get_for_cron_update();


        if (!$mention_keywords->exists()) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No mention keywords for update');
            return;
        }

        $today = date('U', strtotime('today'));
        $yesterday = date('U', strtotime('yesterday'));

        $users_cache = array();
        $aac = $this->getAAC();

        foreach ($mention_keywords as $mention_keyword) {
            $user = new User($mention_keyword->user_id);

            if (!$user->exists()) {
                continue;
            }

            $aac->setUser($user);

            if (!$aac->planHasFeature('brand_reputation_monitoring')) {
                continue;
            }

            if (!isset($users_cache[$mention_keyword->user_id])) {
                $users_cache[$mention_keyword->user_id] = 0;
            }

            $usersBrandReputationMonitoring = $aac->getPlanFeatureValue('brand_reputation_monitoring');

            //$aac->isGrantedPlan('brand_reputation_monitoring')
            if ($usersBrandReputationMonitoring &&
                $users_cache[$mention_keyword->user_id] >= $usersBrandReputationMonitoring) {
                break;
            }

            $users_cache[$mention_keyword->user_id]++;

            // if keywords has some socials set as grabbed, but also has non-requested date
            // clear all socials to try to grab mentions again
            if ($mention_keyword->grabbed_socials
                && $mention_keyword->requested_at
                && $mention_keyword->requested_at < $yesterday
            ) {
                $mention_keyword->grabbed_socials = NULL;
                $saved = $mention_keyword->save();
                if ( ! $saved) {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not updated grabbs: '
                        . $mention_keyword->error->string);
                } else {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Not updated grabbs cleared');
                }
            }

            // get socials user has tokens for
            $socials_for_update = Access_token::inst()->get_user_socials($mention_keyword->user_id);

            // get socials that were already grabbed
            $grabbed_socials = $mention_keyword->get_grabbed_socials_as_array();

            // get socials that were not grabbed yet
            $socials = array_diff($socials_for_update, $grabbed_socials);
$socials = array('instagram');
            if (count($socials)) {

                foreach ($socials as $social) {

                    $args = $mention_keyword->to_array();
                    $args['social'] = $social;

                    try {
                        $this->grabberments($args);
                        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Adding Mentions : ' . $social);



                    } catch(Exception $e) {
                        log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Failed mention : ' . $social
                            . ' ; ' . $e->getMessage());
                        throw $e;
                    }
                }

            } else {
                $mention_keyword->requested_at = $today;
                $mention_keyword->grabbed_socials = NULL;
                $saved = $mention_keyword->save();
                if ($saved) {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Marked as grabbed : mkwid '.
                        $mention_keyword->id);
                } else {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not marked as grabbed : mkwid '
                        . $mention_keyword->id . ' : ' . $mention_keyword->error->string);
                }
            }

        }

        $ids_str = implode(', ', array_values($mention_keywords->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > Mention keywords for update ids: ' . $ids_str);
        return;
    }

    protected function grabberments($mention_keyword_array)
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

            $user_socials = Access_token::inst()->get_user_socials($mention_keyword->user_id);

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

            if ($social === 'facebook') {
                $data = $mentioner->posts($mention_keyword_data, $mention_keyword_array);
            } else if ($social === 'twitter') {
                $data = $mentioner->tweets($mention_keyword_data, $mention_keyword_array);
            } else if ($social === 'google') {
                $data = $mentioner->activities($mention_keyword_data, $mention_keyword_array);
            } else if ($social === 'instagram') {
                $data = $mentioner->tags($mention_keyword_data, $mention_keyword_array);
            } else {
                $data = array();
            }

            if (!is_array($data)) {
                throw new Exception($error_info . ' no results for mentions, not an array. mkwid: ');
            }

            if ($user->ifUserHasConfigValue('auto_follow_twitter')) {
                $autoFollowTwitter = true;
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
                                $mentionTwitter = new Mention_facebook();
                                $mentionTwitter->friends_count = $friendsCount;
                                $mentionTwitter->comments_count = $commentsCount;
                                $mentionTwitter->likes_count = $likesCount;
                                $mentionTwitter->mention_id = $mention->id;
                                $mentionTwitter->save();
                            }

                            break;
                        case 'google':
                            $peopleCount = (int)Arr::path($row, 'other_fields.people_count');
                            $commentsCount = (int)Arr::path($row, 'other_fields.comments');
                            $plusonersCount = (int)Arr::path($row, 'other_fields.plusoners');
                            $resharersCount = (int)Arr::path($row, 'other_fields.resharers');

                            if ($peopleCount || $commentsCount || $plusonersCount || $resharersCount) {
                                $mentionTwitter = new Mention_google();
                                $mentionTwitter->people_count = $peopleCount;
                                $mentionTwitter->comments_count = $commentsCount;
                                $mentionTwitter->plusoners_count = $plusonersCount;
                                $mentionTwitter->resharers_count = $resharersCount;
                                $mentionTwitter->mention_id = $mention->id;
                                $mentionTwitter->save();
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

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Mentions for mkwid: ' . $error_info . ' grabbed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }


    public function crms() {
        set_time_limit(0);
        // get all keywords, that are not set for deletion
        // and they have a date of last request more then one day ago
        $crmDirectories = Crm_directory::inst()->getForUpdate();


        if (!$crmDirectories->exists()) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No crm directories for update');
            return;
        }

        $today = date('U', strtotime('today'));
        $yesterday = date('U', strtotime('yesterday'));

        $users_cache = array();
        $aac = $this->getAAC();

        foreach ($crmDirectories as $directory) {
            $user = new User($directory->user_id);

            if (!$user->exists()) {
                continue;
            }

            $aac->setUser($user);

            if (!$aac->planHasFeature('crm')) {
                continue;
            }

            if (!isset($users_cache[$directory->user_id])) {
                $users_cache[$directory->user_id] = 0;
            }

            $usersCrm = $aac->getPlanFeatureValue('crm');

            //$aac->isGrantedPlan('brand_reputation_monitoring')
            if ($usersCrm &&
                $users_cache[$directory->user_id] >= $usersCrm) {
                break;
            }

            $users_cache[$directory->user_id]++;

            // if keywords has some socials set as grabbed, but also has non-requested date
            // clear all socials to try to grab mentions again
            if ($directory->grabbed_socials
                && $directory->requested_at
                && $directory->requested_at < $yesterday
            ) {
                $directory->grabbed_socials = NULL;
                $saved = $directory->save();
                if ( ! $saved) {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not updated grabbs: '
                        . $mention_keyword->error->string);
                } else {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Not updated grabbs cleared');
                }
            }

            // get socials user has tokens for
            $socials_for_update = Access_token::inst()->get_crm_user_socials($directory->user_id);

            // get socials that were already grabbed
            $grabbed_socials = $directory->get_grabbed_socials_as_array();

            // get socials that were not grabbed yet
            $socials = array_diff($socials_for_update, $grabbed_socials);$socials=array('instagram');
            if (count($socials)) {

                foreach ($socials as $social) {

                    $args = $directory->to_array();
                    $args['social'] = $social;

                    try {
                        $this->grabbercrms($args);
                        log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Adding Mentions : ' . $social);



                    } catch(Exception $e) {
                        log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Failed mention : ' . $social
                            . ' ; ' . $e->getMessage());
                        throw $e;
                    }
                }

            } else {
                $directory->requested_at = $today;
                $directory->grabbed_socials = NULL;
                $saved = $directory->save();
                if ($saved) {
                    log_message('CRON_SUCCESS', __FUNCTION__ . ' > ' . 'Marked as grabbed : mkwid '.
                        $directory->id);
                } else {
                    log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'Not marked as grabbed : mkwid '
                        . $directory->id . ' : ' . $directory->error->string);
                }
            }

        }

        $ids_str = implode(', ', array_values($directory->all_to_single_array('id')));

        log_message('CRON_SUCCESS', __FUNCTION__ . ' > Mention keywords for update ids: ' . $ids_str);
        return;
    }

    protected function grabbercrms($directory_array)
    {

        try {

            $directory_id = Arr::get($directory_array, 'id');
            $directory = new Crm_directory($directory_id);

            $error_info = 'mkwid: ' . Arr::get($directory_array, 'social', 'no soc') . '/' . $directory_id;

            if (!$directory->exists()) {
                throw new Exception($error_info . ' doesn\'t exist.');
            }

            if ($directory->is_deleted) {
                throw new Exception($error_info . ' is set for deletion.');
            }

            if (!$directory->user_id) {
                throw new Exception($error_info . ' has no user id.');
            }

            $user = new User($directory->user_id);
            if (!$user->exists()) {
                throw new Exception($error_info . ' has no user');
            }

            $social = Arr::get($directory_array, 'social');
            if (is_null($social)) {
                throw new Exception($error_info . ' invalid social');
            }

            $user_socials = Access_token::inst()->get_crm_user_socials($directory->user_id);

            if (!in_array($social, $user_socials)) {
                throw new Exception($error_info . ' invalid social');
            }

            $this->load->library('crmer');
            $crmer = Crmer::factory($user->id);


            if ($social === 'facebook') {
                $data = $crmer->getCrmPosts($directory_array);
            } else if ($social === 'twitter') {
                $data = $crmer->getCrmTweets($directory_array);
            } else if ($social === 'instagram') {
                $data = $crmer->getCrmActivities($directory_array);
            } else {
                $data = array();
            }

            if (!is_array($data)) {
                throw new Exception($error_info . ' no results for mentions, not an array. mkwid: ');
            }

            foreach ($data as $original_id => $row) {

                $activity = new Crm_directory_activity();
                $activity->where(array(
                    'crm_directory_id' => $directory_array['id'],
                    'original_id'        => $original_id,
                ))->get(1);

                $activity->social = $social;
                $activity->original_id = Arr::get($row, 'original_id');
                $activity->created_at = Arr::get($row, 'created_at');

                $message = Arr::get($row, 'message');
                $trimMessage = (strlen($message)>4000) ? substr($message, 0, 4000) : $message;
                $activity->message = $trimMessage;

                $activity->creator_id = Arr::get($row, 'creator_id');
                $activity->creator_name = Arr::get($row, 'creator_name');
                $activity->creator_image_url = Arr::get($row, 'creator_image_url');
                $activity->other_fields = serialize(Arr::get($row, 'other_fields', array()));
                $activity->source = Arr::get($row, 'source');

                $relations = array(
                    'crm_directory' => $directory,
                );

                $saved = $activity->save($relations);


                if (!$saved) {
                    log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Activity not saved for mkwid: ' . $error_info . ' grabbed: ' . $activity->error->string);
                }
            }

            // get socials that were already grabbed
            $grabbed_socials = $directory->get_grabbed_socials_as_array();

            if (!in_array($social, $grabbed_socials)) {

                $grabbed_socials[] = $social;
                $now = date('U');

                $directory->grabbed_socials = implode(',', $grabbed_socials);
                $directory->grabbed_at = $now;

                $saved = $directory->save();

                if (!$saved) {
                    log_message('TASK_ERROR', __FUNCTION__ . ' > ' . 'Mention keyword not saved for mkwid: ' . $error_info . ' grabbed: ' . $activity->error->string);
                }
            }

            log_message('TASK_SUCCESS', __FUNCTION__ . ' > ' . 'Mentions for mkwid: ' . $error_info . ' grabbed');

        } catch (Exception $e) {
            log_message('TASK_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());

            return;
            // throw $e;
        }
    }
}