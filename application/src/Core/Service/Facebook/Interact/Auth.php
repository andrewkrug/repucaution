<?php
/**
 * Author: Alex P.
 * Date: 25.04.14
 * Time: 19:38
 */

namespace Core\Service\Facebook\Interact;


/**
 * Class Auth
 * @package Core\Service\Facebook\Interact
 */
class Auth
{
    /**
     * @var \Facebook
     */
    protected $facebook;

    /**
     * List of request params
     * @var array
     */
    protected $params;

    /**
     * @var callable
     */
    protected $isAjax;

    /**
     * @var callable
     */
    protected $renderJson;

    /**
     * @param \Facebook $facebook
     * @param array $params
     * @param callable $isAjax - to determine whether the request is XMLHttpRequest
     * @param callable $renderJson
     */
    public function __construct(\Facebook $facebook, array $params = array(), \Closure $isAjax, \Closure $renderJson)
    {  
        $this->facebook = $facebook;
        $this->params = $params;
        $this->isAjax = $isAjax;
        $this->renderJson = $renderJson;

    }

    /**
     * @param string $uri
     */
    public function setRedirectUri($uri)
    {
        $this->params['redirect_uri'] = $uri;
    }

    /**
     * Redirect user to facebook to obtain params
     * @return string - user id
     */
    public function runAuth()
    {
        $user = $this->facebook->getUser();
        if (!$user) {
            if (!$this->isAjax()) {
                redirect($this->getLoginUrl());
            } else {
                // output json
                $this->renderJson(array(
                        'loginUrl' => $this->getLoginUrl(),
                    ));
            }

        }
        return $user;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->facebook->getLoginUrl($this->params);
    }

    /**
     * Determine whether the request is XMLHttpRequest
     * @return bool
     */
    protected function isAjax()
    {
        return call_user_func($this->isAjax);
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function renderJson(array $data)
    {
        return call_user_func($this->renderJson, $data);
    }
}
