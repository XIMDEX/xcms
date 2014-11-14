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
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');

define('ESCAPE', true);
define('NO_ESCAPE', false);

class xIterator {

	var $_objectName;
	var $_module = NULL;
	var $_objects;
	var $_current = 0;

	function xIterator($condition, $args, $escape = ESCAPE) {
		$object = new $this->_objectName;
		$result = $object->find(ALL, $condition, $args, MULTI, $escape);
		
		$this->_objects = array();
		if (count($result) > 0) {
			reset($result);
			while (list(, $element) = each($result)) {
				$object = new $this->_objectName();
				$object->_unserialize($element);
				$this->_objects[] = $object;
			}
		}
	}
	
	function reloadConstructors() {
		foreach($this->_objects as $key => $object) {
			$constructorName = get_class($object); 
			$object->$constructorName($object->get($object->_idField));
		}
	}
	
	function first() {
		return isset($this->_objects[0]) ? $this->_objects[0] : NULL;
	}
	
	function current() {
		if (isset($this->_objects[$this->_current])) {
			return $this->_objects[$this->_current];
		}
		return NULL;
	}
	
	function last() {
		$total = count($this->_objects);
		if ($total > 0) {
			return $this->_objects[$total - 1];
		}
		return NULL;
	}
	
	function reset() {
		$this->_current = 0;
	}
	
	function key() {
		return $this->_current;
	}
	
	function hasMore() {
		return isset($this->_objects[$this->_current + 1]);
	}
	
	
	/**
	 * returns current and advance pointer
	 *
	 * @return boolean
	 */
	function next() {
		if (($this->key() + 1) > $this->count()) {
			return NULL;
		}
		
		$currentObject = $this->current();
		$this->_current += 1; 
		return $currentObject;
	}
	
	function count() {
		return count($this->_objects);
	}
	
	function seek($n) {
		$this->_current = $n;
	}
}


?>