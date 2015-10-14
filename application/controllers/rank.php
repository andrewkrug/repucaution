<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rank extends MY_Controller {

    protected $website_part = 'dashboard';

    // all user keywords
    protected $keywords;

    // filter periods array
    protected $all_periods;
    // filter periods names array
    protected $all_periods_names;

    // periods available for user depending on the first rank update date
    protected $available_periods;
    // periods names available for user depending on the first rank update date
    protected $available_periods_names;        


    protected $configured;


    public function __construct() {
        parent::__construct($this->website_part);

        $this->lang->load('rank', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('rank', $this->language)
        ]);
           
        $this->all_periods = array(
            '0' => strtotime('today'), // default period
            '1' => strtotime('-1 month 1 day'),
            '2' => strtotime('-3 months 1 day'),
            '3' => strtotime('-6 months 1 day'),
            '4' => strtotime('-1 year 1 day'),
        );

        $this->all_periods_names = array(
            '0' => lang('period_all_time'), // default period
            '1' => lang('period_1_month'),
            '2' => lang('period_3_months'),
            '3' => lang('period_6_months'),
            '4' => lang('period_1_year'),
        );

        $has_address =  User_additional::inst()->get_by_user_and_profile(
            $this->c_user->id,
            $this->profile->id
        )->address_id;
        if ( ! $has_address) {
            $this->configured['no_address'] = TRUE;
        }

        $has_keywords = Keyword::inst()->has_keywords($this->c_user->id, $this->profile->id);
        if ( ! $has_keywords) {
            $this->configured['no_keywords'] = TRUE;
        }

        $first_rank_date = Keyword::inst()->first_rank_date($this->c_user->id, $this->profile->id);
        $first_rank_time = strtotime($first_rank_date);

        $has_results = (bool) $first_rank_date;
        if ( ! $has_results) {
            $this->configured['no_results'] = TRUE;
        }

        // set available periods depending on first rank update
        foreach($this->all_periods as $key => $value) {
            if ($value >= $first_rank_time) {
                $this->available_periods[$key] = $value;
                $this->available_periods_names[$key] = $this->all_periods_names[$key];
            }
        }

        $this->load->library('gls');
    }

    /**
     * Default rank page
     */
    public function index() {

        $this->keywords = Keyword::inst()->with_rank($this->c_user->id, $this->profile->id);

        $this->_force_configure();

        $available_periods_names = count($this->available_periods_names) > 1 ? $this->available_periods_names : array();

        JsSettings::instance()->add(array(
            'google_rank' => array(
                'filter_url' => site_url('rank/filter'),
            ),
        ));

        CssJs::getInst()->add_js(array('libs/handlebar.js', 'libs/handlebars_helpers.js'))
            ->c_js();

        $this->template->set('available_periods_names', $available_periods_names);
        $this->template->set('keywords', $this->keywords);
        $this->template->render();
    }

    /**
     * Ajax filter by period
     */
    public function filter() {

        try {

            if ( ! empty($this->configured)) {
                if (isset($configured['no_website'])) {
                    throw new Exception('Website not set.');
                }
                if (isset($configured['no_keywords'])) {
                    throw new Exception('No keywords.');
                }
            }   

            $period_id = (int) $this->input->post('period_id');

            if ( ! isset($this->available_periods[$period_id]) ) {
                $period_id = 0;
            }

            $period_time = $this->all_periods[ $period_id ];

            $period_date = $period_id != 0 ? date('Y-m-d', $period_time) : NULL;

            $max_results = $this->gls->max_results();
            $max_message = $this->gls->get('no_results_message');

            $keywords = Keyword::inst()->with_rank($this->c_user->id, $this->profile->id, $period_date, $max_results, $max_message);

            $result['success'] = TRUE;
            $result['result'] = $keywords->all_to_array(array('keyword', 'last_rank', 'rank_change'));

        } catch(Exception $e) {
            $result['success'] = FALSE;
            $result['error'] = $e->getMessage();
        }

        $result['period_id'] = $period_id;
        

        exit( json_encode($result) );

    }

    /**
     * Use this method in other methods 
     * to force redirect to configure page
     */
    protected function _force_configure() {
        if ( ! empty($this->configured) ) {
            redirect('rank/configure');
        }
    }


    /**
     * Must-configure page
     */
    public function configure() {
        if (empty($this->configured)) {
            redirect('rank');
        }
        $this->template->set('configured', $this->configured);
        $this->template->render();
    }

    /**
     * Function for dev to add test rank for keywords
     */
    public function add_test_rank() {
        $start_date = strtotime('-2 year');
        $end_date = strtotime('today');

        $keywords = Keyword::inst()->get_user_keywords($this->c_user->id, $this->profile->id);

        for($i = $start_date; $i <= $end_date; $i += 86400) {

            foreach($keywords as $keyword) {
                $kr = Keyword_rank::inst()->where(array('keyword_id' => $keyword->id, 'date' => date('Y-m-d', $i)))->get(1);
                $kr->rank = rand(1, 100);
                $kr->keyword_id = $keyword->id;
                $kr->date = date('Y-m-d', $i);
                $kr->save();
            }

        }

    }

}