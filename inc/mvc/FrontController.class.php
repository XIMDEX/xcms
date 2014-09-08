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



require_once(XIMDEX_ROOT_PATH . '/inc/helper/DebugLog.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/IController.class.php');
/**
 *
 * @brief Abstract controller who serves as base for http controller and cli controller
 *
 * Abstract controller who acts as factory for FrontControllerHTTP and FrontControllerCLI, receives the request
 * and send it to the adecuate controller
 *
 */
class FrontController extends IController {

	/**
	 * Realiza las tareas comunes a todas las acciones
	 * @return unknown_type
	 */
	function dispatch () {
		$frontController = $this->_selectFrontControllerType ();
		if (is_null ($frontController)) {

			$this->_setError ("Error: Tipo de entrada no reconocido", "FrontController");
		} else {
			$frontController->setRequest ($this->request);
			$frontController->dispatch ();
		}
		// Si hay error, no muestra la vista con include ()
		if ($frontController->hasError()) echo $frontController->getMsgError();
	}

	/**
	 * Determina el tipo de controlador que debe gestionar la petición
	 * @return unknown_type
	 */
	function _selectFrontControllerType () {
		$sapi_type = php_sapi_name ();
		if ($sapi_type == "cli") {
			$this->request->setParam ("enviroment", "cli");
			return new FrontControllerCLI ();
		}
		elseif (!(strpos ($sapi_type, "apache") === false) || (substr($sapi_type, 0, 3) == 'cgi') ) {
			$this->request->setParam ("enviroment", "http");
			return new FrontControllerHTTP ();
		}
		else {
			return null;
		}
	}

	/**
	 * Para no romper todas las acciones se establece el parametro nodeid
	 * solo si el array nodes es de un elemento.
	 */
	function normalizeNodesParam() {
		$nodes = $this->request->getParam('nodes');
		if (count($nodes) == 1) {
			$this->request->setParam('nodeid', $nodes[0]);
		}
	}

}
?>