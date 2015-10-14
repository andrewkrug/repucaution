<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(__FILE__).'/Socializer/Facebook.php';
require_once dirname(__FILE__).'/Socializer/Twitter.php';
require_once dirname(__FILE__).'/Socializer/Google.php';
require_once dirname(__FILE__).'/Socializer/Instagram.php';
require_once dirname(__FILE__).'/Socializer/Linkedin.php';
/**
 * Base class of Socializer module.
 * Declaration of required functions
 */
class Socializer
{
    const FBERRCODE = 5;
    const TWERRCODE = 6;

    public static function factory($social_name, $user_id = null, $token = null)
    {
        $social_media = 'Socializer_'.$social_name;
        return new $social_media($user_id, $token);
    }

   // public abstract function getLoginLink();

}