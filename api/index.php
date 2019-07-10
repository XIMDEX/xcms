<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use XimdexApi\core\Request;
use XimdexApi\core\Response;
use XimdexApi\core\Router;
use Ximdex\Modules\Manager;
use XimdexApi\actions\LoginAction;
use XimdexApi\actions\XeditAction;
use XimdexApi\actions\NodeAction;

if (! defined('XIMDEX_ROOT_PATH')) {
    require_once '../bootstrap.php';
}
session_set_cookie_params(0, '/');
$router = new Router(new Request());
$router->addAllowedRequest('ping');
$router->addRoute('ping', function (Request $r, Response $w) {
    $w->setStatus(0);
    $w->setMessage('');
    $w->setResponse('PONG!');
    $w->send();
});

/*
 * Actions
 */
LoginAction::addMethods($router);
XeditAction::addMethods($router);
NodeAction::addMethods($router);

/*
 * Modules actions
 */

// Añadimos ahora las rutas de cada módulo (si lo hay)
$mManager = new Manager();
foreach (Manager::getEnabledModules() as $module) {
    $name = $module['name'];
    $module = $mManager->instanceModule($name);
    if (method_exists($module, 'addApiRoutes')) {
        $module->addApiRoutes($router);
    }
}
$router->execute();
