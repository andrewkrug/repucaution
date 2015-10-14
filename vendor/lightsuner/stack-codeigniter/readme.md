# Codeigniter stackPHP(HttpKernelInterface) Implementation

[![Latest Stable Version](https://poser.pugx.org/lightsuner/stack-codeigniter/v/stable.png)](https://packagist.org/packages/lightsuner/stack-codeigniter)
[![Total Downloads](https://poser.pugx.org/lightsuner/stack-codeigniter/downloads.png)](https://packagist.org/packages/lightsuner/stack-codeigniter)

[StackPHP](http://stackphp.com)


##About
This is a wrapper for Codeigniter. It implements [HttpKernelInterface](https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpKernel/HttpKernelInterface.php)
that allows you to use Codeigniter with other frameworks using StackPHP.

This implementation has got much dirty code. I would be glad if you help clean it.


##Install

Add stack-codeigniter in your composer.json:
```json
{
    "require": {
        "lightsuner/stack-codeigniter": "dev-develop"
    }
}
````
Now tell composer to download the component by running the command:

``` bash
$ php composer.phar update lightsuner/stack-codeigniter
````

Composer will install the component to your project's `vendor/lightsuner` directory.

##Usage

Replace index.php with this:

```php
use StackCI\Application as CIApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

require_once __DIR__.'/vendor/autoload.php';

$session = new Session();
$session->start();

$request = Request::createFromGlobals();
$request->setSession($session);


$ciApp = (new CIApplication(__DIR__, 'development'))->init();

$app = (new Stack\Builder())
    ->resolve($ciApp);

$response = $app->handle($request);

$response->send();

$app->terminate($request, $response);
````

You can also execute some code before Codeigniter will initialized. Use ``StackCI\Application::beforeKernelLoad()``:
```php
$ciApp = (new \StackCI\Application(__DIR__, 'development'))
    ->beforeKernelLoad(function(){

        //... some code goes here

        define('PUBPATH', FCPATH."public");

        if( ! ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }

        //... some other code
    })
    ->init();
````

Use with WanWizard's [datamapper](http://datamapper.wanwizard.eu/):

To make it work correctly you should add ``STACKCIEXTPATH`` to `third_party/datamapper/bootstrap.php`
```php
....
if ( ! function_exists('load_class'))
{
	function &load_class($class, $directory = 'libraries', $prefix = 'CI_')

	......

	foreach (array(STACKCIEXTPATH, BASEPATH, APPPATH) as $path)
    		{
    			if (file_exists($path.$directory.'/'.$class.'.php'))
    			{
    				$name = $prefix.$class;

    				if (class_exists($name) === FALSE)
    				{
    					require($path.$directory.'/'.$class.'.php');
    				}

    				break;
    			}
    		}

    .....

````
and then load datamapper's bootstrap
```php
$ciApp = (new CIApplication(__DIR__, 'development'))
    ->beforeKernelLoad(function(){
        //load Datamapper's bootstrap
        require_once APPPATH.'third_party/datamapper/bootstrap.php';
    })
    ->init();
````

##Details

There is a `BaseApplication` and two folders - `Ext` and `Orig`.

1. `BaseApplication` do the same thing than default Codeigniters's index.php - load and run Kernel.
2. `Orig` contains original files of Codeigniter such as Router, Input, Output, Loader and etc.
3. `Ext` contains extended files

####New functional:
``Libraries/Session`` - new method ``migrate``:

```php
    $this->session->migrate($destroy, $lifetime);
````

``Core/Input`` - new method ``getRequest``:

```php
    $this->input->getRequest();
````

##Advices
- Use [LazyHttpKernel](https://github.com/stackphp/LazyHttpKernel) to use `stack-codeigniter` in conjunction with other frameworks through ``stackPHP``.