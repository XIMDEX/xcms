<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 22/02/16
 * Time: 8:59
 */

namespace XimdexApi\core;


class Request
{
    private $path;

    public function __construct()
    {
        $this->path = isset( $_GET['_action']) ? $_GET['_action'] : "" ;
        $this->path = trim( $this->path ,  "/" ) ;
    }
    /**
     * Get a query value from a key
     *
     * @param $key
     * @param bool $optional
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public function get($key, $optional = false, $default = null){
        if(!$optional && !isset($_GET[$key])){
            throw new APIException("Key $key not found in params", 1);
        }
        if(!isset($_GET[$key])){
            return $default;
        }
        return $_GET[$key];
    }

    /**
     * Return the current path as a string
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}