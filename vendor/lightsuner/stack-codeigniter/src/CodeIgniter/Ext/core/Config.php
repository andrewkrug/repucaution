<?php

use StackCI\CodeIgniter\Orig\Core\Config;
use Symfony\Component\HttpFoundation\Request;

class CI_Config extends Config
{

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {

        $this->request = $request;

        /**
         * TODO i think there will be front controller path, but it includes *.php/etc...
         * https://github.com/stackphp/url-map/issues/9
        */
        //$this->set_item('base_url', $request->getSchemeAndHttpHost().$request->getBasePath());
    }

    /**
     * Get Current Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function site_url($uri = '')
    {
        $uri = '/'.ltrim($uri, '/');

        return $this->request->getUriForPath($uri);
    }

}
