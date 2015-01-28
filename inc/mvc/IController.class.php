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



require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Response.class.php');

/**
 *
 * @brief Controller pseudo abstract class for Actions, Applications and Controllers
 *
 * Pseudo abstract class who serves as base for Actions, Applications and Controllers, provide
 * methods to manage the request object and errors
 *
 */

class IController {
	/**
	 * Objeto Request para almacenar par�metros de petici�n
	 * @var unknown_type
	 */
	var $request;
	/**
	 *
	 * @var unknown_type
	 */
	var $response;
	/**
	 * Indica si se ha producido algun error
	 * @var unknown_type
	 */
	var $hasError;
	/**
	 * Mensaje de error
	 * @var unknown_type
	 */
	var $msgError;
	/**
	 *
	 * @var unknown_type
	 */
	var $messages;

    /**
     * Constructor
     * @return unknown_type
     */
    function __construct() {
    	$this->hasError = false;
    	$this->messages = new \Ximdex\Utils\Messages();
		$this->request = new Request();
		$this->response = new Response();
    }

	/**
	 * Setter
	 * @param $request
	 * @return unknown_type
	 */
	function setRequest ($request) {
		$this->request = $request;
	}

	/**
	 * TODO: Cambiar toda la gesti�n de errores en base a variable booleana + array simple por el objeto messages
	 * Getter
	 * @return unknown_type
	 */
	function hasError () {
		if (isset ($this->hasError)) $this->hasError;
	}
	/**
	 *
	 * @return unknown_type
	 */
	function getMsgError () {
		if (isset ($this->msgError)) $this->msgError;
	}

	/**
	 * Registra un error
	 * @param $msg
	 * @param $module
	 * @return unknown_type
	 */
	function _setError ($msg, $module) {
		$this->hasError = true;
		$this->msgError = $msg;
		// Registra un apunte en el log
		XMD_Log::error($msg);
	}
}
?>