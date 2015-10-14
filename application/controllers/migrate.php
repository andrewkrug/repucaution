<?php

class Migrate extends MX_Controller {

    public function __construct() {
        parent::__construct();

        // $this->input->is_cli_request() 
            // or exit("Execute via command line: php index.php migrate");

        $this->load->library('migration');
    }

    public function index($version = NULL) {

        if ( ! is_null($version)) {

            if ( ! $this->migration->version($version) ) {
                show_error($this->migration->error_string());
            }

        } else {

            if ( ! $this->migration->latest() ) {
                show_error($this->migration->error_string());
            }

        }
                
    }
    //down to version and up to latest
    public function rebuild($version)
    {
        if ( ! $this->migration->version($version) ) {
            show_error($this->migration->error_string());
        } else {
            if ( ! $this->migration->latest() ) {
                show_error($this->migration->error_string());
            }
        }
    }

}