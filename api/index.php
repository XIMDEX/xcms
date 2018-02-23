<?php

use XimdexApi\core\Request;
use XimdexApi\core\Response;
use XimdexApi\core\Router;
use Ximdex\Modules\Manager;


if (!defined('XIMDEX_ROOT_PATH')) {
    require_once '../bootstrap.php';
}


Manager::file('/api/src/Request.php');
Manager::file('/api/src/Response.php');
Manager::file('/api/src/Router.php');
Manager::file('/api/src/APIException.php');
Manager::file('/api/src/AbstractAPIAction.php');

/**
 * @TODO: check global setup
 */

session_set_cookie_params(0, "/");

$router = new Router(new Request());


$router->addAllowedRequest("ping");

$router->addRoute('ping', function (Request $r, Response $w) {
    $w->setStatus(0);
    $w->setMessage("");
    $w->setResponse("PONG!");
    $w->send();
});


/************ ACTIONS ************/


/************ Modules actions ************/

// añadimos ahora las rutas de cada módulo (si lo hay);

foreach (Manager::getEnabledModules() as $module) {
    $name = $module["name"];

    $module = $mManager->instanceModule($name);
    if (method_exists($module, 'addApiRoutes')) {
        $module->addApiRoutes($router);
    }
}


$router->execute();
