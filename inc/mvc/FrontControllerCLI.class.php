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



require_once(XIMDEX_ROOT_PATH . '/inc/mvc/FrontController.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/ParamsCLI.class.php');
/**
 * 
 * @brief FrontController for the cli interface
 * 
 * FrontController for the cli interface, provide specific methods to read 
 * the parameters from cli and launches the ApplicationController to compose
 * the cli interface
 *
 */
class FrontControllerCLI extends FrontController {

	/**
	 * Verifica y parsea los parámetros e instancia al ApplicationController
	 * (non-PHPdoc)
	 * @see inc/mvc/FrontController#dispatch()
	 */
	function dispatch () {
		// Comprueba si la URL de acceso coincide con UrlRoot
		if ($this->_parseParams ()) {
			// Llama al ApplicationController
			$appController = new ApplicationController;
			$appController->setRequest ($this->request);
			$appController->compose ();
			$this->hasError = $appController->hasError ();
			$this->msgError = $appController->getMsgError ();
		}
	}

	/**
	 * Parsea los parámetros
	 * @return unknown_type
	 */
	function _parseParams () {
		$parameterCollector = new ParamsCLI ($_SERVER["argc"], $_SERVER["argv"]);
		if ($parameterCollector->messages->count (MSG_TYPE_ERROR)> 0) {
			$parameterCollector->messages->displayRaw ();
			return false;
		}
		else {
			// Sanitize Params
			foreach( $parameterCollector->getParametersArray() as $idx=>$data) {
				$sanitized_idx = str_replace('--', '', $idx);
				$sanitized_array[$sanitized_idx] = $data; 
			}
			// Copia los parámetros a $this->request
			$this->copyRequest($sanitized_array);
			return true;
		}
	}

	/**
	 * Copia los parámetros al objeto $this->request
	 * @param $array
	 * @return unknown_type
	 */
	function copyRequest ($array) {
		foreach ($array as $key => $value) {
			$this->request->setParam ($key, $value);
		}
		
		$this->normalizeNodesParam();
	}
}
?>