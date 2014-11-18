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




if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

if (!defined('XIMDEX_XVFS_PATH'))
	define('XIMDEX_XVFS_PATH', XIMDEX_ROOT_PATH . "/inc/xvfs");

require_once(XIMDEX_ROOT_PATH . '/inc/persistence/XSession.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');

require_once XIMDEX_XVFS_PATH . '/entities/XVFS_Entity_Dir.class.php';
require_once XIMDEX_XVFS_PATH . '/entities/XVFS_Entity_File.class.php';
require_once XIMDEX_XVFS_PATH . '/entities/XVFS_Entity_Link.class.php';
require_once(XIMDEX_XVFS_PATH . '/logger/XVFS_Log.class.php');

// Estas constantes de error coinciden con las de Repository
define('XVFS_NONE',					 1);		// Sin errores (TRUE)
define('XVFS_INVALID_ROOT',			-1);		// La raiz del respositorio no es valida: No existe o no es directorio
define('XVFS_NOT_EXISTS',			-2);		// El nodo indicado no existe
define('XVFS_NOT_IN_REP',			-3);		// El nodo indicado no existe en el repositorio
define('XVFS_EXISTS',				-4);		// El nodo indicado ya existe en el repositorio
define('XVFS_NOT_DIR',				-5);		// El nodo no es un directorio
define('XVFS_NOT_FILE',				-6);		// El nodo no es un fichero
define('XVFS_NOT_READABLE',			-7);		// El nodo no se puede leer
define('XVFS_NOT_WRITABLE',			-8);		// El nodo no se puede escribir
define('XVFS_INFERE_TYPE',			-8);		// No fue posible inferir el tipo del nodo
define('XVFS_PARENT_NOT_FOUND',		-9);		// No se encuentra el padre del nodo indicado
define('XVFS_NO_NODE_NAME',			-10);		// No se proporciono el nombre del nodo
define('XVFS_NO_SOURCE',			-11);		// No existe el origen indicado
define('XVFS_INVALID_SOURCE',		-12);		// El origen indicado no es valido
define('XVFS_NO_TARGET',			-13);		// No existe el contenedor de destino
define('XVFS_INFERE_ERROR',			-14);		// Error en la inferencia de NodeTypes, seguramente no es un nodo permitido
define('XVFS_BASEIO_ERROR',			-15);		// Error producido por BaseIO
define('XVFS_IS_VIRTUAL',			-16);		// La ruta o la entidad es virtual
define('XVFS_UNKNOWN',				-1000);		// Error desconocido


/**
 *  @brief Class responsible for the start-up of the XVFS system.
 *
 *  XVFS main class.
 */

class XVFS {

	/**
	 *  Tree var who contains all information about tree path.
	 *
	 *  @access private
	 *  @var _tree
	 */
	var $_tree;


	/**
	 *  Constructor sets up {@link $_tree}
	 */
	function XVFS($key = false) {
        error_log( '**XVFS**') ;
		// to ensure singleton.
		if ($key != M_PI) {
			die('Use $obj =& XVFS::getInstance(); for XVFS construction!');
		}

		$this->_tree = NULL;
		// normal constructor
		//$this->_tree = new Tree();
		XSession::start('XVFS_SESSION');
	}


	/**
	 *  GetInstance, return only one instance of the object. (Singleton implementation)
	 *
	 *  @return object An instance of XVFS.
	 */
	static function & getInstance() {
		//XVFS_Log::debug("XVFS::getInstance($name)");

		static $instance = null;

		if (is_null($instance)) {
			$instance = new XVFS(M_PI);
		}

		return $instance;
	}

