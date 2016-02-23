<?php
namespace Ximdex\API;


use Ximdex\Logger;
use Ximdex\Utils\Session;

class Router
{
    private $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Checks if $relPathStr matches the current url path and executes $func
     * else it does nothing. If an Exception is throwed, it will catch it and will
     * send an error message
     *
     * @param string $relPathStr
     * @param callable $func
     * @throws \Exception
     */
    public function route($relPathStr, $func) {
        if($this->request->matchPath($relPathStr)){
            $response = new Response();
            try{
                if(!Session::check(false)){
                    throw new APIException('User not logged', -1);
                }
                $func($this->request, $response);
                $data = $response->render();
            } catch (APIException $e){
                Logger::error($relPathStr . ': ' . $e->getMessage());
                $response = new Response();
                $response->setStatus($e->getStatus())->setMessage($e->getMessage());
                $data = $response->render();
            }
            echo $data;
            die();
        }
    }
}