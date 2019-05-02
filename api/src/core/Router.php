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

namespace XimdexApi\core;

use Ximdex\Logger;

class Router
{
    private $request;

    /**
     * @var array List of allowed (public) requests
     */
    private $allowedRequests = array();
    
    /**
     * @var array List of routes and functions
     */
    private $routes = array();

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->routes = array();
        $this->allowedRequests = array();
    }

    /**
     * Add new route and function to router
     * 
     * @param string $path
     * @param string $func
     */
    public function addRoute(string $path, string $func)
    {
        $this->routes[$path] = $func;
    }

    /**
     * Returns the function that handles de current path
     * 
     * @throws APIException
     * @return array
     */
    private function getFunction()
    {
        $currentPath = $this->request->getPath();
        foreach ($this->routes as $key => $value) {
            if (preg_match("#^{$key}$#i", $currentPath) === 1) {

                // check is a public function
                $public = false;
                foreach ($this->allowedRequests as $url) {
                    if (preg_match("#^{$url}$#i", $currentPath) === 1) {
                        $public = true;
                        break;
                    }
                }
                return array(
                    "function" => $value,
                    "public" => $public
                );
            }
        }
        throw new APIException('Route Not Found', 404);
    }

    /**
     * Get user token for authentication
     * 
     * @return NULL|string
     */
    public function getUserToken()
    {
        $token = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        if (is_null($token)) {
            $token = isset($_GET['token']) ? $_GET['token'] : null;
        }
        return $token;
    }

    public function addAllowedRequest(string $item)
    {
        array_push($this->allowedRequests, $item);
    }

    /**
     * Executes the current path
     * 
     * @throws APIException
     */
    public function execute()
    {
        $response = new Response();
        try {
            $action = $this->getFunction();
            $token = $this->getUserToken();

            // Check user
            if (! $action['public'] && !Token::validateToken($token)) {
                throw new APIException('User not logged', -1);
            }
            if (! is_callable($action['function'])) {
                throw new APIException('Bad Action', 500);
            }
            call_user_func($action['function'], $this->request, $response);
        } catch (APIException $e) {
            Logger::error($this->request->getPath() . ': ' . $e->getMessage());
            $response->setStatus($e->getStatus())->setMessage($e->getMessage());
            $response->send();
        }
    }
}