	/**
	 * Monta un una ruta en el XVFS.
	 * Se realizan varias comprobaciones antes de montar la ruta.
	 *
	 * - El punto de montaje no se debe estar ya montado por otro recurso.
	 * - Comprueba que el nombre del punto de montaje no coincide con el nombre de otro
	 *   recurso ya existente, esto provocaria la ocultacion del recurso ya existente.
	 * - Comprueba que existen todos los recursos de niveles superiores del punto de
	 *   montaje indicado. Esto obliga a montar los recursos en orden y asegura que
	 *   no existiran inconsistencias debidas a recursos inexistentes.
	 *
	 * @param string vfspath Punto de montaje
	 * @param string uri Ruta fisica del sistema que se va a montar
	 * @return boolean TRUE si se monto correctamente
	 */
	static function mount($vfspath, $uri) {
		$xvfs =& XVFS::getInstance();
		if (!is_object($xvfs)) {
			XVFS_Log::fatal("XVFS::mount($vfspath) - No se puede montar el recurso, no se ha encontrado Backend");
		}
		
		if (!is_array($xvfs->_tree)) {
			$xvfs->_tree = array();
		}

		// parse URI
		$uri_array = parse_url($uri);
		if (!is_array($uri_array)) {
			XVFS_Log::fatal("XVFS::mount($uri) Malformed uri");
		}
		
		// Controla si ya esta montado
		if (isset($xvfs->_tree[$vfspath])) {

			$ref =& $xvfs->_tree[$vfspath]['ref'];
			$mp = $ref->getMountPoint();
			if (!isset($uri_array['path']) || empty($uri_array['path'])) {
				$uri_array['path'] = '/';
			}
			// Returns TRUE if we are mounting the same resource on the same mount point
			if ($mp == $vfspath && $ref->_be_base == $uri_array['path']) {
				XVFS_Log::warning("XVFS::mount($vfspath) - El recurso indicado ya ha sido montado.");
				return true;
			} else {
				XVFS_Log::warning("XVFS::mount($vfspath) - No se puede montar el recurso $uri en una ruta ya montada.");
				return false;
			}
		}
		
		// Comprueba que no existe ningun recurso con el mismo path, si esto sucede y se monta
		// el nuevo recurso solo seria visible el recurso montado ahora, ocultaria al existente.
		// La variable _tree no esta inicializada por defecto...
		if ($xvfs->exists($vfspath)) {
			XVFS_Log::error("XVFS::mount($vfspath) - No es posible montar el recurso, ya existe un recurso con el mismo nombre.");
			return false;
		}

		// Comprueba que existe el nivel superior al que se quiere montar
		$parent = dirname($vfspath);
		if ($vfspath != '/' && !$xvfs->exists($parent)) {
			XVFS_Log::error("XVFS::mount($vfspath) - Se deben montar o crear uno o mas recursos antes de montar la ruta $uri.");
			return false;
		}

		

		// Punto de montaje
		$uri_array['vfspath'] = XVFS::normalizePath($vfspath);

		$ret = false;

		// check by regular expression.
		if (is_array($uri_array)) {

			// Se crea el backend usando la factoria. En caso de que no se pueda obtener
			// el backend solicitado se loggea y se sale de la funcion
			$backend = @$uri_array['scheme'];
			$factory = new Factory(XIMDEX_XVFS_PATH . '/backends', 'XVFS_Backend_');
			$be = $factory->instantiate($backend, $uri_array);

			if (is_object($be)) {
				$lastError = $be->lastError;
			} else {
				$be = $factory->instantiate('connector', $uri_array);
			} 

			
			if (!is_object($be) || ($lastError['errno'] < 0)) {
				XVFS_Log::error("XVFS::mount($vfspath) - " . $factory->getError());
			} else {

				if (!is_array($xvfs->_tree)) $xvfs->_tree = array();

				$xvfs->_tree[$vfspath]['ref'] =& $be;
				$xvfs->_tree[$vfspath]['depth'] = XVFS::_getDepth($vfspath);
				$ret = true;
				XVFS_Log::debug("XVFS::mount($vfspath) - Se monto la URI correctamente.");
			}

		} else {
			//printf("XVFS::mount($vfspath) error.\n");
			XVFS_Log::error("XVFS::mount($vfspath) - La URI indicada no es correcta.");
		}
		
		return $ret;
	}

	/**
	 * Desmonta un XVFS
	 *
	 * @param string path Punto de montaje
	 */
	static function umount($path) {

		$xvfs =& XVFS::getInstance();

		if (isset($xvfs->_tree[$path])) {
			unset($xvfs->_tree[$path]);
			if (count($xvfs->_tree) == 0) $xvfs->_tree = null;
			XVFS_Log::info("XVFS::umount($path) - Se desmonto la URI correctamente.");
		}
	}

