<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduled extends MY_Controller {

    protected $website_part = 'dashboard';

    const POSTS_COUNT = 5;

    public function __construct() {
        parent::__construct();
        $this->lang->load('social_scheduled', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_scheduled', $this->language)
        ]);
        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');
        $this->is_user_set_timezone = User_timezone::is_user_set_timezone($this->c_user->id);
    }

    public function index() {

        CssJs::getInst()->c_js('social/scheduled', 'index');

        $user_posts = Social_post::inst()->get_user_scheduled_posts(
            $this->c_user->id,
            $this->profile->id,
            1,
            self::POSTS_COUNT,
            'all'
        );
        $this->template->set('posts', $user_posts);

        $this->template->render();
    }

    public function get_more_posts() {
        if( $this->template->is_ajax() ) {
            $post = $this->input->post();
            $html = '';
            if(isset($post['page'])) {
                $page = (int)$post['page'];
                $category = isset($post['category_id']) ? (int)$post['category_id'] : 'all';
                $posts = Social_post::inst()->get_user_scheduled_posts(
                    $this->c_user->id,
                    $this->profile->id,
                    $page,
                    self::POSTS_COUNT,
                    $category
                );
                $html = $this->template->block('_scheduled_posts', 'social/scheduled/blocks/_post', array('posts' => $posts, 'current_page' => $page));
            }
            echo $html;
        }
        exit();
    }

    /**
     * Main point of gettings form html
     * More - see function bellow
     *
     * @access public
     * @return void
     */
    public function load_edit_post_html() {
        if( $this->template->is_ajax() ) {

            $post = $this->input->post();
            if( isset($post['post_id'])) {
                $post_id = (int)$post['post_id'];
                $social_post = Social_post::inst($post_id);
                echo $this->_get_edit_post_form($social_post);
            }
        }
    }

    /**
     * Then user click on 'edit' in the schedule list - we need to load some of forms
     * Let's load form from created early scripts (create pages)
     *
     * @access   public
     *
     * @param $social_post
     *
     * @return string
     * @internal param $category_id
     */
    private function _get_edit_post_form($social_post) {
        $post = new Social_post($social_post->id);
        $isMedia = $post->isMediaPost();
        $this->load->helper('MY_url_helper');
        $this->load->helper('Image_designer_helper');
        $this->load->config('timezones');

        $block_data = Access_token::inst()->check_socials_access( $this->c_user->id );
        $block_data['social_post'] = $social_post;
        $block_data['isMedia'] = $isMedia;
        $block_data['isSupportScheduledPosts'] = $this->isSupportScheduledPosts;
        $block_data['is_user_set_timezone'] =  User_timezone::is_user_set_timezone($this->c_user->id);

        $socials = array(
            'twitter' => 'twitter',
            'facebook' => 'facebook',
            'linkedin' => 'linkedin'
        );
        foreach($socials as $social) {
            if(!Social_group::hasSocialAccountByType($this->profile->id, $social)) {
                unset($socials[$social]);
            }
        }
        $block_data['socials'] = $socials;
        $block_data['imageDesignerImages'] = Image_designer::getImages();

        $html = $this->template->block('_edit_form', 'social/create/blocks/_post_update', $block_data);

        return $html;
    }

    public function delete( $post_id ) {
        if(Social_post::inst()->delete_scheduled( (int)$post_id, $this->c_user->id )) {
            $this->addFlash(lang('delete_scheduled_success'), 'success');
        } else {
            $this->addFlash(lang('delete_scheduled_error'));
        }
        redirect('social/scheduled');
    }

}