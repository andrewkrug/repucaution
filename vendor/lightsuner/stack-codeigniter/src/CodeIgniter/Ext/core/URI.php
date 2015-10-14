<?php

use StackCI\CodeIgniter\Orig\Core\URI;
use Symfony\Component\HttpFoundation\Request;

class CI_URI extends URI
{


	/**
	 * Get the URI String
	 *
	 * @access	private
	 * @return	string
	 */
	function _fetch_uri_string(Request $request)
	{
		if (strtoupper($this->config->item('uri_protocol')) == 'AUTO')
		{
			// Is the request coming from the command line?
			if (php_sapi_name() == 'cli' or defined('STDIN'))
			{
				$this->_set_uri_string($this->_parse_cli_args($request));
				return;
			}

			// Let's try the REQUEST_URI first, this will work in most situations
			if ($uri = $this->_detect_uri($request))
			{
				$this->_set_uri_string($uri);
				return;
			}

			// Is there a PATH_INFO variable?
			// Note: some servers seem to have trouble with getenv() so we'll test it two ways

			$path = $request->server->get('PATH_INFO', @getenv('PATH_INFO'));
			if (trim($path, '/') != '' && $path != "/".SELF)
			{
				$this->_set_uri_string($path);
				return;
			}
			// No PATH_INFO?... What about QUERY_STRING?

            $path = $request->server->get('QUERY_STRING', @getenv('QUERY_STRING'));
			if (trim($path, '/') != '')
			{
				$this->_set_uri_string($path);
				return;
			}

			// As a last ditch effort lets try using the $_GET array
			if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '')
			{
				$this->_set_uri_string(key($_GET));
				return;
			}

			// We've exhausted all our options...
			$this->uri_string = '';
			return;
		}

		$uri = strtoupper($this->config->item('uri_protocol'));

		if ($uri == 'REQUEST_URI')
		{
			$this->_set_uri_string($this->_detect_uri($request));
			return;
		}
		elseif ($uri == 'CLI')
		{
			$this->_set_uri_string($this->_parse_cli_args($request));
			return;
		}

		$path = (isset($_SERVER[$uri])) ? $_SERVER[$uri] : @getenv($uri);
		$this->_set_uri_string($path);
	}


	/**
	 * Detects the URI
	 *
	 * This function will detect the URI automatically and fix the query string
	 * if necessary.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _detect_uri(Request $request)
	{

        $uri = $request->getPathInfo();

        if (empty($uri)) {
            $uri = '/';
        }

        return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse cli arguments
	 *
	 * Take each command line argument and assume it is a URI segment.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _parse_cli_args(Request $request)
	{
		// @TODO
        $args = array_slice($_SERVER['argv'], 1);

		return $args ? '/' . implode('/', $args) : '';
	}

	// --------------------------------------------------------------------



}
