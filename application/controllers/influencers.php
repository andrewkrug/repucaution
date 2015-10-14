<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Influencers extends MY_Controller {

    protected $website_part = 'dashboard';
    protected $dateFormat = 'M j, Y';

    private $activeSocials;
    protected $configured = array();

    public function __construct() {
        parent::__construct();

        $hasKeywords = Mention_keyword::inst()->has_keywords($this->c_user->id);
        $this->template->set('hasKeywords', $hasKeywords);

        $hasRequested = Mention_keyword::inst()->has_requested($this->c_user->id);
        $this->template->set('hasRequested', $hasRequested);

        CssJs::getInst()->add_css(array('calendar/timeframe-style.css'));

    }


    public function index()
    {
        $this->load->helper('clicable_links');
        $radar = $this->get('core.radar');
        $this->config->load('web_radar');

        $limit = $this->config->config['mentions_limit'];
        $offset =  Arr::get($_GET, 'offset', 0);
        $radar->addFilterParams(array('user_id' => $this->c_user->id))
            ->setLimit($limit)
            ->setOffset($offset);
        $socials = $radar->getSocials();
        $this->activeSocials = Access_token::inst()->get_user_socials($this->c_user->id, $this->profile->id);
        //$this->fbUserImageUrl = $radar->getFBUserImage($this->c_user->id);
        $from = date($this->dateFormat, strtotime(Arr::get($_GET, 'from', 'yesterday')));
        $to = date($this->dateFormat, strtotime(Arr::get($_GET, 'to', 'today')));
        list($from, $to) = $this->getDatesFromRequest();

        if ($keyword = Arr::get($_GET, 'keyword', 0)) {
            $radar->addFilterParams(array('mention_keyword_id' => $keyword));
        }
        if ($social = Arr::get($_GET, 'social', 0)) {
            $radar->addFilterParams(array('social' => $social));
        }
        if ($from) {
            $radar->addFilterParams(array('from' => $from->getTimestamp()));
        }
        if ($to) {
            $radar->addFilterParams(array('to' => $to->getTimestamp()));
        }

        $mentions = $radar->getRadarInfluencers($this->c_user->id);


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

        CssJs::getInst()->c_js();

        JsSettings::instance()->add(array(
            'non_ajax_pagination' => true,
            'keywords' => $keywordsForHighlight,
            'keyword_query_id' => $keyword,
            'keyword_query_str' => $keywordQueryStr,
            'socials' => $socials,
            'dates' => $dates,
        ));
        $this->template->set('dates', $dates);
        $this->template->set('keyword', $keyword);
        $this->template->set('keywordQueryStr', $keywordQueryStr);
        $this->template->set('keywords', $keywords);
        $this->template->set('socials', $socials);
        $this->template->set('feed', $feed);
        $this->template->render();
    }

    /**
     * Prepare "request" and fetch from and to dates
     *
     * @return array of DateTime, 0 - from; 1 - to;
     */
    public function getDatesFromRequest()
    {
        $from = Arr::get($_GET, 'from');
        if (!($from = DateTime::createFromFormat($this->dateFormat, $from))) {
            $from = new DateTime('yesterday');
        }

        $to = Arr::get($_GET, 'to');

        if (!($to = DateTime::createFromFormat($this->dateFormat, $to))) {
            $to = new DateTime('today');
        }

        $from->setTime(0, 0, 0);
        $to->setTime(23, 59, 59);

        return array($from, $to);
    }

    /**
     * Return html of feed
     *
     * @return string
     */
    private function getHtmlData($mentions)
    {
        $htmlData = '';
        if ($mentions->exists()) {
            foreach ($mentions as $mention) {
                $this->load->library('Socializer/socializer');
                $access_token = Access_token::inst($mention->access_token_id)->to_array();
                /* @var Socializer_Facebook $facebook */
                $facebook = Socializer::factory('Facebook', $this->c_user->id, $access_token);
                $fbUserImage = $facebook->get_profile_picture();
                $social = $mention->social;
                $wlist = new Influencers_whitelist();
                $wlist->add($this->c_user->id, $mention->creator_id, $social);

                $radar = $this->get('core.radar');
                if ($social == 'facebook') {
                    $mention->creator_image_url = $facebook->get_profile_picture($mention->creator_id);
                    $mention->user_image = $fbUserImage;
                }
                $mention->actions = in_array($social, $this->activeSocials);
                $mention->created_at = $radar->formatRadarDate($mention->created_at);
                $mention->profileUrl = $radar->getProfileUrl($mention->social);
                $content = $this->template->block('_content', '/influencers/blocks/'.$social, array('mention' => $mention));

                $blockData = array('mention' => $mention, 'content' => $content);
                $htmlData .= $this->load->view('influencers/blocks/_feed', $blockData, true);
            }
        }

        return $htmlData;
    }

    /**
     * Delete user from influencer
     */
    public function delete()
    {
        if ($post = $this->input->post()) {
            $wlist = new Influencers_whitelist();
            $wlist->remove($this->c_user->id, $post['creator_id'], $post['social']);
            $blist = new Influencers_blacklist();
            $result = $blist->add($this->c_user->id, $post['creator_id'], $post['social']);
            echo json_encode(array('success' => $result));
        }
    }

    /**
     * Add user to influencer
     */
    public function add()
    {
        if ($post = $this->input->post()) {
            $blist = new Influencers_blacklist();
            $blist->remove($this->c_user->id, $post['creator_id'], $post['social']);
            $wlist = new Influencers_whitelist();
            $result = $wlist->add($this->c_user->id, $post['creator_id'], $post['social']);
            echo json_encode(array('success' => $result));
        }
    }

}