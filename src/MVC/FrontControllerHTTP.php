<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\MVC;

use Ximdex\Runtime\App;
use Ximdex\Models\Action;

/**
 * @brief FrontController for the http interface
 *
 * FrontController for the http interface, provide specific methods to read
 * the parameters from http and launches the ApplicationController to compose
 * the http interface
 */
class FrontControllerHTTP extends FrontController
{
    /**
     * Verifica y parsea la URL, y llama al ApplicationController
     * 
     * @see inc/mvc/FrontController#dispatch()
     */
    function dispatch()
    {
        // Comprueba si la URL de acceso coincide con UrlRoot
        if ($this->_checkURI()) {
            if (! array_key_exists('action', $_GET)) {
                $_GET['action'] = null;
            }
            if (! array_key_exists('redirect_other_action', $_GET) && 'installer' != $_GET['action']) {
                $this->parseFriendlyUrl();
            }

            // Parser URL
            $this->parseURI();

            // Llama al ApplicationController
            $appController = new ApplicationController();
            $appController->setRequest($this->request);
            
            /*
             * Getting params to check permissions.
             * Some actions can be loaded for everyone
             */
            $idNode = $this->request->getParam('nodeid');
            $actionName = $this->request->getParam('action');
            $idAction = $this->getActionId();
            $listAllowedActions = Action::getAlwaysAllowedActions();
            if (in_array($actionName, $listAllowedActions) || $this->isAllowedAction($idNode, $idAction)) {
                $appController->compose();
                $this->hasError = $appController->hasError();
                $this->msgError = $appController->getMsgError();
            } else {
                $this->hasError = true;
                $this->msgError = 'The action cannot be executed on the selected node';
            }
        }
    }

    /**
     * Comprueba si la URL de acceso coincide con UrlRoot
     * 
     * @return boolean
     */
    function _checkURI()
    {
        $uri_request = explode('?', $_SERVER['PHP_SELF'], 2);
        $ximdex = parse_url(App::getValue('UrlHost') . App::getValue('UrlRoot'));
        if ((isset($ximdex['path']) && strpos($uri_request[0], $ximdex['path']) === false)) {
            $this->setError('Error: la URL de acceso no coincide con la UrlRoot', 'FrontController');
            return false;
        } else {
            return true;
        }
    }

    function parseFriendlyUrl()
    {
        $urlRoot = App::getValue('UrlHost') . App::getValue('UrlRoot');
        
        // Get base url of ximdex
        $base = '/' . preg_replace("/http:\/\/.+?\//", '', $urlRoot) . '/';

        // Remove base of request_uri
        $url = str_replace($base, '', $_SERVER['REQUEST_URI']);

        // Split params/query_string
        list($url, $query_string) = explode('?', $url, 2) + array(NULL, NULL);

        // Parse query string as $_GET
        parse_str($query_string, $_GET);
        
        // Default action without querystring
        if (null == $query_string) {
            $_GET['action'] = 'browser3';
        }

        // Remove index.php|frontcontroller of url if this is
        $url = preg_replace("/index\.php/", '', $url);
        $url = str_replace(basename(APP_ROOT_PATH), '', $url);

        // Get friendly params
        $params = explode('/', ltrim($url, '/'));
        $max = count($params);
        if ($max > 0 && !empty($params[0])) {
            if ('xmd' != $params[0]) {
                $_GET['action'] = $params[0]; //fist params is action
            }
            for ($i = 1; $i < $max; $i++) {
                if (null == $params[$i]) {
                    break;
                }
                if ('_' == $params[$i][0]) {
                    
                    // Params starting with '_' is method
                    $_GET['method'] = substr($params[$i], 1);
                }
                
                //Params friendly: /mod~ximTAGS/nodeid~500/
                list($name, $value) = explode('~', $params[$i], 2) + array(NULL, NULL);
                if (!empty($name)) {
                    $_GET[$name] = $value;
                }
            }
        }
    }

    function ModuleShortUrl()
    {
        $actionName = $this->request->getParam('action');
        if ('createaccount' == $actionName) {
            $this->request->setParam('action', 'login');
        }
    }

