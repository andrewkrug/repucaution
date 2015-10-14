<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Create extends MY_Controller
{

    protected $website_part = 'dashboard';
    protected $is_user_set_timezone;


    const RSS_POSTS_COUNT = 5;
    
    public function __construct()
    {
        parent::__construct();

        $this->lang->load('social_create', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_create', $this->language)
        ]);

        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');
        $this->load->helper('my_url_helper');
        $this->template->set('isSupportScheduledPosts', $this->isSupportScheduledPosts);

        $this->is_user_set_timezone = User_timezone::is_user_set_timezone($this->c_user->id);
        JsSettings::instance()->add(
            array(
                'twitterLimits' => array(
                    'maxLength' => 140,
                    'midLength' => 117,
                    'lowLength' => 94
                ),
                'twitterLimitsText' => lang('twitter_error'),
                'linkedinLimits' => array(
                    'maxLength' => 400,
                    
                ),
                'linkedinLimitsText' => lang('linkedin_error'),
            )
        );
        CssJs::getInst()->c_js('social/create', 'social_limiter');
        CssJs::getInst()->c_js('social/create', 'schedule_block');

    }

    public function index()
    {
        $this->template->render();
    }
    
    /**
     * Create posts single page
     */
    public function update()
    {
        $this->_attach_update_scripts();
        $this->load->helper('Image_designer_helper');

        $this->template->set('imageDesignerImages', Image_designer::getImages());
        $this->template->set('socials', Social_post::getActiveSocials($this->profile->id));
        $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));
        $this->template->set('need_bulk_upload_notification',   User_notification::needShowNotification($this->c_user->id, User_notification::BULK_UPLOAD));

        $this->template->render();
    }

    /**
     * Create post action
     */
    public function post_create()
    {
        if ($this->template->is_ajax()) {
            $post = $this->input->post();

            if (!empty($post['posting_type']) &&
                $post['posting_type'] == 'schedule' &&
                !$this->isSupportScheduledPosts
            ) {
                $this->renderJson(array(
                    'errors' => array(
                        'when_post' => lang('when_post_error'),
                    ),
                ));
            }
            if(in_array('facebook', $post['post_to_socials'])) {
                $this->load->library('Socializer/socializer');
                /* @var Socializer_Facebook $facebook */
                $facebook = Socializer::factory('Facebook',
                    $this->c_user->id,
                    $this->profile->getTokenByTypeAsArray('facebook')
                );
                if(!$facebook->getFanpageId()) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => lang('facebook_fanpage_error')
                    ));
                    exit();
                }
            }
            if($this->isDemo()) {
                echo json_encode(array(
                    'success' => false,
                    'message' => lang('demo_version_error')
                ));
                exit();
            }
            try {
                if(isset($post['is_cron'])) {
                    $this->add_cron_post($post);
                } else {
                    switch ($post['attachment_type']) {
                        case 'photos':
                            $this->post_photo($post);
                            break;
                        case 'videos':
                            $this->post_video($post);
                            break;
                        case 'image-designer':
                            $this->post_photo($post);
                            break;
                        default:
                            $this->post_link($post);
                            break;
                    }
                }
            } catch(Exception $e) {
                echo json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage()
                ));
            }

        }
    }

    public function uploadImageDesignerFile() {
        if ($this->template->is_ajax()) {
            $post = $this->input->post();
            $data = base64_decode($post['image_designer_data_url']);

            $urlUploadImages = dirname($_SERVER['SCRIPT_FILENAME']) . '/public/uploads/' . $this->c_user->id . '/';
            if(!is_dir($urlUploadImages)) {
                mkdir($urlUploadImages, 0777, TRUE);
            }
            $nameImage = time() . '.png';

            $img = imagecreatefromstring($data);

            imageAlphaBlending($img, true);
            imageSaveAlpha($img, true);

            $answer = array(
                'success' => true,
                'image_name' => $nameImage
            );

            if ($img) {
                imagepng($img, $urlUploadImages . $nameImage, 0);
                imagedestroy($img);
            } else {
                $answer['success'] = false;
            }
            echo json_encode($answer);
        }
    }

    /**
     * Post rss page
     */
    public function post_rss()
    {

        $post = $this->input->post();

        if (!empty($post['feed'])) {
            $feedId = $post['feed'];
        } else {
            $feeds = Rss_feed::inst()->user_feeds($this->c_user->id, $this->profile->id);
            $feedId = key($feeds);
        }

        $content = $this->getRssByFeedId($feedId);
        if ($this->template->is_ajax()) {
            echo json_encode(array(
                'success' => true,
                'html' => $content,
            ));
            exit;
        }
        $this->_attach_update_scripts();
        $data = $this->_check_socials_access();
        $this->_add_vars_to_template($data, array('feeds'=> $feeds, 'content' => $content));

        JsSettings::instance()->add('twitterDefaultType', 'midLength');

        $this->template->render();
    }

    public function redirect()
    {
        redirect('social/create/update');
    }

    public function bulk_upload() {
        if ($this->template->is_ajax()) {
            $files = $_FILES['files'];
//            if($files['type'][0] != 'text/csv') {
//                echo json_encode(array(
//                    'succes' => false,
//                    'error' => array(
//                        'message' => 'Wrong file type.'
//                    )
//                ));
//            } else {
                $this->load->helper('bulk_upload_helper');
                $answer = Bulk_upload::getScheduledPostsArray($files['tmp_name'][0], $this->c_user->id, $this->profile->id);
                if(!$answer['success']) {
                    echo json_encode(array(
                        'success' => false,
                        'error' => array(
                            'message' => $answer['error']
                        )
                    ));
                } else {
                    foreach($answer['data'] as $post) {
                        if(!Social_post::checkPostByDescription($post['description'])) {
                            $this->load->library('Socializer/socializer');
                            Social_post::add_new_post($post, $this->c_user->id, $this->profile->id);
                        }
                    }
                    echo json_encode(array(
                        'success' => true
                    ));
                }
//            }
        }
    }

    public function update_notification() {
        if ($this->template->is_ajax()) {
            $post = $this->input->post();
            if(User_notification::setNotification($this->c_user->id, $post['notification'], $post['show'])) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => lang('something_went_wrong')
                ]);
            }
        }
    }

    /**
     * Used to load 'custom RSS feed' block
     * Called by AJAX-request
     *
     * @access public
     * @return void
     */
    public function get_custom_rss_feed_html()
    {
        if ($this->template->is_ajax()) {
            $user_rss = Rss_feed::inst()->user_custom_feeds($this->c_user->id, $this->profile->id);
            if ($user_rss->result_count() > 0) {
                echo $this->template->block(
                    '_suggested_links',
                    'social/create/blocks/_custom_rss',
                    array('rss' => $user_rss)
                );
            } else {
                $this->addFlash('You need to add some rss before.');
                echo '<br>' . $this->template->block(
                        '_alert',
                        'blocks/alert',
                        array()
                    ) . '<br>';
            }
        }
        exit();
    }

    /**
     * Grab RSS data using SimplePie Library
     * Paginate / Limiting Search results
     * After collecting info - send it to view and get feed html
     *
     * @access public
     * @return void
     */
    public function get_rss_feed()
    {
        if ($this->template->is_ajax()) {
            $post = $this->input->post();
            $html = '';
            if (isset($post['link'])) {
                $this->load->library('Simplepie');
                $this->simplepie->set_feed_url($post['link']);
                $this->simplepie->set_cache_location(APPPATH . 'cache/rss');
                if (!file_exists(APPPATH . 'cache/rss') && !is_writable(APPPATH . 'cache/rss')) {
                    $old = umask(0);
                    mkdir(APPPATH . 'cache/rss', 0777);
                    umask($old);
                }
                $this->simplepie->init();
                $this->simplepie->handle_content_type();
                $limit = self::RSS_POSTS_COUNT;
                $pagenum = isset($post['pagenum']) ? (int)$post['pagenum'] : 1;
                $pagenum = $pagenum <= 0 ? 1 : $pagenum;
                $offset = ($pagenum - 1) * $limit;
                $rss_feed = $this->simplepie->get_items($offset, $limit);
                $html = $this->template->block(
                    '_rss_feed',
                    'social/create/blocks/_rss_feed',
                    array('rss_feed' => $rss_feed, 'pagenum' => $pagenum)
                );
            }
            echo $html;
        }
        exit();
    }

    
    /**
     * Grab RSS data using SimplePie Library
     * by feed id
     * 
     * @param int $feedId
     * @return string
     */
    public function getRssByFeedId($feedId)
    {
            $html = '';
            if ($feedId) {
                $feed = Rss_feed::inst($feedId);
                $this->load->library('Simplepie');
                $this->simplepie->set_feed_url($feed->link);
                $this->simplepie->set_cache_location(APPPATH . 'cache/rss');
                if (!file_exists(APPPATH . 'cache/rss') && !is_writable(APPPATH . 'cache/rss')) {
                    $old = umask(0);
                    mkdir(APPPATH . 'cache/rss', 0777);
                    umask($old);
                }
                $this->simplepie->init();
                $this->simplepie->handle_content_type();
                $limit = self::RSS_POSTS_COUNT;
                $rss_feed = $this->simplepie->get_items();
                $html = $this->template->block(
                    '_rss_feed',
                    'social/create/blocks/_rss_feeder',
                    array('rss_feed' => $rss_feed)
                );
            }
            echo $html;
        
    }
    
    /**
     * Used to post a new link to socials
     * ('post a link' form action)
     *
     * @access public
     * @return void
     */
    public function post_link($post) {
        if ($this->template->is_ajax()) {
            $post['post_to_groups'] = array($this->profile->id);
            $post['title'] = 'from '.get_instance()->config->config['OCU_site_name'];
            $this->bitly_load();

            // add http://
            if (!empty($post['url']) && !preg_match("~^(?:f|ht)tps?://~i", $post['url'])) {
                $post['url'] = "http://" . $post['url'];
            }

            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);
            $errors = Social_post::validate_post($post);

            if (empty($errors)) {

                try {
                    if (!isset($post['post_id'])) {

                        if (!empty($post['url'])) {

                            if ($this->bitly) {
                                $bitly_data = $this->bitly->shorten($post['url']);
                                if (strlen($bitly_data['url']) < strlen($post['url'])) {
                                    $post['url'] = $bitly_data['url'];
                                }

                            }
                        }

                    }

                    $this->load->library('Socializer/socializer');
                    Social_post::add_new_post($post, $this->c_user->id, $this->profile->id);

                    $result['success'] = true;
                    $result['message'] = lang('post_was_successfully_added');
                } catch(Exception $e) {
                    $result['success'] = false;
                    $result['errors']['post_to_groups[]'] = '<span class="message-error">' . $e->getMessage() . '</span>';
                }

            } else {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            echo json_encode($result);
        }
        exit();
    }

    public function post_photo($post) {
        if ($this->template->is_ajax()) {
            $post['post_to_groups'] = array($this->profile->id);
            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);
            $errors = Social_post::validate_post($post);
            if (empty($errors)) {
                $this->load->library('Socializer/socializer');
                Social_post::add_new_post($post, $this->c_user->id, $this->profile->id);
                $result['success'] = true;
                $result['message'] = lang('post_was_successfully_added');
            } else {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            echo json_encode($result);
        }
        exit();
    }

    public function post_video($post) {
        if ($this->template->is_ajax()) {
            $post['post_to_groups'] = array($this->profile->id);
            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);
            $post['user_id'] = $this->c_user->id;
            $post['title'] = 'from '.get_instance()->config->config['OCU_site_name'];
            $errors = Social_post::validate_post($post);
            if (empty($errors)) {
                $this->load->library('Socializer/socializer');
                Social_post::post_video($post, $this->c_user->id, $this->profile->id);
                $result['success'] = true;
                $result['message'] = lang('post_was_successfully_added');
            } else {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            echo json_encode($result);
        }
        exit();
    }

    public function add_cron_post($post) {
        if ($this->template->is_ajax()) {
            $post['post_to_groups'] = array($this->profile->id);
            $post['timezone'] = User_timezone::get_user_timezone($this->c_user->id);
            $post['user_id'] = $this->c_user->id;
            $errors = Social_post::validate_post($post);
            if (empty($errors)) {
                $errors = Social_post_cron::validate_cron($post);
                if(empty($errors)) {
                    Social_post_cron::add_new_post($post, $this->c_user->id, $this->profile->id);
                    $result['success'] = true;
                    $result['message'] = lang('post_was_successfully_added');
                } else {
                    $result['success'] = false;
                    $result['errors'] = $errors;
                }
            } else {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            echo json_encode($result);
        }
        exit();
    }

    /**
     * Check - is user have access to post into socials
     * Get Access Tokens for Facebook / Twitter from our database
     * Also need to check - is user select some Facebook fanpage
     *
     * @access private
     * @return array
     */
    private function _check_socials_access() {
        return Access_token::inst()->check_socials_access($this->c_user->id);
    }
    
    private function _attach_update_scripts() {
        CssJs::getInst()->add_css(array(
           'custom/pick-a-color-1.css'
        ));
        CssJs::getInst()->add_js(array(
            'libs/jq.file-uploader/jquery.iframe-transport.js',
            'libs/jq.file-uploader/jquery.fileupload.js',
            'libs/fabric/fabric.min.js',
            'libs/fabric/StackBlur.js',
            'libs/color/tinycolor-0.9.15.min.js',
            'libs/color/pick-a-color-1.2.3.min.js'
        ));
        CssJs::getInst()->c_js('social/create', 'post_update');
        CssJs::getInst()->c_js('social/create', 'post_attachment');
        CssJs::getInst()->c_js('social/create', 'post_cron');
        CssJs::getInst()->c_js('social/create', 'bulk_upload');
        CssJs::getInst()->c_js('social/create', 'social_limiter');
        CssJs::getInst()->c_js('social/create', 'schedule_block');
    }

    /**
     * Add socials data and category id to template
     *
     * @access   public
     *
     * @param       $socials_data
     * @param array $params
     *
     * @internal param $category_slug
     */
    private function _add_vars_to_template($socials_data, $params = array()) {
        $this->template->set($socials_data);
        $this->template->set('is_user_set_timezone', $this->is_user_set_timezone);
        if (!empty($params)) {
            foreach ($params as $k=>$v) {
                $this->template->set($k, $v);
            }
        }
    }

    /**
     * Used to upload/delete campaign gallery images
     *
     * @access public
     * @return void
     */
    public function upload_images() {
        $this->load->library('MY_upload_handler');
        $upload_handler = new MY_upload_handler($this->c_user->id);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->get();
                }
                break;
            case 'DELETE':
                if ($postId = $this->getRequest()->query->get('post_id', '')) {
                    $post = new Social_post($postId);
                    $post->media->delete_all();
                }
                $upload_handler->delete();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->post();
                }
                break;

            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
    }
}