	/**
	 * Devuelve una referencia a un backend segun el path del recurso indicado.
	 *
	 * @param string path Backend path
	 * @return object
	 */
	static function & _getBackend($bpath) {
		$xvfs =& XVFS::getInstance();
		$backend = null;

		if (is_array($xvfs->_tree)) {

			$vfs_array = array_keys($xvfs->_tree);
			$i = 0;
			$depth = -1;
			$total = count($vfs_array);

			//
			// NOTE: Debe recorrer el arbol entero para que se pueda encontrar el backend correcto.
			// La asignacion del backend depende del orden que ocupa en el array.
			//
			// Si algun MP se ha montado sobre otro MP, por ejemplo: /tmp y /tmp/datos,
			// y se esta buscando el recurso /tmp/datos/fichero.txt, si se encuentra el elemento /tmp
			// en el array antes que /tmp/datos se asignara el primero, y esto provoca que el backend
			// devuelto sea incorrecto.
			//
			// Si se recorre todo el arbol la expresion regular encontrara antes o despues la ruta /tmp/datos
			//
			while ($i < $total) {

				$vfspath = $vfs_array[$i];
				
				if ($vfspath != '/') {
					$_depth = XVFS::_getDepth($vfspath);

					// Se asegura que se obtendra la coincidencia mas larga con la ayuda de $depth
					if (preg_match("#^$vfspath#m", $bpath) > 0 && $_depth > $depth) {
						$backend =& $xvfs->_tree[$vfspath]['ref'];
						$depth = $_depth;
					}
				} else {
					$backend =& $xvfs->_tree['/']['ref'];
				}
				$i++;
			}
		} 

		return $backend;
	}

	/**
	 * Obtiene los posibles puntos de montaje que pueden existir bajo
	 * un directorio concreto.
	 * Devuelve un array con todos los puntos de montaje encontrados.
	 *
	 * Es usada en el metodo read().
	 *
	 * @param string bpath Backend path bajo el cual se buscaran los puntos de montaje
	 * @return array Array con los puntos de montaje encontrados
	 */
	static function & _getMountPoints($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$ret = array();

		$xvfs =& XVFS::getInstance();
		$tree =& $xvfs->_tree;

		// Primero se ajusta la ruta base donde se buscaran los MP.
		// La busqueda se realiza mediante una expresion regular, el VFSPATH
		// encontrado en la lista de MP del objeto XVFS debe comenzar con
		// la ruta base.
		// De esta forma se considera que ese VFSPATH se ha montado bajo
		// el directorio indicado en el parametro.
		//
		// TODO: Quizas sea necesario afinar mas la definicion del nombre de un
		// recurso en la expresion regular
		$_path = str_replace('//', '/', $bpath . '/');
		$regex = "#^$_path([_a-z][_\.a-z\d]*)#im";

		// Se recorre la lista de MP montados
		foreach ($tree as $vfspath=>$be) {

			$arr_matches = null;
			$match = preg_match($regex, $vfspath, $arr_matches);

			if ($match > 0) {

				// Si se obtienen coincidencias se comprueba que no existan
				// ya en el array de resultados.
				// Se inserta la coincidencia en la expresion regular y no el
				// VFSPATH, esto es porque solo nos interesa el primer nivel
				// de la ruta.
				$match = $arr_matches[0];
				if (!in_array($match, $ret)) $ret[] = $match;
			}
		}


		return $ret;
	}

