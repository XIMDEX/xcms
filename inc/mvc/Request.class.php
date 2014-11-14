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
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/lang/AssociativeArray.class.php');

/**
 * 
 * @brief Http request parameters container
 * 
 * This class is intended to store the request parameters
 *
 */

class Request {

	/**
	 * 
	 * @var unknown_type
	 */
	var $params;

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function __construct() {
		$this->params = new AssociativeArray();
	}


	/**
	 * Añadimos un valor a un array
	 * @param $key
	 * @param $value
	 * @param $defValue
	 * @return unknown_type
	 */
	function add($key, $value, $defValue = "") {

		$value = isset ($value) ? $value : $defValue;
		$this->params->add($key, $value);
	}

	/**
	 * 
	 * @param $key
	 * @param $value
	 * @param $defValue
	 * @return unknown_type
	 */
	function setParam ($key, $value, $defValue = "") {

		$value = isset ($value) ? $value : $defValue;

		$this->params->set($key, $value);
	}

	/**
	 * 
	 * @param $vars
	 * @return unknown_type
	 */
	function setParameters ($vars) {
		if(!empty($vars) > 0 ) {
			foreach($vars as $key => $value) {
				if (is_object($value) || is_array($value)) {		
					$this->setParam($key, $value);
				} else {
					$this->setParam($key, trim($value));
				}
			}
		}
	}

	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function & getParam ($key) {
		return $this->params->get($key);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function &  getRequests() {
		return $this->params->getArray();
	}

	/**
	 * need to clean this.
	 * @return unknown_type
	 */
	function isGet () {
		return (!empty($_GET));
	}
	function isPost () {
		return (!empty($_POST));
	}
	function isCookie () {
		return (!empty($_COOKIE));
	}
	function isFile () {
		return (!empty($_FILES));
	}
	

	// Transitional methods. You MUST use Request object returned from ApplicationController.

	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	public static function get($name) {

		return isset($_GET[$name]) ? $_GET[$name] : NULL;
	}

	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	public static function post($name) {

		return isset($_POST[$name]) ? $_POST[$name] : NULL;
	}

	/**
	 * 
	 * @param $name
	 * @return unknown_type
	 */
	public static function request($name) {

		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : NULL;
	}

	
}
?>