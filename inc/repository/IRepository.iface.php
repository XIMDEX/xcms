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



	// Estas constantes de error coinciden con las de XVFS
	define('REP_NONE',					 1);		// Sin errores (TRUE)
	define('REP_INVALID_ROOT',			-1);		// La raiz del respositorio no es valida: No existe o no es directorio
	define('REP_NOT_EXISTS',			-2);		// El nodo indicado no existe
	define('REP_NOT_IN_REP',			-3);		// El nodo indicado no existe en el repositorio
	define('REP_EXISTS',				-4);		// El nodo indicado ya existe en el repositorio
	define('REP_NOT_DIR',				-5);		// El nodo no es un directorio
	define('REP_NOT_FILE',				-6);		// El nodo no es un fichero
	define('REP_NOT_READABLE',			-7);		// El nodo no se puede leer
	define('REP_NOT_WRITABLE',			-8);		// El nodo no se puede escribir
	define('REP_INFERE_TYPE',			-8);		// No fue posible inferir el tipo del nodo
	define('REP_PARENT_NOT_FOUND',		-9);		// No se encuentra el padre del nodo indicado
	define('REP_NO_NODE_NAME',			-10);		// No se proporciono el nombre del nodo
	define('REP_NO_SOURCE',				-11);		// No existe el origen indicado
	define('REP_INVALID_SOURCE',		-12);		// El origen indicado no es valido
	define('REP_NO_TARGET',				-13);		// No existe el contenedor de destino
	define('REP_INFERE_ERROR',			-14);		// Error en la inferencia de NodeTypes, seguramente no es un nodo permitido
	define('REP_BASEIO_ERROR',			-15);		// Error producido por BaseIO
	define('REP_IS_VIRTUAL',			-16);		// La ruta o la entidad es virtual
	define('REP_UNKNOWN',				-1000);		// Error desconocido


	require_once(XIMDEX_ROOT_PATH . '/inc/repository/NodeEntity.class.php');


	/**
	 * Clase abstracta o interface, unicamente define la API a implementar
	 */
	class IRepository {

		function & read($node) {
		}

		function exists($entity) {
		}

		function isDir($entity) {
		}

		function isFile($entity) {
		}

		function isReadable($entity, $userName) {
		}

		function isWritable($entity, $userName) {
		}

		function search() {
		}

		function view() {
		}

		// ===== Operaciones con nodos =====

		function append($entity, $userName, $mode) {
		}

		function mkdir($entity, $userName, $mode) {
		}

		function update($entity, $userName, $mode) {
		}

		function delete($entity, $userName) {
		}

		function copy($source, $dest, $userName, &$copied) {
		}

		function move($source, $dest, $userName, &$moved) {
		}

		function rename($entity, $newName, $userName) {
		}

	}

?>