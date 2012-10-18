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



require_once(XIMDEX_ROOT_PATH . '/inc/repository/IRepository.iface.php');


/**
 * @abstract
 *
 * Esta clase extiende IRepository pero no implementa la gestion de nodos,
 * solo la gestion de mensajes de error.
 */
class Repository extends IRepository {

	/**
	 * @var object Array que contiene referencias a los nodos hijos
	 */
	var $_childrens;

	/**
	 * @var object Raiz del repositorio, de tipo Node
	 */
	var $_root;

	/**
	 * @var array Coleccion de errores producidos
	 */
	var $_messages;

	/**
	 * Ultimo codigo de error producido por BaseIO
	 */
	var $_baseio_error;


	/**
	 * Constructor.
	 * Es posible construir el repositorio a traves de un Path, un idNodo o un objeto NodeEntity.
	 *
	 * NOTE: La mayoria de los metodos de esta clase aceptan, como parametro identificador del nodo, un idNodo, un Path o un objeto NodeEntity.
	 *
	 * @param mixed root Identificador del nodo raiz del repositorio
	 */
	function Repository($root) {

		$this->_root =& $root;
		$this->_messages = array();
	}

	/**
	 * Inserta un nuevo error en la coleccion
	 *
	 * @param int error Codigo de error
	 * @return array Codigo de error y su mensaje de texto
	 */
	function addError($error) {
		$err = array();
		$err['errno'] = $error;
		$err['error'] = $this->getErrorString($error);
		$this->_messages[] = $err;
		return $err;
	}

	/**
	 * Devuelve la coleccion de errores
	 *
	 * return array
	 */
	function getErrors() {
		return $this->_messages;
	}

	/**
	 * Devuelve el ultimo error producido
	 *
	 * @return array
	 */
	function getLastError() {
		$last = end($this->_messages);
		if (!is_array($last)) {
			$this->addError(REP_NONE);
			$last = end($this->_messages);
		}
		return $last;
	}

	/**
	 * Limpia la coleccion de errores
	 */
	function clearErrors() {
		unset($this->_messages);
		$this->_messages = array();
	}


	/**
	 * Obtiene los mensajes asociados a los codigos de error de BaseIO
	 */
	function _getBaseIOError($error) {
		switch ($error) {
			case ERROR_NO_PERMISSIONS:
				$msg = 'No tiene permisos para realizar la operación.';
				break;
			case ERROR_INCORRECT_DATA:
				$msg = 'La información enviada a BaseIO no es correcta.';
				break;
			case ERROR_NOT_REACHED:
				$msg = 'ERROR_NOT_REACHED';
				break;
			case ERROR_NOT_ALLOWED:
				$msg = 'El tipo de nodo especificado no está permitido en este directorio.';
				break;
			default:
				$msg = 'La operación se realizó correctamente.';
		}

		return $msg;
	}

	/**
	 * Traduce el codigo de error en un mensaje de texto.
	 *
	 * @param int error Codigo de error
	 * @return string
	 */
	function getErrorString($error) {

		switch($error) {
			case REP_NONE:
				$msg = 'Operacion realizada correctamente.';
				break;
			case REP_INVALID_ROOT:
				$msg = 'La raiz del respositorio no es valida: No existe el nodo o no es un directorio.';
				break;
			case REP_NOT_EXISTS:
				$msg = 'El nodo indicado no existe.';
				break;
			case REP_NOT_IN_REP:
				$msg = 'El nodo indicado no existe en el repositorio.';
				break;
			case REP_EXISTS:
				$msg = 'El nodo indicado existe en el repositorio.';
				break;
			case REP_NOT_DIR:
				$msg = 'El nodo no es un directorio.';
				break;
			case REP_NOT_FILE:
				$msg = 'El nodo no es un fichero.';
				break;
			case REP_NOT_READABLE:
				$msg = 'No tiene permisos de lectura.';
				break;
			case REP_NOT_WRITABLE:
				$msg = 'No tiene permisos de escritura.';
				break;
			case REP_INFERE_TYPE:
				$msg = 'No fue posible inferir el tipo del nodo.';
				break;
			case REP_PARENT_NOT_FOUND:
				$msg = 'No se encuentra el padre del nodo indicado.';
				break;
			case REP_NO_NODE_NAME:
				$msg = 'No se proporciono el nombre del nodo.';
				break;
			case REP_NO_SOURCE:
				$msg = 'No existe el origen indicado.';
				break;
			case REP_INVALID_SOURCE:
				$msg = 'El origen indicado no es valido.';
				break;
			case REP_NO_TARGET:
				$msg = 'No existe el contenedor de destino.';
				break;
			case REP_INFERE_ERROR:
				$msg = 'No fue posible inferir el tipo del nodo indicado.';
				break;
			case REP_BASEIO_ERROR:
				$msg = $this->_getBaseIOError($this->_baseio_error);
				break;
			default: /*REP_UNKNOWN*/
				$msg = 'Error desconocido.';
				break;
		}

		return $msg;
	}


	// Aporta metodos para iterar sobre el repositorio


	function & next() {
	}

	function reset($entity) {
	}

}

?>
