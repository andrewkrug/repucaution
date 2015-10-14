<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function lang($line, $vars = array()) {
    $CI =& get_instance();
    $line = $CI->lang->line($line);

    if ($vars)
    {
        $line = vsprintf($line, (array) $vars);
    }

    return $line;
}

/* End of file MY_language_helper.php */
/* Location: ./application/helpers/MY_language_helper */