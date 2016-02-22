<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 22/02/16
 * Time: 9:24
 */

namespace Ximdex\API;


use Ximdex\Utils\Session;

class Router
{
    private $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * @param string $relPathStr
     * @param callable $func
     * @throws \Exception
     */
    public function Route($relPathStr, $func) {
        if($this->request->MatchPath($relPathStr)){
            $response = new Response();
            try{
                if(!Session::check()){
                    throw new \Exception('User not logged');
                }
                $func($this->request, $response);
                $data = $response->render();
            } catch (Exception $e){
                Logger::error($relPathStr . ': ' . $e->getMessage());
                $response = new Response();
                $response->setStatus(-1);
                $response->setMessage('An error was through');
                $data = $response->render();
            }
            echo $data;
            die();
        }
    }
}