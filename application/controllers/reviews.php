<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reviews extends MY_Controller {

    protected $website_part = 'dashboard';
    protected $date_format = 'Y-m-d';
    protected $js_datepicker_date_format = 'M d, y';
    protected $js_string_date_format = 'M d, yy';

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('reviews', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('reviews', $this->language)
        ]);
    }

    public function index($directory_id) {
        if(empty($directory_id) || !is_numeric($directory_id)) {
            show_404();
        }

        $directory_user = Directory_User::get_user_dir($this->c_user->id, $directory_id, $this->profile->id);

        if(!$directory_user->exists()) {
            return $this->link_to_config();
        }

        $from = $this->getRequest()->get('from', strtotime('-30 days'));
        $to = $this->getRequest()->get('from', strtotime('now'));
        $from_date = DateTime::createFromFormat($this->date_format, $from);
        $to_date = DateTime::createFromFormat($this->date_format, $to);

        $reviewsTotal = Review::count_by_user_dir($this->c_user->id, $directory_id);
        $stars = (int)$directory_user->directory->stars;
        $review = new Review();
        $options = array(
            'rank' => Review::generateRantQueryByStars($stars)
        );
        if($from_date && $to_date){
            $options['date_from'] = $from_date->format( Review::POSTEDFORMAT);
            $options['date_to'] = $to_date->format( Review::POSTEDFORMAT);
        }

        $details = $review->details($this->c_user->id, $this->profile->id, $directory_id, $options );
        /*$html = $this->load($directory_id);*/
        JsSettings::instance()->add(array(
			
            'directory' => array(
                'id' => $directory_id,
                'reviews_total' => $reviewsTotal,
                /*'nav' => array(
                    'prev' => $prev_link,
                    'next' => $next_link
                ),*/
                'link' =>  ($directory_user->directory->type == 'Google_Places' ? '' : $directory_user->link ),
                'stars' => (int)$directory_user->directory->stars,
                'details_url' => site_url('reviews/details'),
                'show_all_link' => site_url('reviews/all/'.$directory_id),
            ),
            'date_format' => array(
                'datepicker' => $this->js_datepicker_date_format,
                'string' => $this->js_string_date_format
            )
        ));


        CssJs::getInst()
            ->add_js(array(
                'libs/highcharts/highcharts.js'
            ))
            ->c_js();


        $this->template->set('from_date', $from_date);
        $this->template->set('to_date', $to_date);
		$this->template->set('type',$directory_user->directory->type);
        $this->template->set('reviewsTotal', $reviewsTotal);
        $this->template->set('reviews', $details['latest_reviews']);
        $this->template->set('rank', $details['rank_details']);
        $this->template->set('rate', $details['rank']);
        $this->template->set('stars', $stars);

        /*$this->template->set('html', $html);*/
		
		if($directory_user->directory->type == 'Foursquare'){
			$data = unserialize($directory_user->additional);
			$this->template->set('rate', round($data['rate'], 1));
			$this->template->set('visitors', round($data['visitors'], 1));
			$this->template->set('checkins', round($data['checkins'], 1));
		}
        $this->template->render();

    }

    public function all($directory_id){
        if(empty($directory_id) || !is_numeric($directory_id)) {
            show_404();
        }

        $directory_user = Directory_User::get_user_dir($this->c_user->id, $directory_id, $this->profile->id);

        if(!$directory_user->exists()) {
            return $this->link_to_config();
        }

        JsSettings::instance()->add(array(
            'stars' => (int)$directory_user->directory->stars,
        ));

        CssJs::getInst()->add_js(array(
            'libs/raty/jquery.raty.min.js',
            'libs/lodash.compat.js',
        ))->c_js();

        $reviews = new Review();
        $reviews->latest_reviews_paged($directory_id, $this->c_user->id, (int)$this->input->get('page'));

        sMenu::getInst()->set_pal('reviews/'.$directory_id);

        $this->template->set('reviews', $reviews);
        $this->template->set('directory_id', $directory_id);
		$this->template->set('type',$directory_user->directory->type);
        $this->template->render();

    }

    public function details(){
        $directory_id = $this->input->post('directory');
        $from_date = $this->input->post('from');
        $to_date = $this->input->post('to');


        $from_date = DateTime::createFromFormat($this->date_format, $from_date);
        $to_date = DateTime::createFromFormat($this->date_format, $to_date);
        $directory_user = Directory_User::get_user_dir($this->c_user->id, $directory_id, $this->profile->id);

        if(!$directory_user->exists() ){
            header('HTTP/1.0 400 Bad Request', true, 400);
            echo 'Bad Request';
            exit;
        }


        $start = (int)$directory_user->directory->stars;

        $review = new Review();

        $options = array(
            'rank' => Review::generateRantQueryByStars($start)
        );
        if($from_date && $to_date){
            $options['date_from'] = $from_date->format( Review::POSTEDFORMAT);
            $options['date_to'] = $to_date->format( Review::POSTEDFORMAT);
        }

        $details = $review->details($this->c_user->id, $directory_id, $options );

        $reviews = array();
        foreach($details['latest_reviews'] as $_review){
            $review_array = $_review->to_array(array('text', 'posted', 'author','rank'));
            $review_array['posted'] = date($this->date_format, $review_array['posted']);
            $reviews[] = $review_array;
        }
        $details['latest_reviews'] = $reviews;

        if(!$details['rank']){
            $details['rank'] = floor($start/2);
        }

        header('Content-type: application/json');
        echo json_encode($details);
        exit;
    }

    private function link_to_config() {
        $config_path = site_url('settings/directories');

        $this->template->set('settings_url', $config_path);

        $this->template->current_view = 'reviews/config_link';
        $this->template->render();
    }

    public function load($directory_id = 0)
    {
        $directory = (int)Arr::get($_GET, 'directory', $directory_id);
        $offset =  Arr::get($_GET, 'offset', 0);
        $from =  Arr::get($_GET, 'from', 0);
        $to = Arr::get($_GET, 'to', 0);
        $this->config->load('reviews');
        $limit = $this->config->config['reviews_limit'];
        $directoryUser = Directory_User::get_user_dir($this->c_user->id, $directory, $this->profile->id);
        $filters = array(
                         'directory_id' => $directory,
                         'user_id' => $this->c_user->id,
                        );
        if ($from) {
            $filters['posted >='] = strtotime($from);
        }
        if ($to) {
            $filters['posted <='] = strtotime($to);
        }
        $reviewModel = new Review();
        $reviews = $reviewModel->getByFilters($filters, $limit, $offset);
        $html = '';
        if ($reviews->exists()) {
            $type = $directoryUser->directory->type;
            foreach ($reviews as $review) {
                $review->posted = date($review::POSTEDFORMAT, $review->posted);
                $html .= $this->template->block('_content', '/reviews/blocks/review', array(
                                                                                            'review' => $review,
                                                                                            'type' => $type,
                                                                                            ));
            }
        }
        if ($this->isAjax()) {
            echo $html;
        } else {
            return $html;
        }


    }

}