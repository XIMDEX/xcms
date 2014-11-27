<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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
//
require_once(XIMDEX_ROOT_PATH . '/api/utils/ResponseBuilder.class.php');
require_once(XIMDEX_ROOT_PATH . '/api/services/NodeService.class.php');
require_once(XIMDEX_ROOT_PATH . '/api/classes/AbstractAPIAction.class.php');

/**
 *
 * @brief FrontController for the http api interface
 *
 * FrontController for the http api interface, provide specific methods to read
 * the parameters from http and launches the ApplicationController to compose
 * the http interface
 *
 */
class FrontControllerAPI extends FrontController
{
    const XIM_API_TOKEN_PARAM = "ximtoken";
    const XIM_API_DEFAULT_METHOD = "index";
    const XIM_API_KEY_CONFIG_PARAM = "ApiKey";
    const XIM_API_IV_CONFIG_PARAM = "ApiIV";

    private $apiActionsFolder;

    /**
     * <p>Stores the current action being executed</p>
     * @var string
     */
    private $action;

    /**
     * <p>Stores the current action method being executed</p>
     * @var string
     */
    private $method;

    /**
     * <p>Default constructor</p>
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiActionsFolder = realpath(XIMDEX_ROOT_PATH . '/api/actions/');
        $this->response->set('Content-Type', 'application/json');
        $this->request->setParam('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
        $this->setToRequest();
    }

    /**
     * <p>Dispatches the current request to be executed by the requested action</p>
     */
    function dispatch()
    {
        // Comprueba si la URL de acceso coincide con UrlRoot
        if (!$this->_checkURI()) {
            $this->sendErrorResponse('400', 'Bad URL requested');
        }

        if (!$this->checkAction()) {
            $this->sendErrorResponse('400', 'Bad request. Action does not exist');
        }

        $factory = new \Ximdex\Utils\Factory($this->apiActionsFolder . '/' . $this->action, "Action_");
        $actionObject = $factory->instantiate($this->action);

        //Retrieve and check the ximtoken if the action requires security
        if (in_array('SecuredAction', class_implements($actionObject))) {
            $encryptedXimtoken = $this->request->getParam(self::XIM_API_TOKEN_PARAM);
            if ($encryptedXimtoken == null)
                $this->sendErrorResponse('400', 'Token missing for this action');

            $tokenService = new \Ximdex\Services\Token();

            $ximtoken = $tokenService->decryptToken($encryptedXimtoken, \App::getValue(self::XIM_API_KEY_CONFIG_PARAM), \App::getValue(self::XIM_API_IV_CONFIG_PARAM));

            if ($ximtoken == null) {
                $this->sendErrorResponse('400', 'The token does not have a valid format');
            }

            /*
            if (!($ximtoken['validTo'] > time())) {
                $this->sendErrorResponse('400', 'The token has expired');
            }*/

            if ($tokenService->hasExpired($ximtoken)) {
                $this->sendErrorResponse('400', 'The token has expired');
            }

            $this->request->setParam('XimUser', $ximtoken['user']);
        }

        if (method_exists($actionObject, $this->method) && is_callable(array($actionObject, $this->method))) {
            /* Sets the ResponseBuilder object to the specified Action and initializes it with the Response instance
             * to be used. This instance is the same as the one provided as parameter to the requested method of the action
             */
            $actionObject->setResponseBuilder(new ResponseBuilder($this->response));
            call_user_func(array($actionObject, $this->method), $this->request, $this->response);
            $respText = is_array($this->response->getContent()) ? json_encode($this->response->getContent()) : $this->response->getContent();
            $this->response->set('Content-Type', 'application/json');
            $this->response->sendHeaders();
            echo $respText;
            exit();
        } else {
            $this->sendErrorResponse('400', "Bad method for this action");
        }
    }

    /**
     * <p>Sends an error response with the specified status code and message</p>
     */
    private function sendErrorResponse($status_code, $message)
    {
        $this->response->header_status($status_code);
        $respContent = array("error" => 1, "message" => $message);
        $this->response->setContent($respContent);
        echo json_encode($this->response->getContent());
        exit();
    }

    /**
     * <p>Checks whether the requested action exists and whether it needs security to be executed</p>
     */
    private function checkAction()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = str_replace('?' . $_SERVER['QUERY_STRING'], "", $requestUri);
        if (!preg_match('/.*\/api(?:\-\w+?)?\/(.*?)\/?$/', $requestUri, $matches))
            // The reg.exp doesn't match or an error ocurred
            return false;
        $am = $matches[1];
        $pieces = explode(DIRECTORY_SEPARATOR, $am);

        switch (count($pieces)) {
            case 1:
                $this->action = $pieces[0];
                $this->method = self::XIM_API_DEFAULT_METHOD;
                break;
            default:
                $this->action = $pieces[0];
                $this->method = $pieces[1];
        }

        if (!file_exists($this->apiActionsFolder . '/' . $this->action)) {
            return false;
        }

        return true;
    }

    /**
     * Comprueba si la URL de acceso coincide con UrlRoot
     * @return unknown_type
     */
    function _checkURI()
    {
        $host_request = $_SERVER["HTTP_HOST"];
        $uri_request = explode("?", $_SERVER["REQUEST_URI"], 2);
        $ximdex = parse_url(\App::getValue('UrlRoot'));

        if ($ximdex["host"] != $_SERVER["HTTP_HOST"] && strpos($uri_request, $ximdex["path"]) === 0) {
            $this->_setError("Error: la URL de acceso no coincide con la UrlRoot", "FrontController");
            return false;
        } else {
            return true;
        }
    }

    /**
     * <p>Sets the request parameters in the current request</p>
     */
    function setToRequest()
    {
        $this->request->setParameters($_FILES);
        $this->request->setParameters($_GET);
        $this->request->setParameters($_POST);
    }

}
