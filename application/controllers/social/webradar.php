<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Webradar extends MY_Controller {

    protected $website_part = 'dashboard';
    protected $dateFormat = 'M j, Y';
    
    private $activeSocials;
    protected $configured = array();

    public function __construct() {
        parent::__construct();

        $this->lang->load('web_radar', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('web_radar', $this->language)
        ]);

        $hasKeywords = Mention_keyword::inst()->has_keywords($this->c_user->id);
        $this->template->set('hasKeywords', $hasKeywords);

        $hasRequested = Mention_keyword::inst()->has_requested($this->c_user->id);
        $this->template->set('hasRequested', $hasRequested);


        //CssJs::getInst()->add_js(array('libs/calendar/prototype.js', 'libs/calendar/timeframe.js'));

    }
    
    public function index($social = null)
    {
        $this->load->helper('clicable_links');
        /* @var Core\Service\Radar\Radar $radar */
        $radar = $this->get('core.radar');
        $this->config->load('web_radar');

        $limit = $this->config->config['mentions_limit'];
        $offset =  Arr::get($_GET, 'offset', 0);
        $radar->addFilterParams(array(
                'user_id' => $this->c_user->id,
                'profile_id' => $this->profile->id
            ))->setLimit($limit)
            ->setOffset($offset);

        $socials = $radar->getSocials();
        if ($social && !in_array($social, $socials)) {
            $social = null;
        }

        $this->activeSocials = Access_token::inst()->get_user_socials($this->c_user->id, $this->profile->id);

        list($from, $to) = $this->getDatesFromRequest();

        if ($keyword = Arr::get($_GET, 'keyword', 0)) {
            $radar->addFilterParams(array('mention_keyword_id' => $keyword));
        }
        if ($social) {
            $radar->addFilterParams(array('social' => $social));
        }
        if ($from) {
            $radar->addFilterParams(array('from' => $from->getTimestamp()));
        }
        if ($to) {
            $radar->addFilterParams(array('to' => $to->getTimestamp()));
        }

        $mentions = $radar->getRadarMentions();

        $feed = $this->getHtmlData($mentions);
        if ($this->template->is_ajax()) {
            echo json_encode(array('html' => $feed));
            exit;
        }

        $keywordQueryStr = ($keyword) ? '&keyword=' . $keyword : '';
        $formatedFrom = $from->format($this->dateFormat);
        $formatedTo = $to->format($this->dateFormat);
        $dates = array('from' => $formatedFrom, 'to' => $formatedTo);
        
        $keywords = Mention_keyword::inst()->dropdown($this->c_user->id, $this->profile->id);
        $keywordsForHighlight = Mention_keyword::inst()
                ->get_for_highlight($this->c_user->id, intval($keyword));
        
        CssJs::getInst()->add_js(array(
            'controller/webradar/index.js',
            'libs/jsUtils.min.js'
        ));
        
        JsSettings::instance()->add(array(
            'non_ajax_pagination' => true,
            'keywords' => $keywordsForHighlight,
            'keyword_query_id' => $keyword,
            'keyword_query_str' => $keywordQueryStr,
            'social' => $social,
            'socials' => $socials,
            'dates' => $dates,
        ));
        $this->template->set('dates', $dates);
        $this->template->set('dateRange', $diff = $from->diff($to)->format("%a"));
        $this->template->set('keyword', $keyword);
        $this->template->set('keywordQueryStr', $keywordQueryStr);
        $this->template->set('keywords', $keywords);
        $this->template->set('socials', $socials);
        $this->template->set('feed', $feed);
        $socialName = ($social) ? ucfirst($social) : lang('all_mentions');
        $this->template->set('social', $socialName);

        $this->template->current_view = 'social/webradar/index';
        $this->template->render();
    }
    
    public function twitter()
    {
        $this->index('twitter');
    }
    
    public function google()
    {
        $this->index('google');
    }
    
    public function facebook()
    {
        $this->index('facebook');
    }

    public function instagram()
    {
        $this->index('instagram');
    }
    
    /**
     * Prepare "request" and fetch from and to dates
     *
     * @return array of DateTime, 0 - from; 1 - to;
     */
    public function getDatesFromRequest()
    {
        $from = Arr::get($_GET, 'from');//var_dump($from, DateTime::createFromFormat($this->dateFormat, $from));die;
        if (!($from = DateTime::createFromFormat($this->dateFormat, $from))) {
            $from = new DateTime('-7 days');
        }

        $to = Arr::get($_GET, 'to');

        if (!($to = DateTime::createFromFormat($this->dateFormat, $to))) {
            $to = new DateTime('tomorrow');
        }

        $from->setTime(0, 0, 0);
        $to->setTime(23, 59, 59);

        return array($from, $to);
    }

    /**
     * Return html of feed
     *
     * @param $mentions
     *
     * @return string
     */
    private function getHtmlData($mentions)
    {
        $htmlData = '';
        if ($mentions->exists()) {
            $this->load->library('Socializer/socializer');
            $facebook = Socializer::factory('Facebook', $this->c_user->id);
            $fbUserImage = $facebook->get_profile_picture();
            $wlist = Influencers_whitelist::create()->getByUser($this->c_user->id);
            foreach ($mentions as $mention) {
                $social = $mention->social;
                /* @var Core\Service\Radar\Radar $radar */
                $radar = $this->get('core.radar');
                if ($social == 'facebook') {
                    $mention->creator_image_url = $facebook->get_profile_picture($mention->creator_id);
                    $mention->user_image = $fbUserImage;
                }
                $mention->actions = in_array($social, $this->activeSocials);
                $mention->influencer = array_key_exists($mention->creator_id, $wlist) && $wlist[$mention->creator_id] == $mention->social;
                $mention->created_at = $radar->formatRadarDate($mention->created_at);
                $mention->profileUrl = $radar->getProfileUrl($mention->social);
                $content = $this->template->block('_content', '/social/webradar/blocks/'.$mention->social, array('mention' => $mention));
                
                $blockData = array('mention' => $mention, 'content' => $content);
                $htmlData .= $this->load->view('social/webradar/blocks/_feed', $blockData, true);
            }
        }
        
        return $htmlData;
    }
          
}