<?php

/**
 * Class registry
 *
 * This function acts as a singleton.  If the requested class does not
 * exist it is instantiated and set to a static variable.  If it has
 * previously been instantiated the variable is returned.
 *
 * @access    public
 *
 * @param    string    the class name being requested
 * @param    string    the directory where the class should be found
 * @param    string    the class name prefix
 *
 * @return    object
 */

if (!function_exists('load_class')) {
    function &load_class($class, $directory = 'libraries', $prefix = 'CI_')
    {
        static $_classes = array();

        // Does the class exist?  If so, we're done...
        if (isset($_classes[$class])) {
            return $_classes[$class];
        }

        $name = false;

        // Look for the class first in the local application/libraries folder
        // then in the native system/libraries folder
        foreach (array(
                     APPPATH,
                     STACKCIEXTPATH,
                     BASEPATH
                 ) as $path) {
            if (file_exists($path . $directory . '/' . $class . '.php')) {
                $name = $prefix . $class;

                if (class_exists($name) === false) {
                    require($path . $directory . '/' . $class . '.php');
                }

                break;
            }
        }

        // Is the request a class extension?  If so we load it too
        if (file_exists(APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . '.php')) {
            $name = config_item('subclass_prefix') . $class;

            if (class_exists($name) === false) {
                require(APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . '.php');
            }
        }

        // Did we find the class?
        if ($name === false) {

            throw new Exception(sprintf('Unable to locate the specified class: %s.php', $class));
        }

        // Keep track of what we just loaded
        is_loaded($class);

        $_classes[$class] = new $name();

        return $_classes[$class];
    }
}


/**
 * Error Handler
 *
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * in application/errors/errors.php
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access    public
 * @return    void
 */
if (!function_exists('show_error')) {
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        $_error =& load_class('Exceptions', 'core');
        $_error->show_error($heading, $message, 'error_general', $status_code);

    }
}

// ------------------------------------------------------------------------

/**
 * 404 Page Handler
 *
 * This function is similar to the show_error() function above
 * However, instead of the standard error template it displays
 * 404 errors.
 *
 * @access    public
 * @return    void
 */
if (!function_exists('show_404')) {
    function show_404($page = '', $log_error = true)
    {
        $_error =& load_class('Exceptions', 'core');
        $_error->show_404($page, $log_error);
    }
}

// ------------------------------------------------------------------------

