<?php
/**
 * User: alkuk
 * Date: 03.04.14
 * Time: 15:00
 */

use StackCI\Application as CIApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use LanguageSelector\I18nSelector;

require_once __DIR__.'/vendor/autoload.php';

$session = new Session();
$session->start();

$request = Request::createFromGlobals();
$request->setSession($session);


//$ciApp = new CIApplication(__DIR__, 'development');
$ciApp = new CIApplication(__DIR__, 'production');

$ciApp->beforeKernelLoad(function(){
        define('PUBPATH', FCPATH."public");

        if( ! ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }

        require_once(APPPATH.'third_party/datamapper/bootstrap.php');
    })
    ->init();

$stackBuilder = new Stack\Builder();
//$languageSelector = new I18nSelector($ciApp);
//$stackBuilder->push($languageSelector);
$app = $stackBuilder->resolve($ciApp);


$response = $app->handle($request);

$response->send();

$app->terminate($request, $response);