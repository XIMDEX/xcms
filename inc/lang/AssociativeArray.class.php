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




class AssociativeArray {

	/**
	 *
	 * @access protected
	 */
	var $_data = NULL;

	/**
	 * 
	 * @return unknown_type
	 */	
	function AssociativeArray() {

		$this->_data = array();
	}
	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function add($key, &$value) {
		if($value) {
			$this->_data[$key][] = $value;
		}elseif($value == "0") {
			$this->_data[$key][] = 0;
		}else {
			$this->_data[$key][] = null;
		}
	}

	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function set($key, $value) {
		if($value) {
			$this->_data[$key] = $value;
		}elseif($value == "0") {
			$this->_data[$key] = 0;
		}else {
			$this->_data[$key] = null;
		}
	}

	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function & get($key) {
		if (isset($this->_data[$key])) {
			$value =  $this->_data[$key];
			return $value;
		} else {
			$retval = NULL;
			return $retval;
		}
	}

	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function exist($key) {
		return isset($this->_data[$key]);
	}

	/**
	 * 
	 * @param $value
	 * @return unknown_type
	 */
	function getKey($value) {
		/* TODO: implement*/

		$retval = NULL;
		return $retval;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function getKeys() {
		return array_keys($this->_data);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function & getArray() {
		return $this->_data;	
	}

	/**
	 * 
	 * @param $key
	 * @return unknown_type
	 */
	function del($key) {

		if (isset($this->_data[$key])) {
			$value = $this->_data[$key];
			unset($this->_data[$key]);
			return $value; 
		} else {
			$retval = NULL;
			return $retval;
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function display() {
		/* TODO: detect context, CLI, web, etc...*/
		print("<pre>\n");
		print_r($this->_data);
		print("</pre>\n");
	}
	
	/**
	 * 
	 * @param $property
	 * @return unknown_type
	 */
	function __get($property) {
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		} else {
			return NULL;
		}
	}
	
	/**
	 * 
	 * @param $property
	 * @param $value
	 * @return unknown_type
	 */
	function __set($property, $value) {
		if($value) {
			$this->_data[$property] = $value;
		}elseif($value == "0") {
			$this->_data[$property] = 0;
		}else {
			$this->_data[$property] = null;
		}
	}
}

?>