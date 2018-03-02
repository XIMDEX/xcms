<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 19/02/16
 * Time: 14:39
 */

namespace XimdexApi\actions;

use XimdexApi\core\Router;


abstract class Action
{

    protected const PREFIX = '';
    protected const ROUTES = [];
    protected const PUBLIC = [];

    public static function addMethods(Router $router)
    {
        foreach (static::ROUTES as $route => $action) {
            $router->addRoute(static::getPath($route), [static::class, $action]);

            if (in_array($route, static::PUBLIC)) {
                $router->addAllowedRequest(static::getPath($route));
            }
        }
    }

    protected static function getPath($action)
    {
        return (!empty(static::PREFIX) ? static::PREFIX . '/' : '') . $action;
    }
}