    function checkSession()
    {
        $session_exists = \Ximdex\Runtime\Session::get('userID');
        $action_without_session = array('createaccount', 'logout', 'installer', 'rendernode');
        $actionName = $this->request->getParam('action');
        $method = $this->request->getParam('method');
        $session_exists = \Ximdex\Runtime\Session::get('userID');
        $need_session = !in_array($actionName, $action_without_session);
        if ('installer' == $actionName) {
            $this->request->setParam('actionName', 'installer');
            $this->request->setParam('action', 'installer');
            $this->request->setParam('actionid', 0);
            $this->request->setParam('mod', null);
        } else if (!$session_exists && $need_session) {
            $this->request->setParam('actionName', 'login');
            $this->request->setParam('action', 'login');
            if ('check' != $method) {
                $this->request->setParam('method', 'index');
            }
            $this->request->setParam('actionid', 0);
            $this->request->setParam('mod', null);
        }
    }

    /**
     * Parses the URI
     */
    function parseURI()
    {
        // We add the diferents parameters from get, post and file, to the class request
        $this->setToRequest();
        $this->ModuleShortUrl();
        $this->checkSession();
        $params = null;
        $actionName = $this->request->getParam('action');
        $module = $this->request->getParam('mod');
        $method = $this->request->getParam('method');
        $renderer = $this->request->getParam('renderer');
        $actionId = $this->getActionId();

        // Get action by $actionId
        if (!empty($actionId)) {
            $action = new Action($actionId);
            $actionName = $action->get('Command');
            $module = $action->get('Module');
            $params = $action->get('Params');
            $method = $action->get('Params');
            $renderer = $this->getRenderer();
            parse_str($action->get('Params'), $params);
        }
        $actionPath = $this->getActionPath($module);

        // If action doesnt exist
        if (empty($module) && !file_exists(XIMDEX_ROOT_PATH . $actionPath . $actionName)) {
            $actionId = null;
            $actionName = 'browser3';
        } else if (empty($actionName)) {
            $actionId = null;
            $actionName = 'composer';
            $module = null;
        }
        $method = $this->getMethod();
        $this->setToParams($actionName, $actionId, $actionPath, $method, $module, $renderer, $params);
        $this->setXsessionParams($renderer, $actionPath);
        $this->normalizeNodesParam();
    }

    function getActionId()
    {
        $actionId = $this->request->getParam('actionid');
        $actionName = $this->request->getParam('action');
        $nodeid = $this->request->getParam('nodeid');
        if (empty($actionId)) {
            $actionId = isset($_REQUEST['actionid']) ? $_REQUEST['actionid'] : 0;
        }
        if (!empty($actionId)) {
            return (int) $actionId;
        } else if (!empty($actionName) && 'browser3' != $actionName && !empty($nodeid)) {
            $module = $this->request->getParam('mod');
            $action = new Action();
            $actionId = (int) $action->setByCommandAndModule($actionName, $nodeid, $module);
            return $actionId;
        }
        return null;
    }

    function getActionPath($module = null)
    {
        if (! empty($module)) {
            return \Ximdex\Modules\Manager::path($module) . '/actions/';
        }
        return App::getValue('UrlFrontController') . '/actions/';
    }

    function getMethod()
    {
        $method = $this->request->getParam('method');
        if (empty($method)) {
            return 'index';
        }
        return $method;
    }

    function getRenderer()
    {
        return 'Smarty';
    }

    function setToRequest()
    {
        $this->request->setParameters($_FILES);
        $this->request->setParameters($_GET);
        $this->request->setParameters($_POST);
    }

    function setToParams($actionName, $actionId, $actionPath, $method, $module, $renderer, $params = NULL)
    {
        $this->request->setParam('action', $actionName, 'failover');
        $this->request->setParam('actionName', $actionName, 'failover');
        $this->request->setParam('method', $method);
        $this->request->setParam('action_path', $actionPath);
        $this->request->setParam('actionid', $actionId);
        $this->request->setParam('module', $module);
        $this->request->setParam('mod', $module);
        $this->request->setParam('renderer', $renderer);
        $this->request->setParam('out', 'WEB');
        if (!empty($params)) {
            $this->request->setParam('params', $params);
        }
    }

    function setXsessionParams($renderer, $_action_path)
    {
        // Guardado de datos persistente
        \Ximdex\Runtime\Session::set('renderer', $renderer);
        \Ximdex\Runtime\Session::set('actionPath', $_action_path);
    }
}
