<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    protected $website_part = 'dashboard';

    public function __construct() {
        parent::__construct($this->website_part);
        $this->lang->load('dashboard', $this->language);
        $this->lang->load('social_create', $this->language);
        $this->lang->load('social_scheduled', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('dashboard', $this->language)
        ]);
        $this->template->set('breadcrumbs', false);
    }

    public function index() {

        if ($this->c_user->isTrialPlanEnds()) {
            $this->addFlash(lang('subscription_ends_error', [site_url('subscript/plans')]), 'error');
        }


        // UNCOMMENT TO USE

        // get average google rank for all keywords for chart in range

        $keyword_rank = Keyword::average_for_range($this->c_user->id, '-30 days', 'today'); // average result for all the range
        $keywords_trending = Keyword::average_for_range($this->c_user->id, '-30 days', 'today', FALSE); // average for each day in range


        // analytics data
    
        $google_access_token = Access_token::getByTypeAndUserId('googlea', $this->c_user->id);
        list( $ga_visits_chart , $ga_visits_count ) = $google_access_token->google_analytics_dashboard_visits();
    
        $review = new Review();
        $last_reviews_count = $review->last_period_count($this->c_user->id, $this->profile->id);
        $review->clear();

        $social_values = Social_value::inst();
        $social_values->set_values($this->c_user->id, $this->profile->id, array('from' => date('Y-m-d', strtotime('-30 days')), 'to' => date('Y-m-d',time())));

        $all_socials_data = $social_values->get_data();

        $monthly_trending = array(
            'reviews' => $review->last_month_trending($this->c_user->id, $this->profile->id),
            'traffic' => $ga_visits_chart,
            'keywords' => $keywords_trending,
            'twitter_followers' => $all_socials_data['twitter'],
            'facebook_likes' => $all_socials_data['facebook'],
            
        );


        $keywordsForHighlight = Mention_keyword::inst()
            ->get_for_highlight($this->c_user->id, 0);
        CssJs::getInst()->add_js('www.google.com/jsapi', 'external', 'footer');
        CssJs::getInst()->add_js(array(
            'libs/lodash.compat.js',
            'libs/highcharts/highcharts.js'
        ))->c_js();

        $opportunities =  $this->getOpportunities();

        if (!empty($opportunities['web_radar'])) {
            CssJs::getInst()->add_js('controller/webradar/index.js');
        }

        JsSettings::instance()->add(array(
            'monthly_trending' => $monthly_trending,
            'dashboard' => true,
            'keywords' => $keywordsForHighlight,
            'opportunities' => $opportunities
        ));

        $summary = array(
            'reviews' => (int)$last_reviews_count,
            'fb_likes' => (int)$all_socials_data['likes_count'],
            'twiter_followers' => (int)$all_socials_data['followers_count'],
            'web_traffic' => (int)$ga_visits_count,
            'google_rank' => (int)round($keyword_rank, 3),
        );

        $this->isSupportScheduledPosts = $this->getAAC()->isGrantedPlan('scheduled_posts');
        $this->load->helper('my_url_helper');
        $this->template->set('isSupportScheduledPosts', $this->isSupportScheduledPosts);
        $this->template->set('socials', Social_post::getActiveSocials($this->profile->id));

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
        CssJs::getInst()->add_css(array(
            'custom/pick-a-color-1.css'
        ));
        CssJs::getInst()->add_js(array(
            /*'ui/jquery.ui-1.9.2.min.js',*/
            'libs/jq.file-uploader/jquery.iframe-transport.js',
            'libs/jq.file-uploader/jquery.fileupload.js',
            'libs/fabric/fabric.min.js',
            'libs/fabric/StackBlur.js',
            'libs/color/tinycolor-0.9.15.min.js',
            'libs/color/pick-a-color-1.2.3.min.js'
        ));

        CssJs::getInst()->c_js('social/create', 'post_update');
        CssJs::getInst()->c_js('social/create', 'post_cron');
        CssJs::getInst()->c_js('social/create', 'post_attachment');
        CssJs::getInst()->c_js('social/create', 'social_limiter');
        CssJs::getInst()->c_js('social/create', 'schedule_block');
        CssJs::getInst()->c_js('social/create', 'bulk_upload');

        $this->template->set('is_user_set_timezone', User_timezone::is_user_set_timezone($this->c_user->id));

        $user_posts = Social_post::inst()->get_user_scheduled_posts($this->c_user->id, $this->profile->id, 1, 3, 'all');

        $this->template->set('posts', $user_posts);

        $this->load->helper('Image_designer_helper');
        $this->template->set('imageDesignerImages', Image_designer::getImages());
        $this->template->set('summary', $summary);
        $this->template->set('opportunities', $opportunities);
        $this->template->set('need_welcome_notification',  User_notification::needShowNotification($this->c_user->id, User_notification::WELCOME));
        $this->template->render();
    }

    /**
     * Return opportunities for dashboard
     *
     * @return array
     */
    protected function getOpportunities()
    {
        $aac = $this->getAAC();

         $opportunities = array(
            'twitter' => $aac->isGrantedPlan('social_activity'),
            'facebook' => $aac->isGrantedPlan('social_activity'),
            'web_traffic' => $aac->isGrantedPlan('website_traffic_monitoring'),
            'web_radar' => $aac->planHasFeature('brand_reputation_monitoring'),
            'reviews' => $aac->isGrantedPlan('reviews_monitoring'),
            'google_rank' => $aac->isGrantedPlan('local_search_keyword_tracking'),
        );

        $opportunities['summary'] = (
            $opportunities['twitter'] ||
            $opportunities['facebook'] ||
            $opportunities['reviews'] ||
            $opportunities['web_traffic']
        );

        $opportunities['trends'] = (
            $opportunities['twitter'] ||
            $opportunities['facebook'] ||
            $opportunities['web_traffic'] ||
            $opportunities['google_rank'] ||
            $opportunities['reviews']
        );

        return $opportunities;
    }
}