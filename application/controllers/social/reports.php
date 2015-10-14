<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    protected $website_part = 'dashboard';

    // default dates for datepickers
    protected $dates;
    // default date format in reports section
    protected $date_format = 'Y-m-d';


    public function __construct() {
        parent::__construct();

        $this->lang->load('social_reports', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('social_reports', $this->language)
        ]);
        $this->dates = array(
            'from' => date($this->date_format, strtotime('-1 month')),
            'to' => date($this->date_format),
        );
    }

    /**
     * Render Reports Page
     *
     * @access public
     * @return void
     */
    public function index() {
        JsSettings::instance()->add(array(
            'reports' => array(
                'dates' => $this->dates,
                'date_format' => $this->date_format,
            ),
        ));

        CssJs::getInst()->add_js(array(
            'libs/highcharts/highcharts.js'
        ));

        CssJs::getInst()->c_js('social/reports', 'index');
        CssJs::getInst()->add_js('www.google.com/jsapi', 'external', 'header');

        $this->template->render();
    }

    /**
     * Collect data for current user
     * For selected period (default - for 1 month)
     *
     * @access public
     * @return void
     */
    public function get_chart_data() {
        $social_values = Social_value::inst();

        $post = $this->input->post();

        $post['from'] = isset($post['from']) ? date($this->date_format, strtotime($post['from'])) : $this->dates['from'];
        $post['to'] = isset($post['to']) ? date($this->date_format, strtotime($post['to'])) : $this->dates['to'];

        $social_values->set_values($this->c_user->id, $this->profile->id, $post);

        $data = $social_values->get_data();

        echo json_encode($data);
    }



}