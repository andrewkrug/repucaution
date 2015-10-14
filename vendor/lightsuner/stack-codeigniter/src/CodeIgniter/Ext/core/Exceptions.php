<?php

use StackCI\CodeIgniter\Orig\Core\Exceptions;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CI_Exceptions extends Exceptions
{




	/**
	 * 404 Page Not Found Handler
	 *
	 * @access	private
	 * @param	string	the page
	 * @param 	bool	log error yes/no
	 * @return	string
	 */
	function show_404($page = '', $log_error = TRUE)
	{
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', '404 Page Not Found --> '.$page);
		}

		$this->show_error($heading, $message, 'error_404', 404);
	}

	// --------------------------------------------------------------------

    /**
     * General Error Page
     *
     * This function takes an error message as input
     * (either as a string or an array) and displays
     * it using the specified template.
     *
     * @access	private
     * @param	string	the heading
     * @param	string	the message
     * @param	string	the template name
     * @param 	int		the status code
     * @return	string
     */
    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        //set_status_header($status_code);

        $message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

        if (ob_get_level() > $this->ob_level + 1)
        {
            ob_end_flush();
        }
        ob_start();
        include(APPPATH.'errors/'.$template.'.php');
        $buffer = ob_get_contents();
        ob_end_clean();

        throw new HttpException($status_code, $buffer);
    }

}
