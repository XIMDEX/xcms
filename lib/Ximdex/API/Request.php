<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 22/02/16
 * Time: 8:59
 */

namespace Ximdex\API;


class Request
{
    private $path;
    private $query;

    public function __construct()
    {
        $url = $_SERVER['REQUEST_URI'];


        $pathStr = parse_url($url, PHP_URL_PATH);
        $queryStr = parse_url($url, PHP_URL_QUERY);

        $path = $this->splitPath($pathStr);
        parse_str($queryStr, $query);

        while($path[0] != 'wservices'){
            array_shift($path);
        }
        array_shift($path);
        $this->path = $path;
        $this->query = $query;
    }

    /**
     * Checks if $path matches with the current url
     *
     * @param string $path
     * @return bool
     */
    public function matchPath($path){
        $pathSplitted = $this->splitPath($path);
        if(count($pathSplitted) != count($this->path)){
            return false;
        }

        for($i = 0; $i < count($this->path); $i++){
            $subject = $this->path[$i];
            $pattern = "/^{$pathSplitted[$i]}$/i";
            $check = preg_match($pattern, $subject);
            if($check !== 1){
                return false;
            }
        }
        return true;
    }

    /**
     * Get a query value from a key
     *
     * @param $key
     * @param bool $optional
     * @param null $default
     * @return null
     * @throws \Exception
     */
    public function get($key, $optional = false, $default = null){
        if(!$optional && !isset($this->query[$key])){
            throw new \Exception("Key $key not found in params");
        }
        if(!isset($this->query[$key])){
            return $default;
        }
        return $this->query[$key];
    }

    /**
     * Return the current path as an array
     *
     * @return array
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Splits a path and returns an array
     *
     * @param string $pathStr
     * @return array
     */
    private function splitPath($pathStr){
        return preg_split('/\//', $pathStr, -1, PREG_SPLIT_NO_EMPTY);
    }
}