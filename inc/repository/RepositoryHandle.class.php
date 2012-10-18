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
	 * Implementa la interfaz definida por IRepository
	 */
	class RepositoryHandle extends IRepository {

		/**
		 * @var array Repositorios montados
		 */
		var $_reps;


		/**
		 * Constructor
		 */
		function RepositoryHandle($key=null) {
			if ($key != M_PI) {
				die('Use $obj =& RepositoryHandle::getInstance(); for RepositoryHandle construction!');
			}
		}

		/**
		 * Obtiene una instancia del RepositoryHandle, singleton...
		 */
		function & getInstance() {
			static $instance = null;

			if (is_null($instance)) {
				$instance = new RepositoryHandle(M_PI);
			}

			return $instance;
		}

		/**
		 * Monta repositorio en el manejador.
		 * Solamente se pueden montar repositorios bajo el primer nivel,
		 * es decir, no se permiten puntos de montaje anidados.
		 *
		 * @param string vpath Punto de montaje
		 * @param object repository Instancia del repositorio a montar bajo vpath
		 * @return boolean TRUE si se monto correctamente
		 */
		function mount($vpath, &$repository) {

			$RepositoryHandle =& RepositoryHandle::getInstance();
			$reps =& $RepositoryHandle->_reps;
			$vpath = RepositoryHandle::normalizePath($vpath);

			if (!is_a($repository, 'Repository')) {
				// Necesita un repositorio como parametro
				return false;
			}

			if (isset($reps[$vpath])) {
				// Ya montado
				return true;
			}

			$depth = RepositoryHandle::_getDepth($vpath);
			if ($depth != 1) {
				// Solo se permiten repositorios bajo el primer nivel
				// No se puede montar un repositorio en la raiz
				return false;
			}

			$root =& $repository->getRoot();
			$rpath = $root->get('path');

			$data = array();
			$data['vpath'] = $vpath;
			$data['rpath'] = $rpath;
			$data['ref'] = $repository;

			$reps[$vpath] =& $data;
			return true;
		}

		/**
		 * desmonta un repositorio
		 *
		 * @param string vpath Punto de montaje a desmontar
		 * @return boolean TRUE si se desmonto correctamente
		 */
		function umount($vpath) {

			$RepositoryHandle =& RepositoryHandle::getInstance();
			$reps =& $RepositoryHandle->_reps;
			$vpath = RepositoryHandle::normalizePath($vpath);

			if (isset($reps[$vpath])) {
				unset($reps[$vpath]);
			}

			return true;
		}

		/**
		 * Obtiene la informacion que guarda el manejador de repositorios
		 * a traves del punto de montaje.
		 * Se devuelve un array con tres elementos, el punto de montaje,
		 * el XPath real del repostorio en XIMDEX y una instancia del repositorio.
		 *
		 * @param string vpath Punto de montaje
		 * @return array Array con la informacion
		 */
		function & _getDataByPath($vpath) {

			$RepositoryHandle =& RepositoryHandle::getInstance();
			$vpath = RepositoryHandle::normalizePath($vpath);

			$keys = array_keys($RepositoryHandle->_reps);
			$i = 0;
			$depth = -1;
			$total = count($keys);
			$data = null;

			while ($i < $total) {

				$vkey = $keys[$i];

				if ($vkey != '/') {

					$regex = "#^$vkey#m";
					$_depth = RepositoryHandle::_getDepth($vkey);

					// Se asegura que se obtendra la coincidencia mas larga con la ayuda de $depth
					if (preg_match($regex, $vpath) > 0 && $_depth > $depth) {
						$data = $RepositoryHandle->_reps[$vkey];
						$depth = $_depth;
					}
				}
				$i++;
			}

//			if (is_null($data)) $data = $RepositoryHandle->_reps['/'];

			return $data;
		}

		/**
		 * Obtiene una referencia al repositorio que representa una ruta virtual.
		 *
		 * @param string vpath Ruta virtual de un recurso
		 * @return object Instancia del repositorio montado
		 */
		function & getRepository($vpath) {

			$RepositoryHandle =& RepositoryHandle::getInstance();
			$data =& $RepositoryHandle->_getDataByPath($vpath);
			$rep = null;
			if (is_null($data)) return $rep;
			$rep =& $data['ref'];
			return $rep;
		}

		/**
		 * Devuelve el XPath real de un elemento en XIMDEX a traves
		 * del path virtual que lo representa en el manejador de repostitorios.
		 *
		 * @param string vpath Path virtual en el manejador
		 * @return string XPath real en XIMDEX
		 */
		function pathInRepository($vpath) {

			$RepositoryHandle =& RepositoryHandle::getInstance();
			$data =& $RepositoryHandle->_getDataByPath($vpath);
			$rpath = null;
			if (is_null($data)) return $rpath;

			$hnd_path = $data['vpath'];
			$rep_path = $data['rpath'];

			$rpath = preg_replace("#^($hnd_path){1}#", $rep_path, $vpath, 1);
			$rpath = RepositoryHandle::normalizePath($rpath);

			return $rpath;
		}

		/**
		 * Devuelve un array donde cada elemento es un recurso de un path
		 *
		 * @param string path Ruta a transformar
		 * @param boolean join Si es FALSE se devuelve un array con un nombre de recurso en cada elemento, si es TRUE se devuelve un string con el path
		 * @param boolean escape Si es TRUE escapa los slashes
		 * @param boolean slash Si es TRUE se concatena el slash en cada recurso
		 * @return array
		 */
		function normalizePath($path, $join = true, $slash = true, $escape = false) {

			$path = str_replace('//', '/', $path);

			if ($escape) {
				$delimiter = "\/";
			} else {
				$delimiter = "/";
			}

			if ($path != '/') {

				//$path = RepositoryHandle::unslashPath($path);
				if ($path[strlen($path)-1] == '/') {
					$path = substr($path, 0, strlen($path)-1);
				}

				if ($path[0] == '/') {
					$path = substr($path, 1, strlen($path)-1);
				}

				$path_l = explode("/", $path);

				foreach($path_l as $p) {
					if ($slash)
						$ret[] = $delimiter . $p;
					else
						$ret[] = $p;
				}
			} else {
				$ret[] = $delimiter;
			}

			if ($join) $ret = join($ret);
			return $ret;
		}

		/**
		 * Obtiene la profundidad de una determinada ruta.
		 *
		 * @param string path
		 * @return string
		 */
		function _getDepth($path) {

			if ($path == '/') {
				return 0;
			} else {
				$p = RepositoryHandle::normalizePath($path, false, false);
				return count($p);
			}
		}


		// ==================== API ====================


		/**
		 * Obtiene un objeto NodeEntity a partir de una ruta en el manejador.
		 *
		 * @param string vpath Ruta en el manejador
		 * @return object Objeto NodeEntity
		 */
		function & read($vpath) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return $rep;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$entity =& $rep->read($rpath);
			return $entity;
		}

		/**
		 * Devuelve si el recurso indicado existe en un repositorio.
		 *
		 * @param string vpath Ruta virtual al recurso.
		 * @return boolean
		 */
		function exists($vpath) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->exists($rpath);
			return $ret;
		}

		/**
		 * Devuelve TRUE si el recurso indicado representa a un contenedor.
		 */
		function isDir($vpath) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->isDir($rpath);
			return $ret;
		}

		/**
		 * Devuelve TRUE si el recurso indicado representa a un fichero.
		 */
		function isFile($vpath) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->isFile($rpath);
			return $ret;
		}

		/**
		 * Devuelve TRUE si el usuario indicado puede leer el recurso.
		 */
		function isReadable($vpath, $userName) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->isReadable($rpath, $userName);
			return $ret;
		}

		/**
		 * Devuelve TRUE si el usuario indicado puede escribir en el recurso.
		 */
		function isWritable($vpath, $userName) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->isWritable($rpath, $userName);
			return $ret;
		}

		/**
		 * No implementado
		 */
		function search() {
			die('RepositoryHandle::search() Not imlemented');
		}

		/**
		 * No implementado
		 */
		function view() {
			die('RepositoryHandle::view() Not imlemented');
		}


		// ===== Operaciones con nodos =====


		/**
		 * Crea un nuevo recurso en un repositorio.
		 *
		 * @param string vpath Ruta virtual del nuevo recurso
		 * @param string content Contenido que se le asignara al nuevo recurso
		 * @param string userName Nombre del usuario que realiza la opreacion
		 * @return int Si es negativo es un codigo de error, si no es el ID del nuevo recurso
		 */
		function append($vpath, $content, $userName) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);

			$entity = new NodeEntity_File($rpath);
			$entity->set('name', basename($rpath));
			$entity->set('path', $rpath);
			$entity->setContent($content);

			$ret = $rep->append($entity, $userName, null);
			return $ret;
		}

		/**
		 * Crea un nuevo contenedor en un repositorio.
		 *
		 * @param string vpath Ruta virtual del nuevo contenedor
		 * @param string userName Nombre del usuario que realiza la opreacion
		 * @return int Si es negativo es un codigo de error, si no es el ID del nuevo contendor
		 */
		function mkdir($vpath, $userName, $mode=null) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);

			$entity = new NodeEntity_Dir($rpath);
			$entity->set('name', basename($rpath));
			$entity->set('path', $rpath);

			$ret = $rep->mkdir($entity, $userName, $mode);
			return $ret;
		}

		/**
		 * Actualiza un recurso en un repositorio.
		 *
		 * @param string vpath Ruta virtual del nuevo contenedor
		 * @param string userName Nombre del usuario que realiza la opreacion
		 * @return int Si es negativo es un codigo de error, si no es el ID del recurso actualizado
		 */
		function update($vpath, $userName, $mode=null) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->update($rpath, $userName, $mode);
			return $ret;
		}

		/**
		 * Elimina un recurso de un repostorio.
		 *
		 * @param string vpath Ruta virtual del recurso
		 * @return boolean
		 */
		function delete($vpath, $userName) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->delete($rpath, $userName);
			return $ret;
		}

		/**
		 * Copia un recurso
		 *
		 * @param string source Ruta virtual del origen
		 * @param string dest Ruta virtual del destino
		 * @param string Nombre del usuario que realiza la operacion
		 * @param object copied Referencia al nuevo recurso creado
		 * @return int Si es negativo es un codigo de error
		 */
		function copy($source, $dest, $userName, &$copied) {

			$rep_source =& RepositoryHandle::getRepository($source);
			$rep_dest =& RepositoryHandle::getRepository($dest);

			if (is_null($rep_source) || is_null($rep_dest)) return false;

			$rsource = RepositoryHandle::pathInRepository($source);
			$rtarget = RepositoryHandle::pathInRepository($dest);

			if ($rep_source === $rep_dest) {

				// Se copia un elemento en el mismo backend
				$ret = $rep_source->copy($rsource, $rtarget, $userName, $copied);

			} else {

				// Se copia un elemento a un backend distinto

				if ($rep_source->isDir($rsource)) {

					$ret = RepositoryHandle::mkdir($dest, $userName, null);

					/* Recursivo */
					$source_dir = RepositoryHandle::read($source);

					if (!is_null($source_dir)) {
						$col = $source_dir->getCollection();
						foreach ($col as $item) {

							$item = $rep_source->read($item);
							$name = $item->get('name');

							$sourceItem = RepositoryHandle::normalizePath("$source/$name");
							$targetItem = RepositoryHandle::normalizePath("$dest/$name");

							$ret = RepositoryHandle::copy($sourceItem, $targetItem, $userName, $copied);
						}
					}
					/* Recursivo */

				} else {

					$entity = $rep_source->read($rsource);
					$content = $entity->getContent();
					$ret = RepositoryHandle::append($dest, $content, $userName);
				}

				$copied = RepositoryHandle::read($dest);
			}

			return $ret;
		}

		/**
		 * Mueve un recurso
		 *
		 * @param string source Ruta virtual del origen
		 * @param string dest Ruta virtual del destino
		 * @param string Nombre del usuario que realiza la operacion
		 * @param object moved Referencia al recurso movido
		 * @return int Si es negativo es un codigo de error
		 */
		function move($source, $dest, $userName, &$moved) {

			$rep_source =& RepositoryHandle::getRepository($source);
			$rep_dest =& RepositoryHandle::getRepository($dest);

			if (is_null($rep_source) || is_null($rep_dest)) return false;

			$rsource = RepositoryHandle::pathInRepository($source);
			$rtarget = RepositoryHandle::pathInRepository($dest);

			if ($rep_source === $rep_dest) {

				// Se copia un elemento en el mismo backend
				$ret = $rep_source->move($rsource, $rtarget, $userName, $moved);

			} else {

				// Se mueve un elemento a un backend distinto
				$ret = RepositoryHandle::copy($source, $dest, $userName, $moved);
	//			$ret = $ret > 0 ? true : false;

				if ($ret > 0) {
					// Si la copia es correcta se elimina el origen...
					$ret = RepositoryHandle::delete($source, $userName);
					$moved = RepositoryHandle::read($dest);
	//				$ret = $ret > 0 ? true : false;
					// Si no se pudo eliminar el origen... ¿Se elimina la copia?...
					if ($ret < 0) RepositoryHandle::delete($dest, $userName);
				}
			}

			return $ret;
		}

		/**
		 * Renombra un recurso
		 *
		 * @param string vpath Ruta virtual del recurso
		 * @param string newName Nuevo del recurso
		 * @param string userName Nombre del usuario que realiza la operacion
		 * @return int Si es un valor negativo es un codigo de error
		 */
		function rename($vpath, $newName, $userName) {

			$rep =& RepositoryHandle::getRepository($vpath);
			if (is_null($rep)) return false;

			$rpath = RepositoryHandle::pathInRepository($vpath);
			$ret = $rep->rename($rpath, $newName, $userName);
			return $ret;
		}


	}

?>