	/**
	 * Elimina slashes del inicio y final de una ruta
	 *
	 * @param string path
	 * @return string
	 */
	static function unslashPath($path) {

		if ($path != '/' || !empty($path)) {
			$matches = array();
			if (preg_match('/(.*)\/$/', $path, $matches)) {
				$path = $matches[1];
			}

			if (preg_match('/^\/(.*)/', $path, $matches)) {
				$path = $matches[1];
			}
		}

		return $path;
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
	static function normalizePath($path, $join = true, $slash = true, $escape = false) {

		$path = str_replace('//', '/', $path);

		if ($escape) {
			$delimiter = "\/";
		} else {
			$delimiter = "/";
		}

		if ($path != '/') {

			$path = XVFS::unslashPath($path);

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
	static function _getDepth($path) {

		if ($path == '/') {
			return 0;
		} else {
			$p = XVFS::normalizePath($path, false, false);
			return count($p);
		}
	}

	// ============== API ==============
	static function search($query, $rootPath='/', $descend=true) {
		$backend = XVFS::_getBackend($rootPath);
		$backends = array();
		$total = 0;
		$itemsPerPage = $query['items'];
		$requestedPage = $query['page'];
		if ($backend instanceof XVFS_Backend 
			&& $backend instanceof Backend_XVFS_interface_searcheable) {
			$ret = XVFS::count($query, $backend);
			$backends[] = array('backend' => $backend, 'count' => $ret);
			$total += $ret;
			if ($descend) {
				$mps = XVFS::_getMountPoints($rootPath);
				foreach ($mps as $mp) {
					$b = XVFS::_getBackend($mp);
					if ($backend instanceof XVFS_Backend 
						&& $backend instanceof Backend_XVFS_interface_searcheable) {
						$ret = XVFS::count($query, $b);
						$backends[] = array('backend' => $b, 'count' => $ret);
						$total += $ret;
					}
				}
			}
		}
		
		$page = $query['page'];
		$items = $query['items'];
		
		$lowLimit = $items * ($page -1);
		$highLimit = $items * ($page);
		
		$accumulated = 0;
		$accumulatedResult = NULL;
		foreach($backends as $backendInfo) {
			$beginBe = $accumulated;
			//continue empty backend search
			if ($backendInfo['count'] == 0) {
				continue;
			}
			$lowBe = $accumulated;
			$highBe = $accumulated + $backendInfo['count'];
			$accumulated = $highBe;
			
			// ---*---*--- queried segment
			// ---|---|--- backend segment
			
			//*---*-------|-----| not yet arrived 
			if ($highLimit < $lowBe) {
				continue;
			}
			
			//---*---|--*---|-------- between low
			if ($lowLimit <= $lowBe && ($highLimit >= $lowBe && $highLimit <= $highBe)) {
				//refactor $query
				$lowQuery = $lowBe - $beginBe;
				$highQuery = $highLimit - $beginBe;
				$query['low_limit'] = $lowQuery;
				$query['high_limit'] = $highQuery;
				$result = $backendInfo['backend']->search($query);
				// combine results
				$accumulatedResult = self::_combineSearchResults($accumulatedResult, $result);
				continue;
			}
			
			//------|----*--*---|-------- inside
			if ($lowLimit >= $lowBe && $highLimit <= $highBe) {
				//refactor $query
				$lowQuery = $lowLimit - $beginBe;
				$highQuery = $highLimit - $beginBe;
				$query['low_limit'] = $lowQuery;
				$query['high_limit'] = $highQuery;
				$result = $backendInfo['backend']->search($query);
				// combine results
				$accumulatedResult = self::_combineSearchResults($accumulatedResult, $result);
				break;
			}
			
			//---|--*----|---*---------- between high
			if (($lowLimit >= $lowBe && $lowLimit <= $highBe) && $highLimit > $highBe) {
				//refactor $query
				$lowQuery = $lowLimit - $beginBe;
				$highQuery = $highBe - $beginBe;
				$query['low_limit'] = $lowQuery;
				$query['high_limit'] = $highQuery;
				$result = $backendInfo['backend']->search($query);
				// combine results
				$accumulatedResult = self::_combineSearchResults($accumulatedResult, $result);
				continue;
			}
			
			//--------|------|----*---*------ Past
			if ($lowLimit > $highBe) {
				continue;
			}
		}
		$accumulatedResult['pages'] = ceil($total/$itemsPerPage);
		$accumulatedResult['page'] = $requestedPage;
		
		return $accumulatedResult;
	}
	
	static function count($query, $backend) {
		if ($backend instanceof XVFS_Backend 
			&& $backend instanceof Backend_XVFS_interface_searcheable) {
			return $backend->count($query);
		}
		return 0;
	}
	
	static function _combineSearchResults($result1, $result2) {
		if (empty($result1)) {
			return $result2;
		}
		// combine both results
		
		$result1['data'] = array_merge((array) $result1['data'], (array) $result2['data']);
		$result1['records'] = $result1['records'] + count($result2['data']);
		
		return $result1;
	}

	/**
	 * Devuelve TRUE si la entidad es un directorio.
	 *
	 * @param string bpath BackendPath
	 * @return boolean TRUE si la entidad es un directorio
	 */
	static function isDir($bpath) {
		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = !is_null($backend) ? $backend->isDir($backend->realPath($bpath)) : false;
		XVFS_Log::debug("XVFS::isDir($bpath) - ret: " . ($ret ? 'TRUE' : 'FALSE'));
		return $ret;
	}

	/**
	 * Devuelve TRUE si la entidad es un fichero
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	static function isFile($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = !is_null($backend) ? $backend->isFile($backend->realPath($bpath)) : false;
		XVFS_Log::debug("XVFS::isFile($bpath) - ret: " . ($ret ? 'TRUE' : 'FALSE'));
		return $ret;
	}

	/**
	 * Devuelve TRUE si la entidad tiene permisos de lectura
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	static function isReadable($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = !is_null($backend) ? $backend->isReadable($backend->realPath($bpath)) : false;
		XVFS_Log::debug("XVFS::isReadable($bpath) - ret: " . ($ret ? 'TRUE' : 'FALSE'));
		return $ret;
	}

	/**
	 * Devuelve TRUE si la entidad tiene permisos de escritura
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	static function isWritable($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = !is_null($backend) ? $backend->isWritable($backend->realPath($bpath)) : false;
		XVFS_Log::debug("XVFS::isWritable($bpath) - ret: " . ($ret ? 'TRUE' : 'FALSE'));
		return $ret;
	}

	/**
	 * Devuelve TRUE si la entidad existe en un backend determinado
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	static function exists($bpath) {
		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = !is_null($backend) ? $backend->exists($backend->realPath($bpath)) : false;
		XVFS_Log::debug("XVFS::exists($bpath) - ret: " . ($ret ? 'TRUE' : 'FALSE'));
		return $ret;
	}

	/**
	 * Obtiene el contenido de una entidad
	 *
	 * @param string bpath BackendPath
	 * @return string
	 */
	static function & getContent($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);

		$content = $backend->getContent($backend->realPath($bpath));
		if (is_null($content)) XVFS_Log::error("XVFS::getContent($bpath) - No se pudo obtener el contenido o se indico un directorio.");
		return $content;
	}

	/**
	 * Establece el contenido de una entidad
	 *
	 * @param string path Entidad referenciada por el backend path
	 * @param string content contenido que se asignara
	 */
	static function setContent($bpath, $content) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);

		$result = $backend->setContent($backend->realPath($bpath), $content);
		return ($result > 0 || $result == true) ? true : false;
	}

