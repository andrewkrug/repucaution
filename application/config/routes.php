<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "dashboard";

$route['admin'] = 'admin/admin_dashboard';
$route['manager'] = 'manager/manager_route';
$route['settings'] = "settings/personal";
$route['reviews/(:num)'] = "reviews/index/$1";

$route['social'] = 'social/create/redirect';
$route['social/create'] = 'social/create/update';
$route['social/mentions'] = 'social/mentions/social/facebook';
$route['social/mentions/facebook'] = 'social/mentions/social/facebook';
$route['social/mentions/twitter'] = 'social/mentions/social/twitter';
$route['social/mentions/google'] = 'social/mentions/social/google';
$route['social/fbbuilder'] = 'fbbuilder/bootstrap';
$route['webradar'] = 'social/webradar';
$route['webradar/twitter'] = 'social/webradar/twitter';
$route['webradar/facebook'] = 'social/webradar/facebook';
$route['webradar/google'] = 'social/webradar/google';
$route['webradar/instagram'] = 'social/webradar/instagram';
$route['webradar/all']='social/webradar';
$route['webradar/influencers']='influencers';
$route['reviews'] ="dashboard";
$route['images/(:any)'] = 'images/display/$1';
$route['404_override'] = '';

/* Location: ./application/config/routes.php */