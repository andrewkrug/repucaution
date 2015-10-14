<?php
/**
 * Google custom search library
 * 
 * response example
 * https://friendpaste.com/4h034YduB7LlPJZqCRAZ2v
 * 
 * Usage example:
 *     
 *      $gcs = $this->load->library('gcs');
 *      if ($gcs->request('cats', 50)->success()) {
 *           echo 'Domain rank: ' . $gcs->domain_rank('youtube.com');
 *       } else {
 *           echo 'Gcs error: ' . $gcs->error();
 *       }
 */
class Gcs {

    const ROWS_PER_REQUEST = 10;

    protected 
                    // CI instance
                    $ci,
                    // lib config
                    $config,


                    // fields required for basic query
                    $required = array('endpoint', 'key', 'cx', 'q'),
                    // fields excluded from query
                    $request_exclude = array('endpoint'),


                    // last error string                    
                    $error = NULL,


                    // number of total results for query (is set after the first request)
                    $total_results = NULL,

                    // number of current results (is updated after each request)
                    // google returns only ROWS_PER_REQUEST results per request
                    $current_results = NULL,

                    // stores the number of requested rows
                    // changes in each request
                    // used when no rank for domain found in results as default value
                    $rows_requested = ROWS_PER_REQUEST,

                    // already parsed data after all requests completed
                    $data = NULL,

                    // collect all requests infomation such as
                    /*
                        "searchTime": 0.15943,                  // sum
                        "formattedSearchTime": "0.16",      // sum
                        "totalResults": "348",
                        "formattedTotalResults": "348"
                    */
                    $search_info = NULL;


    /**
     * @param $params (array) - query params on lib init
     */
    public function __construct($params) {
        $this->ci = &get_instance();
        $this->config = $this->ci->load->config('gcs', TRUE);
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

    /**
     * Return last request search info
     * 
     * @param $key (string) - return special param
     * @return mixed, null
     */
    public function search_info($key = NULL) {
        if ( ! $this->data) {
            return NULL;
        }
        return $key 
            ? (isset($this->search_info[$key]) ? $this->search_info[$key] : NULL)
            : $this->search_info;
    }

    /**
     * Return last request rank for domain
     * Domain is cleared before rank search
     * 
     * @param $domain (string) - domain
     * @param $all (bool) - FALSE - return the highest rank; TRUE - return array of ranks
     * @return  NULL - if last request has no data
     *                 0 - if no rank for domain
     *                 int - if rank for domain exists, domain rank
     *                 array - if $all is set to TRUE, array of all positions will be returned
     *                            if no ranks - return empty array
     */
    public function domain_rank($domain, $all = FALSE) {
        if ( ! $this->data) {
            return NULL;
        }

        $ranks = array();

        $domain = $this->_clear_domain($domain);

        foreach($this->data as $key => $item) {
            $pos = strpos(strtolower($item['displayLink']), strtolower($domain));
            if ($pos !== FALSE) {
                $ranks[] = $key + 1;
            }
        }

        return $all
            ? $ranks
            : ( empty($ranks) ? intval($this->rows_requested) : min($ranks) );
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
    public function request($query = '', $rows = self::ROWS_PER_REQUEST) {
        // clear values for new request
        $this->data = NULL;
        $this->error = NULL;
        $this->total_results = NULL;
        $this->current_results = NULL;
        $this->rows_requested = $rows;

        $prevent_infinite_loop = 0;

        try {
            // check for empty query string
            $query = trim($query);
            if ( ! $query) {
                throw new Exception('query string is empty');
            }
            // set query
            $params = array(
                'q' => $query,
            );
            $this->set($params);

            // validate required fields
            foreach($this->required as $key) {
                if ( ! isset($this->config[$key]) ) {
                    throw new Exception('"' . $key . '" field is required');
                }
            }

            // run loop for required number of rows
            while(intval($this->current_results) < $rows) {

                // check if google total results count is lower than required results count
                // at least one request will be already completed
                if ( ! is_null($this->total_results) && $this->total_results < $rows) {
                    break;
                }

                if ($prevent_infinite_loop > $rows) {
                    throw new Exception('infinite request loop');
                }

                // set initial results offset
                $this->set(array(
                    'startIndex' => intval($this->current_results) + 1,
                ));

                // create url for request 
                $query_params = array_flip(array_diff(array_flip($this->config), $this->request_exclude));
                $url = $this->config['endpoint'] . http_build_query($query_params);


                echo '<pre>';

                var_dump($url);

                // perform request to google api
                $response = $this->_curl($url);
                echo '<hr>';

                var_dump($response);

                // parse response, check if request has results
                $has_items = $this->_parse($response);
                if ( ! $has_items) {
                    // check if any data collected
                    if ( ! $this->data) {
                        throw new Exception('no results');
                    }
                    break; // if any data collected - request was successfull, break and continue
                }

                $prevent_infinite_loop += self::ROWS_PER_REQUEST;

            }

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

        if (isset($json['error']['message'], $json['error']['code'])) {

            throw new Exception('google error: ' . $json['error']['message'] . ' [ code: ' . $json['error']['code'] . ' ] ');
            
        } else {

            // get request info, especially number of rows returned and number of total rows for query
            $request = $json['queries']['request'];
            $request = current($request);
            // update values for correct loop
            $this->total_results = $request['totalResults'];
            $this->current_results = intval($this->current_results) + $request['count'];
            // current results must be the same as google's
            if ($this->current_results + 1 == $request['startIndex'] + $request['count']) {
                // ??
            }
            
            // collect request search info
            $search_info = $json['searchInformation'];
            if ($this->search_info) {
                $this->search_info['searchTime'] += $search_info['searchTime'];
                $this->search_info['formattedSearchTime'] += $search_info['formattedSearchTime'];
            } else {
                $this->search_info = $search_info;
            }

            // get results
            $items = $json['items'];

            // stop requests flow if no results
            if ( empty($items) ) {
                return FALSE;
            }

            // add items to request data array
            foreach($items as $item) {

                // get only useful information
                $data = array(
                    'link' => $item['link'],
                    'displayLink' => $item['displayLink'],
                    'formattedUrl' => $item['formattedUrl'],
                    'htmlFormattedUrl' => $item['htmlFormattedUrl'],
                );

                $this->data[] = $data;
            }

        }

        return TRUE;
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
        // $url = 'https://gist.github.com/micrddrgn/1578e1906fc3502ec40b/raw/01bb10b19249eaa1cf27466066eed152d9f11b96/gistfile1.txt';
        // $url = 'https://friendpaste.com/4h034YduB7LlPJZqCRAZ2v/raw?rev=323965646563';
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