	/**
	 * Obtiene el descriptor de una entidad
	 *
	 * @param string bpath
	 * @return string
	 */
	static function getDescriptor($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$descriptor = !is_null($backend) ? $backend->getDescriptor($bpath) : false;
		if (!$descriptor) XVFS_Log::error("XVFS::getDescriptor($bpath) - No se pudo obtener el descriptor del recurso indicado.");
		return $descriptor;
	}

	/**
	 * Devuelve el tipo mime de la entidad
	 *
	 * @param string bpath
	 * @param string
	 */
	static function getMIME($bpath) {

		$bpath = XVFS::normalizePath($bpath);

		if (XVFS::isReadable($bpath)) {
			$backend = XVFS::_getBackend($bpath);
			$mime = $backend->getMIME($backend->realPath($bpath));
		} else {
			$mime = 'application/x-non-readable';
		}

//		if (!$mime) XVFS_Log::error("XVFS::getDescriptor($bpath) - No se pudo obtener el descriptor del recurso indicado.");
		return $mime;
	}

	/**
	 * Obtiene una entidad XVFS a partir de una ruta determinada
	 *
	 * @param string path Ruta en el backend de la entidad que se desea obtener
	 * @return Object Entidad XVFS_Entity o NULL si no se encuentra.
	 */
	static function & read($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$entity = $backend->read($backend->realPath($bpath));
//	error_log(strtolower(get_class($backend)).' entity '.print_r($entity,true)); 	
		// Importante
		//
		// $ret contiene los recursos bajo $bpath, pero no incluye posibles
		// puntos de montaje bajo esta ruta.
		// Se busca si existe algun MP en este path y se insertan en la coleccion
		// que devuelve Backend::read().

		if (!is_null($entity)) {
			$entity->set('backend', strtolower(get_class($backend)));
			if ($entity->get('isdir')) {
//			if (XVFS::isDir($bpath)) {
				$mp = XVFS::_getMountPoints($bpath);
				$col = $entity->get('collection');
				if (is_array($mp) && is_array($col)) {
					$col = array_merge($col, $mp);
				} elseif (is_array($mp)) {
					$col = $mp;
				}
				$entity->set('collection', $col);
			}
		}

		XVFS_Log::info("XVFS::read($bpath) - " . (is_null($entity) ? 'No se encontro el recurso indicado.' : 'ret: TRUE'));
		return $entity;
	}

