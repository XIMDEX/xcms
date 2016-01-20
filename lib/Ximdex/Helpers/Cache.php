<?php

namespace Ximdex\Helpers ;

use MemCache ;

if (!defined('MEMCACHE_COMPRESSED')) {
    define ('MEMCACHE_COMPRESSED', 'MEMCACHE_COMPRESSED');
}


class Cache {
    /**
     *
     * @var int
     */
    var $expire = 3600;
    /**
     *
     * @var String
     */
    var $compress = MEMCACHE_COMPRESSED;

    /**
     * Constructor
     * @param $compression
     * @param $expirationTime
     */
    function Cache($compression = false, $expirationTime = 3600) {
        $this->expire = $expirationTime;
        $this->compress = (bool) $compression ? MEMCACHE_COMPRESSED : false;
    }

    /**
     *
     * @return memcache
     */
    function & getInstance() {

        static $memCacheConnection = NULL;

        if ($memCacheConnection === NULL) {
            $memCacheConnection = new MemCache();
            $memCacheConnection->connect('localhost', 11211);
        }

        return $memCacheConnection;
    }

    /**
     *
     * @param $key
     * @param $value
     * @return string
     */
    function set($key, $value) {
        $memCache = Cache::getInstance();
        return $memCache->set($key, $value, $this->compress, $this->expire);
    }

    /**
     *
     * @param $key
     * @return string
     */
    function get($key) {
        $memCache = Cache::getInstance();
        return $memCache->get($key);
    }

    /**
     *
     * @param $key
     * @param $value
     * @return string
     */
    function replace($key, $value) {
        $memCache = Cache::getInstance();
        return $memCache->replace($key, $value, $this->compress, $this->expire);
    }

    /**
     *
     * @param $key
     * @return bool
     */
    function delete($key) {
        $memCache = Cache::getInstance();
        return $memCache->delete($key);
    }

    /**
     *
     */
    function flush() {
        $memCache = Cache::getInstance();
        $memCache->flush();
    }

}