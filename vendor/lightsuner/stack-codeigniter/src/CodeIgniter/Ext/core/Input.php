<?php

use StackCI\CodeIgniter\Orig\Core\Input;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class CI_Input extends Input
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Set Request
     *
     * @param Request $request
     * @return $this;
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Response
     *
     * @param Response $response
     * @return $this;
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the GET array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
    public function get($index = null, $xss_clean = false)
	{
		// Check if a field has been provided
		if ($index === null && $this->request->query->count())
		{
			return $this->request->query->all();
		}

        $val = $this->request->query->get($index, false);

        if ($xss_clean) {
            $val = $this->security->xss_clean($val);
        }

		return $val;
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the POST array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
    public function post($index = null, $xss_clean = false)
	{
		// Check if a field has been provided
		if ($index === null && $this->request->request->count())
		{
			return $this->request->request->all();
		}

        $val = $this->request->request->get($index, false);

        if ($xss_clean) {
            $val = $this->security->xss_clean($val);
        }

        return $val;
	}


	// --------------------------------------------------------------------

	/**
	* Fetch an item from either the GET array or the POST
	*
	* @access	public
	* @param	string	The index key
	* @param	bool	XSS cleaning
	* @return	string
	*/
	function get_post($index = '', $xss_clean = false)
	{
		if (!$this->request->request->has($index))
		{
			return $this->get($index, $xss_clean);
		} else {
			return $this->post($index, $xss_clean);
		}
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the COOKIE array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function cookie($index = '', $xss_clean = false)
	{
	    $val = $this->request->cookies->get($index);

        if ($xss_clean) {
            $val = $this->security->xss_clean($val);
        }

        return $val;
	}

	// ------------------------------------------------------------------------

	/**
	* Set cookie
	*
	* Accepts six parameter, or you can submit an associative
	* array in the first parameter containing all the values.
	*
	* @access	public
	* @param	mixed
	* @param	string	the value of the cookie
	* @param	string	the number of seconds until expiration
	* @param	string	the cookie domain.  Usually:  .yourdomain.com
	* @param	string	the cookie path
	* @param	string	the cookie prefix
	* @param	bool	true makes the cookie secure
	* @return	void
	*/
	function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = false)
	{
        if (is_array($name)) {
            extract($name);
        }

        $coockie = new Cookie($prefix.$name, $value, $expire, $path, $domain, $secure, false);

        $this->response->headers->setCookie($coockie);

	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the SERVER array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function server($index = '', $xss_clean = false)
	{
        $val = $this->request->server->get($index, false);

        if ($xss_clean) {
            $val = $this->security->xss_clean($val);
        }

        return $val;
	}

	// --------------------------------------------------------------------

	/**
	* Fetch the IP Address
	*
	* @return	string
	*/
	public function ip_address()
	{
		return $this->request->getClientIp();
	}

	// --------------------------------------------------------------------



	/**
	* User Agent
	*
	* @access	public
	* @return	string
	*/
	function user_agent()
	{
		if ($this->user_agent !== false)
		{
			return $this->user_agent;
		}

		$this->user_agent = $this->request->server->get('HTTP_USER_AGENT', false);

		return $this->user_agent;
	}

	// --------------------------------------------------------------------



	/**
	 * Request Headers
	 *
	 * In Apache, you can simply call apache_request_headers(), however for
	 * people running other webservers the function is undefined.
	 *
	 * @param	bool XSS cleaning
	 *
	 * @return array
	 */
	public function request_headers($xss_clean = false)
	{
        $this->headers = $this->request->headers->all();

		return $this->headers;
	}

	// --------------------------------------------------------------------

	/**
	 * Is ajax Request?
	 *
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header
	 *
	 * @return 	boolean
	 */
	public function is_ajax_request()
	{
		return $this->request->isXmlHttpRequest();
	}

}

/* End of file Input.php */
/* Location: ./system/core/Input.php */