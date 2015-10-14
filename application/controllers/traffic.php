<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Traffic extends MY_Controller {

    protected $website_part = 'dashboard';

    // Access_token model "google" for current user
    protected $access_token;

    // default dates for datepickers
    protected $dates;
    // default date format in traffic section
    protected $date_format = 'M j, Y';

    public function __construct() {
        parent::__construct($this->website_part);

        $this->lang->load('analytics', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('analytics', $this->language)
        ]);

        $this->access_token = Access_token::getByTypeAndUserIdAndProfileId(
            'googlea',
            $this->c_user->id,
            $this->profile->id
        );

        $this->load->library('google_analytics/ga_use');

        // default date range
        $this->dates = array(
            'from' => date($this->date_format, strtotime('-30 days')),
            'to' => date($this->date_format),
        );
    }

    /**
     * Default page - Website traffic with overall statistics and graph
     */
    public function index() {
        $this->_force_configure();

        JsSettings::instance()->add(array(
            'analytics' => array(
                'dates' => $this->dates,
                'date_format' => $this->date_format,
                'default_traffic_type' => 'web',
                'traffic_types' => $this->ga_use->queries_types(), 
                'request_url' => site_url('traffic/data'),
            ),
        ));
        CssJs::getInst()->add_js('www.google.com/jsapi', 'external', 'footer');
        CssJs::getInst()->add_js(array(
            'libs/handlebar.js',
            'libs/handlebars_helpers.js',
            'libs/highcharts/highcharts.js',
            /*'date.js'*/
        ));

        CssJs::getInst()->c_js();
        $this->template->set('dates', $this->dates);
        $this->template->render();
    }

    /**
     * Get data from GA by ajax
     *
     * @param        $type
     * @param string $prop
     */
    public function data($type, $prop = 'table') {
        if ($this->input->is_ajax_request() 
            && $this->access_token->connected()
        ) {

            // get dates from form
            $from_str = $this->input->post('from');
            $to_str = $this->input->post('to');

            try {

                $data = $this->ga_use
                    ->init($this->access_token->token2)
                    ->default_dates($this->dates)
                    ->rows($type, $prop, $this->access_token->instance_id, $from_str, $to_str);

                $result['success'] = TRUE;
                $result = Arr::merge($result, $data);
                
            } catch (Exception $e) {

                $result['success'] = FALSE;
                $result['error'] = $e->getMessage();

            }

            $result['dates'] = array(
                'from' => date($this->date_format, strtotime($from_str)),
                'to' => date($this->date_format, strtotime($to_str)),
            );

            exit( json_encode($result) );
        }
    }

    /**
     * Check if user has connected GA account and profile to get data from
     */
    protected function _force_configure() {
        if ( ! $this->access_token->connected()) {
            redirect('traffic/configure');
        }
    }

    /**
     * If user has no connected GA account or profile to get data from
     * show this page
     */
    public function configure() {
        if ($this->access_token->connected()) {
            redirect('traffic');
        }
        $this->template->set('access_token', $this->access_token);
        $this->template->render();
    }

}