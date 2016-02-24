<?php
namespace Ximdex\API;


use Ximdex\Logger;
use Ximdex\Utils\Session;

class Router
{
    private $request;


    /**
     * @var array List of allowed (public) requests
     */
    private $allowedRequests = array() ;
    /**
     * @var array List of routes and functions
     */
    private $routes = array() ;

    public function __construct( Request $request )
    {
        $this->request = $request ;
        $this->routes = array();
        $this->allowedRequests = array();

    }


    /**
     * @param $path
     * @param $func
     *
     * Add new route and function to router
     */
    public function addRoute($path , $func)
    {
        $this->routes[$path] = $func;
    }

    /**
     *
     * Returns the function that handles de current path
     */
    public function getFunction(  ) {
        $currentPath = $this->request->getPath() ;
        foreach( $this->routes as $key => $value ) {

            if ( preg_match( $currentPath, $key ) === true ) {

                return $value;
            }
            throw new APIException('Route Not Found', 404 );

        }
    }

    public function addAllowedRequest( $item ) {
        array_push( $this->allowedRequests , $item ) ;


    }
}