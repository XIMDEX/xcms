<?php
use Ximdex\API\Request;
use Ximdex\API\Response;
use Ximdex\API\Router;
use Ximdex\Modules\Manager;


if (!defined('XIMDEX_ROOT_PATH')) {
    require_once '../bootstrap.php';
}


ModulesManager::file('/public_rest/classes/Request.php');
ModulesManager::file('/public_rest/classes/Response.php');
ModulesManager::file('/public_rest/classes/Router.php');
ModulesManager::file('/public_rest/classes/APIException.php');
ModulesManager::file('/public_rest/classes/AbstractAPIAction.php');


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


// añadimos ahora las rutas de cada módulo (si lo hay);

foreach(Manager::getEnabledModules() as $module){
    $name = $module["name"];

    $module = $mManager->instanceModule($name);
    if(method_exists($module, 'addApiRoutes')) {
        $module->addApiRoutes( $router );
    }
}


$router->execute();