	/**
	 * Escribe una entidad en un backend determinado.
	 *
	 * @param string bpath La entidad que se quiere escribir en el backend.
	 * @param string content Contenido que se asignara si se especifica.
	 * @return boolean Devuelve TRUE si se pudo escribir correctamente.
	 */
	static function append($bpath, $content=null) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = $backend->append($backend->realPath($bpath), $content);
		if ($ret > 0) {
			XVFS_Log::info("XVFS::append($bpath) - El nodo se agrego correctamente.");
		} else {
			XVFS_Log::error("XVFS::append($bpath) - Ocurrio un error al intentar agregar el nodo.");
		}
		return $ret;
	}

	/**
	 * Actualiza el contenido de una entidad en un backend determinado.
	 *
	 * @param string bpath La entidad que se quiere escribir en el backend.
	 * @param string content Contenido que se asignara si se especifica.
	 * @return boolean Devuelve TRUE si se pudo escribir correctamente.
	 */
	static function update($bpath, $content=null) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = $backend->update($backend->realPath($bpath), $content);
		if ($ret > 0) {
			XVFS_Log::info("XVFS::update($bpath) - El nodo se actualizo correctamente.");
		} else {
			XVFS_Log::error("XVFS::update($bpath) - Ocurrio un error al intentar actualizar el nodo.");
		}
		return $ret;
	}

	/**
	 * Crea un nuevo directorio en el backend.
	 * Devuelve TRUE si se pudo crear el directorio
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 *
	 * TODO: El parametro mode no se esta pasando a write, lo hago opcional
	 */
	static function mkdir($bpath, $mode=null) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = $backend->mkdir($backend->realPath($bpath), $mode);
		if ($ret > 0 || $ret == true) {
			XVFS_Log::info("XVFS::mkdir($bpath) - El directorio se creo correctamente.");
		} else {
			XVFS_Log::error("XVFS::mkdir($bpath) - Ocurrio un error al intentar crear el directorio.");
		}
		return $ret;
	}

	/**
	 * Elimina una entidad de un backend
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	static function delete($bpath) {

		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = $backend->delete($backend->realPath($bpath));
		if ($ret > 0) {
			XVFS_Log::info("XVFS::delete($bpath) - El nodo se elimino correctamente.");
		} else {
			XVFS_Log::error("XVFS::delete($bpath) - Ocurrio un error al intentar eliminar el nodo.");
		}
		return $ret > 0 ? true : false;
	}

	/**
	 * Renombra un nodo
	 *
	 * @param string bpath Backendpath de la entidad a renombrar
	 * @param string newName Nuevo nombre que se le asignara a la entidad
	 * @return boolean
	 */
	static function rename($bpath, $newName) {
		$bpath = XVFS::normalizePath($bpath);
		$backend = XVFS::_getBackend($bpath);
		$ret = $backend->rename($backend->realPath($bpath), $backend->realPath($newName));
//		$ret = $ret > 0 ? true : false;
		if ($ret > 0) {
			XVFS_Log::info("XVFS::rename($bpath, $newName) - El nodo se renombro correctamente.");
		} else {
			XVFS_Log::error("XVFS::rename($bpath, $newName) - Ocurrio un error al intentar renombrar el nodo.");
		}
		return $ret > 0 ? true : false;
	}

	/**
	 * Mueve una entidad a una ruta distinta.
	 * Devuelve TRUE si se pudo mover el objeto.
	 *
	 * @param string source Backendpath de la entidad origen
	 * @param string target Backendpath de la entidad destino
	 * @return boolean
	 */
	static function move($source, $target) {

		$source = XVFS::normalizePath($source);
		$target = XVFS::normalizePath($target);

		$sbe = XVFS::_getBackend($source);
		$tbe = XVFS::_getBackend($target);

		if ($sbe->getMountPoint() == $tbe->getMountPoint()) {

			// Se mueve un elemento en el mismo backend
			$ret = $sbe->move($sbe->realPath($source), $sbe->realPath($target));
//			$ret = $ret > 0 ? true : false;

		} else {

			// Se mueve un elemento a un backend distinto
			$ret = XVFS::copy($source, $target);
//			$ret = $ret > 0 ? true : false;

			if ($ret > 0) {
				// Si la copia es correcta se elimina el origen...
				$ret = XVFS::delete($source);
//				$ret = $ret > 0 ? true : false;
				// Si no se pudo eliminar el origen... �Se elimina la copia?...
				if ($ret < 0) XVFS::delete($target);
			}
		}

		if ($ret > 0) {
			XVFS_Log::info("XVFS::move($source, $target) - El recurso se movio correctamente.");
		} else {
			XVFS_Log::error("XVFS::move($source, $target) - El recurso no se pudo mover.");
		}
		return $ret > 0 ? true : false;
	}

	/**
	 * Copia una entidad a la ruta indicada.
	 * Devuelve TRUE si se pudo copiar la entidad correctamente.
	 *
	 * TODO: solucionar errores retornados en la copia entre distintos backends
	 * Cuando se copian directorios entre distintos backends se llama a esta funcion
	 * recursivamente para copiar todo el arbol elemento a elemento.
	 * Si la copia de uno de los elementos hijos de la raiz falla, el proceso de copia
	 * debe continuar y copiar el arbol lo mejor que pueda.
	 * �Como informar de los errores producidos en la copia de elementos hijos?
	 * �El resultado final debe ser un error?
	 *
	 * @param string source Backendpath de la entidad origen
	 * @param string target Backendpath de la entidad destino
	 * @return boolean
	 */
	static function copy($source, $target) {

		$source = XVFS::normalizePath($source);
		$target = XVFS::normalizePath($target);

		$sbe = XVFS::_getBackend($source);
		$tbe = XVFS::_getBackend($target);

		if ($sbe->getMountPoint() == $tbe->getMountPoint()) {

			// Se copia un elemento en el mismo backend
			$ret = $sbe->copy($sbe->realPath($source), $sbe->realPath($target));
//			$ret = $ret > 0 ? true : false;

		} else {

			// Se copia un elemento a un backend distinto

			if ($sbe->isDir($source)) {

				$ret = $tbe->mkdir($tbe->realPath($target));
				/* Recursivo */
				$source_dir = $sbe->read($sbe->realPath($source));

				if (!is_null($source_dir)) {
					$col = $source_dir->getCollection();
					foreach ($col as $item) {
						$name = basename($item);
						$targetItem = XVFS::normalizePath("$target/$name");
						$ret = XVFS::copy($item, $targetItem);
					}
				}
				/* Recursivo */

			} else {

				$ret = $tbe->append($tbe->realPath($target), $sbe->getContent($sbe->realPath($source)));
			}
		}

		if ($ret > 0) {
			XVFS_Log::info("XVFS::copy($source, $target) - El recurso se copio correctamente.");
		} else {
			XVFS_Log::error("XVFS::copy($source, $target) - El recurso no se pudo copiar.");
		}
		return $ret;
	}

}

?>