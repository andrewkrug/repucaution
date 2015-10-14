<?php

class Gls {

    // goolge api returns only 60, this value is to prevent infinite loop
    const MAX_RESULTS = 60; 

    protected 
                    // CI instance
                    $ci,
                    // lib config
                    $config,


                    // fields required for basic query
                    $required = array('endpoint', 'key', 'query', 'sensor'),
                    // fields excluded from query
                    $request_exclude = array('endpoint', 'max_results', 'no_results_message'),


                    // last error string                    
                    $error = NULL,


                    // number of total results for query (is set after the first request)
                    // $total_results = NULL,

                    // number of current results (is updated after each request)
                    // google returns only MAX_RESULTS results per request
                    $current_results = NULL,

                    // is overwritten by config "max_results"
                    $max_results = self::MAX_RESULTS,

                    $next_page_token = NULL,

                    // already parsed data after all requests completed
                    $data = NULL,

                    $dirty = NULL,

                    $urls = NULL,

                    $codes = array(
                        'OK',
                        'ZERO_RESULTS',
                        'OVER_QUERY_LIMIT',
                        'REQUEST_DENIED',
                        'INVALID_REQUEST',
                    )
                    ;


    /**
     * @param $params (array) - query params on lib init
     */
    public function __construct($params = array()) {
        $this->ci = &get_instance();
        $this->config = $this->ci->load->config('gls', TRUE);
        $this->set($params);
    }

    /**
     * (re) set query params
     * 
     * @param $params (array) - query params
     * @return gcs - for chaining
     */
    public function set($params = NULL) {
        if ( is_array($params) && ! empty($params)) {
            $this->config = array_merge($this->config, $params);
        }
        return $this;
    }

    /**
     * Get config value
     * 
     * @param $key (string)
     */
    public function get($key) {
        return isset($this->config[$key])
            ? $this->config[$key]
            : NULL;
    }

    /**
     * If last request was successful
     * 
     * @return bool
     */
    public function success() {
        return empty($this->error);
    }

    /**
     * Last error string
     * 
     * @return string
     */
    public function error() {
        return $this->error;    
    }


    public function max_results() {
        return isset($this->config['max_results'])
            ? $this->config['max_results']
            : self::MAX_RESULTS;
    }


    /**
     * Return last request rank for domain
     * Domain is cleared before rank search
     *
     * @param      $location_id
     * @param bool $all (bool) - FALSE - return the highest rank; TRUE - return array of ranks
     *
     * @return  NULL - if last request has no data
     *          0 - if no rank for domain
     *          int - if rank for domain exists, domain rank
     *          array - if $all is set to TRUE, array of all positions will be returned
     *          if no ranks - return empty array
     * @throws Exception
     */
    
    public function location_rank($location_id, $all = FALSE) {
        if ( ! $this->data) {
            return NULL;
        }

        $ranks = array();

        if ( ! $location_id) {
            throw new Exception('empty location id');
        }

        foreach($this->data as $key => $item) {
            $pos = strpos(strtolower($item['id']), strtolower($location_id));
            if ($pos !== FALSE) {
                $ranks[] = $key + 1;
            }
        }

        $max_results = $this->max_results();

        return $all
            ? $ranks
            : ( empty($ranks) ? intval($max_results) : min($ranks) );
    }


