<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mentions extends MY_Controller {

    protected $website_part = 'dashboard';
    protected $dateFormat = 'M j, Y';

    protected $configured = array();

    public function __construct() {
        parent::__construct();

        $has_keywords = Mention_keyword::inst()->has_keywords($this->c_user->id);
        $this->template->set('has_keywords', $has_keywords);

        $has_requested = Mention_keyword::inst()->has_requested($this->c_user->id);
        $this->template->set('has_requested', $has_requested);

    }

    public function social($social = 'facebook') {
		
        $this->load->helper('clicable_links');

        //$social = ($social === 'twitter') ? $social : 'facebook';

        $has_access = Access_token::inst()->get_by_type($social, $this->c_user->id)->exists();
        $this->template->set('has_access', $has_access);

        if ($has_access) {

            $page = max(1, intval(Arr::get($_GET, 'page', 1)));
            $per_page = 10;

            $keyword = Arr::get($_GET, 'keyword', 0);
            $keyword_query_str = ($keyword) ? '&keyword=' . $keyword : '';
            $keywords = Mention_keyword::inst()->dropdown($this->c_user->id);

            $use_dates = (Arr::get($_GET, 'from') || Arr::get($_GET, 'to'));
            $from = date('M j, Y', strtotime(Arr::get($_GET, 'from', 'yesterday')));
            $to = date('M j, Y', strtotime(Arr::get($_GET, 'to', 'today')));
            list($from, $to) = $this->getDatesFromRequest();

            $formatedFrom = $from->format($this->dateFormat);
            $formatedTo = $to->format($this->dateFormat);

            $dates = array('from' => $formatedFrom, 'to' => $formatedTo);
            $dates_query_str = '&from=' . urlencode($formatedFrom) . '&to=' . urlencode($formatedTo);
            $this->template->set('dates', $dates);
            $this->template->set('use_dates', $use_dates);

            $mentions = Mention::inst()->by_social($this->c_user->id, $social);
            if ($keyword) {
                $mentions->where('mention_keyword_id', $keyword);
            }

            $mentions
                ->where('created_at >=', $from->getTimestamp())
                ->where('created_at <=', $to->getTimestamp());

            $mentions->get_paged($page, $per_page);

            $keywords_for_highlight = Mention_keyword::inst()
                ->get_for_highlight($this->c_user->id, intval($keyword));

            JsSettings::instance()->add(array(
                'non_ajax_pagination' => true,
                'keywords' => $keywords_for_highlight,
                'keyword_query_str' => $keyword_query_str,
                'keyword_query_id' => $keyword,
                'dates_query_str' => $dates_query_str,
                'dates' => $dates,
            ));

            CssJs::getInst()
                ->c_js('social/activity', $social)
                ->c_js();

            if ($social === 'facebook') {

                $profile_photo = User_additional::inst()
                    ->get_value($this->c_user->id, 'facebook_profile_photo');

                if (is_null($profile_photo)) {
                    try {
                        $this->load->library('Socializer/socializer');
                        $facebook = Socializer::factory('Facebook', $this->c_user->id);
                        $profile_picture = $facebook->get_profile_picture();
                        if (isset($profile_picture['picture']['data']['url'])) {
                            $profile_photo = $profile_picture['picture']['data']['url'];
                        }

                        if ($profile_photo) {
                            User_additional::inst()->set_value(
                                $this->c_user->id, 
                                'facebook_profile_photo', 
                                $profile_photo
                            );
                        }
                    } catch(Exception $e) {
                        $this->template->set('socializer_error', $e->getMessage());
                    }
                }

                $this->template->set('profile_photo', $profile_photo);
            }

            $this->template->set('keyword', $keyword);
            $this->template->set('keyword_query_str', $keyword_query_str);
            $this->template->set('keywords', $keywords);
            $this->template->set('mentions', $mentions);
        }

        $this->template->set('social', $social);
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
}