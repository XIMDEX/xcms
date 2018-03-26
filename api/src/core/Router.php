<?php

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
     * @param $path
     * @param $func
     */
    public function addRoute($path, $func)
    {
        $this->routes[$path] = $func;
    }

    /**
     *
     * Returns the function that handles de current path
     */
    /**
     * @return array with action and public (true/false)
     * @throws APIException
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
     * @return string
     */
    public function getUserToken()
    {
        $token = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        if (is_null($token)) {
            $token = isset($_GET['token']) ? $_GET['token'] : null;
        }
        return $token;
    }

    public function addAllowedRequest($item)
    {
        array_push($this->allowedRequests, $item);

    }

    /**
     * Executes the current path
     */
    public function execute()
    {
        $response = new Response();
        try {
            $action = $this->getFunction();
            $token = $this->getUserToken();

            // check user
            if (!$action['public'] && !Token::validateToken($token)) {
                throw new APIException('User not logged', -1);
            }
            if (!is_callable($action['function'])) {
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
