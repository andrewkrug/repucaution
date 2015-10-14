<?php

class Ga_use {

    protected 
                    $ci,
                    $config,

                    $queries = array(),

                    $default_dates = array(),

                    $client = NULL,
                    $service = NULL;

    public function __construct($params = array()) {

        $this->ci = & get_instance();

        $this->ci->load->config('site_config', TRUE);
        // $this->config = $this->ci->config->item('google_app', 'site_config');
        $this->config = Api_key::build_config(
            'google', 
            $this->ci->config->item('google_app', 'site_config')
        );
        $this->config['client_secret'] = $this->config['secret'];

        $this->ci->load->config('ga_queries', TRUE);
        $this->queries = $this->ci->config->item('queries', 'ga_queries');

        $this->ci->load->library('google_analytics/ga_client');

        if (isset($params['token'])) {
            $this->init($params['token']);
        }    

        $this->default_dates = array(
            'from' => '-1 month',
            'to' => 'today',
        );

    }

    public function init($token) {
        try {
            $this->client = $this->ci->ga_client->client_init($this->config);
            $this->client->refreshToken($token);
            $this->service = $this->ci->load->library('google_analytics/ga_service', array('client' => $this->client));
        } catch(Google_AuthException $e) {
            throw new Exception('Authorization error. Please try to reconnect your Google Analytics account.');
        }
        return $this;
    }

    public function default_dates($dates) {
        if ( is_array($dates) && ! empty($dates)) {
            if (isset($dates['from'])) {
                $this->default_dates['from'] = $dates['from'];
            }
            if (isset($dates['to'])) {
                $this->default_dates['to'] = $dates['to'];
            }
        }
        return $this;
    }

    public function queries_types() {
        return array_keys($this->queries);
    }

    public function rows($type, $prop, $profile_id, $from_str, $to_str) {

        try {

            if ( ! isset($this->queries[$type])) {
                throw new Exception('Undefined type: ' . $type);
            }

            if ( ! isset($this->queries[$type][$prop])) {
                throw new Exception('Undefined property: ' . $type . ' -> ' . $prop);
            }

            $from_s = strtotime($from_str);
            $to_s = strtotime($to_str);

            if ( ! $from_s) {
                if ( ! isset($this->default_dates['from'])) {
                    throw new Exception('Invalid "from" date: ' . $from_str);
                }
                $from_s = strtotime($this->default_dates['from']);
            }

            if ( ! $to_s) {
                if ( ! isset($this->default_dates['to'])) {
                    throw new Exception('Invalid "to" date: ' . $to_str);
                }
                $to_s = strtotime($this->default_dates['to']);
            }

            // convert to GA-friendly format
            $from = date('Y-m-d', min($from_s, $to_s));
            $to = date('Y-m-d', max($from_s, $to_s));


            $query = $this->queries[$type];

            $query_prop = $query[$prop];


            $data = $this->service->data_ga->get(
                'ga:' . $profile_id, 
                $from,
                $to,
                key($query_prop), // dimension
                current($query_prop) // metrics
            );            

            $rows = $data->getRows();

            return array(
                'result' => $rows,
                'headers' => isset($query['headers']) ? $query['headers'] : NULL,
                'caption' => isset($query['caption']) ? $query['caption'] : NULL,
                'values' => isset($query['values']) ? $query['values'] : NULL,
            );

        } catch (Google_ServiceException $e) {

            //var_dump($e);
            
            
            $parts = explode(')', $e->getMessage());
            $error_message = (is_array($parts) && $parts[ count($parts) - 1]) ? $parts[ count($parts) - 1] : $e->getMessage();
            log_message('TASK_ERROR',__FUNCTION__ .' > '.$error_message);
            throw new Exception($error_message);
                
        } catch (Exception $e) {

            throw $e;

        }

    }

}