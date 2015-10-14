<?php
/**
 * User: alkuk
 * Date: 25.05.14
 * Time: 14:35
 */

namespace Core\Service\FireWall;

use Core\Service\AccessControl\AppAccessControl;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class Route
{
    /**
     * @var \Core\Service\AccessControl\AppAccessControl
     */
    protected $acl;

    /**
     * @var \Ion_auth
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $expressions;

    public function __construct($userManager, AppAccessControl $acl, array $expressions)
    {
        $this->userManager = $userManager;
        $this->acl = $acl;
        $this->parseExpressions($expressions);
    }

    /**
     * Check route and if required - restrict access to it
     *
     * @param $route
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function routeRestrict($route, Request $request)
    {
        foreach ($this->expressions as $expression => $result) {
            if (!preg_match($expression, $route)) {
                continue;
            }

            try {
                if (is_callable($result)) {
                    $result = $result($this->acl);
                }

                if ($result === false) {
                    throw new AccessDeniedHttpException();
                }
            } catch (Exception $e) {
                if (!$this->userManager->logged_in()) {
                    $request->getSession()->set('forward_url', $request->getUri());
                    redirect('auth');
                }

                throw $e;
            }



            break;
        }

    }

    /**
     * Parse input expressions with callbacks and save into $this->expressions
     *
     * @param array $expressions
     */
    protected function parseExpressions(array $expressions)
    {
        foreach ($expressions as $expression => $callback) {
            $subExpressions = explode(',', $expression);
            foreach ($subExpressions as $subExpression) {
                $this->expressions[$this->prepareExpression($subExpression)] = $callback;
            }
        }
    }

    /**
     * Prepare expression to preg
     *
     * @param string $expression
     *
     * @return string
     */
    protected function prepareExpression($expression)
    {
        $expression = trim($expression);
        $expression = str_replace('*', '.*', $expression);
        $expression = '#^'.$expression.'$#';

        return $expression;
    }
}
