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



require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');

class Factory {

	var $_path;
	var $_root_name;
	var $_error;
	
	function Factory($path, $root_name) {
	
		$this->_path = $path;
		$this->_root_name = $root_name;
	
	}
	
	function register() {
	
	}
	
	/**
	 * Devuelve una instancia de la clase indicada o NULL en uno de los siguientes casos:
	 * 
	 * 	1. El fichero que se pretende incluir no existe o no puede ser leido por cuestiones de privilegios.
	 * 	2. El fichero se incluyo pero no se encuentra la declaracion de la clase indicada.
	 * 
	 * Para comprobar la correcta instanciacion se deberia hacer -> if (is_object($o)) { ...
	 * Para comprobar cual es el error producido: Factory::getError()
	 */
	function instantiate($type = NULL, $args = array()) {
	
		$class = $this->_root_name;
		if (!is_null($type)) {
			$class .= $type;
		}
		$class_path = $this->_path . "/$class.class.php";

		
		if (!class_exists($class)) {
			if (file_exists($class_path) && is_readable($class_path)) {
				require_once($class_path);
			}else {
				$this->_setError("Factory::instantiate(): El fichero $class_path no existe o no puede ser leido");
				XMD_Log::error("Factory::instantiate(): El fichero $class_path no existe o no puede ser leido");
				return NULL;
			}
		}
		
		if (!class_exists($class)) {
			$this->_setError("Factory::instantiate(): La clase '$class' no ha sido declarada en $class_path");
			return NULL;
		}
		if ($args) {
			$obj = new $class($args);
		} else {
			$obj = new $class();
		}
		if (!is_object($obj)) {
			XMD_Log::fatal("Could'nt instanciate the class $class");
		}
		return $obj;
	
	}
	
	function _setError($msg) {
		XMD_Log::warning($msg);
		$this->_error = $msg;
	}
	
	function getError() {
		return $this->_error;
	}

}


?>
