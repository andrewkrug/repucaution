<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class HTML {

    /**
     * Convert special characters to HTML entities. All untrusted content
     * should be passed through this method to prevent XSS injections.
     *
     *     echo HTML::chars($username);
     *
     * @param   string  $value          string to convert
     * @param   boolean $double_encode  encode existing entities
     * @return  string
     */
    public static function chars($value, $double_encode = TRUE, $charset = 'utf-8')
    {
        return htmlspecialchars( (string) $value, ENT_QUOTES, $charset, $double_encode);
    }

    /**
     * Convert all applicable characters to HTML entities. All characters
     * that cannot be represented in HTML with the current character set
     * will be converted to entities.
     *
     *     echo HTML::entities($username);
     *
     * @param   string  $value          string to convert
     * @param   boolean $double_encode  encode existing entities
     * @return  string
     */
    public static function entities($value, $double_encode = TRUE, $charset = 'utf-8')
    {
        return htmlentities( (string) $value, ENT_QUOTES, $charset, $double_encode);
    }

    /**
     * Recursively clear array values and keys (if needed) with HTML::chars function
     * 
     *      $array = HTML::chars_arr($array);
     * 
     * @param $array (array) - may be nested
     * @param $keys (bool) - if keys should be protected too
     */
    public static function chars_arr($array, $keys = FALSE) {
        if ( !is_array($array)) return;
        $result = array();
        foreach ($array as $key => $value) {
            if ($keys) {
                $nkey = HTML::chars($key);
                if ($key != $nkey) {
                    unset($array[$key]);
                    $key = $nkey;
                }
            }
            $result[$key] = is_array($value) ? HTML::chars_arr($value, TRUE) : HTML::chars($value);
        }
        return $result;
    }

}