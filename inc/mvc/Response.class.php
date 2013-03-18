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
 * @brief Http response parameters container
 *
 * This class is intended to store the response parameters and send back them to the server
 *
 */
class Response {

	private	$_headers;
	private $_content;

	function __construct() {

		$this->_headers = new AssociativeArray();
		ob_start();
	    foreach ($_SERVER as $key => $value) {
	        if (preg_match('/^HTTP_(.*)$/' , $key)) {
	            $key = str_replace('_', ' ', substr($key, 5));
	            $key = str_replace(' ', '-', ucwords(strtolower($key)));
	            $this->_headers->add($key, $value);
	        }
	    }
	}


	/**
	 * Añadimos un valor a un array
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	public function set($key, $value) {
		$this->_headers->set($key, $value);
	}

	/**
	 *
	 * @param $key
	 * @return unknown_type
	 */
	public function get($key) {
		return $this->_headers->get($key);
	}

	/**
	 *
	 * @return unknown_type
	 */
	public function sendHeaders() {
		echo ob_get_clean(); // asegura que no ha habido escritura antes de enviar las cabeceras
		$keys = $this->_headers->getKeys();
		foreach ($keys as $key) {
			$values = $this->get($key);
			if (is_array($values)) {
				foreach ($values as $value) {
					header($key . ":" . $value);
				}
			} else if(!empty($key) && !empty($values) )  {
				header($key . ": " . $values);
			}
		}
	}

	public function sendStatus($string, $replace, $status) {
		echo ob_get_clean(); // asegura que no ha habido escritura antes de enviar las cabeceras

		if (is_numeric($status)) {
			header($string, $replace, $status);
//			die();
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	public function getContent() {
		return $this->_content;
	}

	/**
	 *
	 * @param $content
	 * @return unknown_type
	 */
	public function setContent($content) {
		$this->_content = $content;
	}
}
?>