    /**
     * Get data from google custom search
     * 
     * 
     * @param $query (string) - query string (keyword)
     * @param $rows (int) - number or rows to get collected
     * | google returns only ROWS_PER_REQUEST results per request so if the $rows value is larger than ROWS_PER_REQUEST
     * | multiple requests will be produced
     * | data for all requests will be collected as one collection (one array)
     * @return gcs - chaining
     */
    public function request($query = '') {
        // clear values for new request
        
        $this->data = NULL;
        $this->dirty = NULL;
        $this->error = NULL;
        $this->urls = NULL;
        $this->next_page_token = NULL;
        $this->current_results = NULL;

        try {
            // check for empty query string
            $query = trim($query);
            if ( ! $query) {
                throw new Exception('query string is empty');
            }
            // set query
            $params = array(
                'query' => $query,
            );
            $this->set($params);

            // validate required fields
            foreach($this->required as $key) {
                if ( ! isset($this->config[$key]) ) {
                    throw new Exception('"' . $key . '" field is required');
                }
            }

            $query_params = array_flip(array_diff(array_flip($this->config), $this->request_exclude));
            $url = $this->config['endpoint'] . http_build_query($query_params);

            $response = $this->_curl($url);

            
            // run loop for all pages
            do {

                if ($this->current_results > self::MAX_RESULTS) {
                    throw new Exception('infinite request loop');
                }

                if ($this->next_page_token) {
                    $this->set(array(
                        'pagetoken' => $this->next_page_token,
                    ));
                    sleep(5); // sleep while next page is being prepared by google
                }

                // create url for request 
                $query_params = array_flip(array_diff(array_flip($this->config), $this->request_exclude));
                $url = $this->config['endpoint'] . http_build_query($query_params);

                $this->urls[] = $url;

                // perform request to google api
                $response = $this->_curl($url);

                // parse response, check if request has results
                $has_items = $this->_parse($response);

                if ( ! $has_items) {
                    // check if any data collected
                    if ( ! $this->data) {
                        throw new Exception('no results');
                    }
                    break; // if any data collected - request was successfull, break and continue
                }

            } while((bool) $this->next_page_token);

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        return $this;
    }

    /**
     * Parse response from request
     * 
     * @param $response (string) - hopefully not empty and in json format
     * @return bool - if has any items
     * @throws Exception
     */
    protected function _parse($response) {

        if ( ! $response) {
            throw new Exception('empty response');
        }

        $json = json_decode($response, TRUE);

        if ( ! $json) {
            throw new Exception('invalid response json');
        }

        if ( ! isset($json['status']) OR ! in_array($json['status'], $this->codes)) {
            throw new Exception('invalid response status');
        }

        switch($json['status']) {

            case 'ZERO_RESULTS':
                return FALSE;
                break;

            case 'OVER_QUERY_LIMIT':
            case 'REQUEST_DENIED':
            case 'INVALID_REQUEST':
                throw new Exception('google error: ' . $json['status']);
                break;

            default:
                // 'OK' STATUS

        }

        $this->next_page_token = isset($json['next_page_token']) ? $json['next_page_token'] : NULL;

        // get results
        $items = $json['results'];

        // stop requests flow if no results
        if ( empty($items) ) {
            return FALSE;
        }

        $this->current_results = intval($this->current_results) + count($items);

        // add items to request data array
        foreach($items as $item) {

            // get only useful information
            $data = array(
                'id' => $item['id'],
                'formatted_address' => $item['formatted_address'],
            );

            $this->data[] = $data;
            $this->dirty[] = $item;
        }

        return TRUE;
    }

    /**
     * Return all results as is
     * 
     * @return array | NULL - if request is not completed
     */
    public function dirty() {
        return $this->dirty;
    }

    /**
     * Return url used to do request
     * 
     * @return array | NULL - if request is not completed
     */
    public function urls() {
        return $this->urls;
    }

    /**
     * Return autocomplete results for term
     * 
     * @param $term (string)
     * @return array | bool
     */
    public function autocomplete($term) {

        $term = trim($term);

        if (empty($term)) {
            return false;
        }

        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';

        $args = array(
            'sensor' => 'false',
            'language' => 'en',
            'input' => $term,
            'key' => $this->config['key'],
        );

        $url .= http_build_query($args);

        $response = $this->_curl($url);
        $response = json_decode($response, TRUE);

        return $response;
    }

  
    /**
     * Get site full domain from url (with all subdomains)
     * 
     * @param $url (string) - url
     * @return string - clean site full domain
     */
    protected function _clear_domain($url) {
        $url = preg_replace("/^((http|https):\/\/)*(www.)*/is", '', $url); 
        $url = trim($url, '/');
        $pos = strpos($url, '/');
        if ( $pos !== FALSE) {
            $url = substr($url, 0, $pos);
        }
        $url = trim($url, '/');
        return $url;
    }

    /**
     * CURL url
     * 
     * @return string
     * @throws Exception
     */
    protected function _curl($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);      
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 20); 
        $response = curl_exec($curl);
        if ($error_no = curl_errno($curl)) {
            $error_string = curl_error($curl);
            curl_close($curl);
            throw new Exception ('curl error ' . $error_no . ' : ' . $error_string . '.');
        }
        curl_close($curl);
        return $response;
    }

}
