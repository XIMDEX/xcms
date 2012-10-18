<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




 


if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../..'));
}
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
	 * @return unknown_type
	 */
	function Cache($compression = false, $expirationTime = 3600) {
		$this->expire = $expirationTime;
		$this->compress = (bool) $compression ? MEMCACHE_COMPRESSED : false;
	}
	
	/**
	 * 
	 * @return unknown_type
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
	 * @return unknown_type
	 */
	function set($key, $value) {
		$memCache = Cache::getInstance();
		return $memCache->set($key, $value, $this->compress, $this->expire);
	}
	
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function get($key) {
		$memCache = Cache::getInstance();
		return $memCache->get($key);
	}
	
	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function replace($key, $value) {
		$memCache = Cache::getInstance();
		return $memCache->replace($key, $value, $this->compress, $this->expire);
	}
	
	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function delete($key) {
		$memCache = Cache::getInstance();
		return $memCache->delete($key);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function flush() {
		$memCache = Cache::getInstance();
		$memCache->flush();
	}

}

?>