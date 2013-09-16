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



require_once(XIMDEX_XVFS_PATH . '/XVFS_Backend.class.php');
require_once(XIMDEX_XVFS_PATH . '/backends/XVFS_VirtualPathAdapter.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/Repository_XNodes.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_interface.class.php');


// Constants
define('DEFAULT_USERNAME', 'ximdex');

if (!defined('ROOT_NODE')) {
	define('ROOT_NODE', 1);
}
define('ROOT_NAME', 'ximDEX');
define('ROOT_DEPTH', 0);


/**
 * @brief Backend for access to ximDEX nodes.
 *
 * Implementation of ximDEX backend.
 *
 * NOTE: Las operaciones que crean entidades no devuelven un error si el recurso
 * indicado ya existe en el repositorio, sino que devuelven un codigo indicando que
 * el metodo se ejecuto correctamente.
 * Esto es un workaround necesario cuando se llama al backend desde webDAV.
 *
 */
class XVFS_Backend_xnodes
	extends XVFS_Backend
	implements Backend_XVFS_interface, Backend_XVFS_interface_searcheable {

	/**
	 * @var object Repositorio de nodos
	 */
	var $_repository;

	/**
	 * @var object Transformaciones de entidades virtuales
	 */
	var $_virtual;
	
	var $lastError;
	var $errors;


	/**
	 * Constructor
	 * Recibe como parametro la definicion del backend, ruta fisica,
	 * punto de montaje y tipo de backend
	 *
	 *  @param $uri array Definicion del backend
	 */
	function XVFS_Backend_xnodes($uri) {

		parent::XVFS_Backend($uri);

		$rpath = $this->realPath($uri['vfspath']);
		$this->_repository = new Repository_XNodes($rpath);

		$this->_virtual =& XVFS_VirtualPathAdapter::getInstance();

		$this->lastError = $this->_repository->getLastError();
		$this->errors = $this->_repository->getErrors();
	}

	/**
	 * Devuelve el usuario que inicio sesion en el sistema
	 * @return string Nombre del usuario
	 */
	function _getUser() {

		// TODO: El atributo _uri conserva los elementos user y pass, usarlos?
		$user_name = XSession::get('user');
		if (is_null($user_name)) $user_name = DEFAULT_USERNAME;
		return $user_name;
	}

	/**
	 * Metodo analogo a XVFS_Backend_xnodes::read() para uso interno de la clase.
	 * Obtiene una entidad NodeEntity a traves de un backendpath verificando si
	 * es una entidad virtual o no.
	 *
	 * @param string bpath
	 * @return object
	 */
	function & _getEntity($bpath) {

		$nodeEntity = null;

		$nodeEntity = $this->_virtual->getEntity($bpath);

		if (is_null($nodeEntity)) {

			$this->_repository->clearErrors();
			$nodeEntity = $this->_repository->read($bpath);
		}

		return $nodeEntity;
	}

	/**
	 * Devuelve un array que contiene el nombre de un documento estructurado
	 * sin el sufijo de idiomas y el sufijo encontrado.
	 *
	 * @param string Nombre o path del documento
	 * @return string
	 */
	function _stripLanguageSuffix($path) {

		$regexp = '/-(id[a-z]*)$/im';
		$ret = preg_match($regexp, $path, $matches);

		$info = array();
		$info['name'] = basename($path);
		$info['suffix'] = null;

		if ($ret > 0) {
			$suffix = $matches[1];
			$info['suffix'] = $suffix;
			$info['name'] = str_replace("-$suffix", '', basename($path));
		}

		return $info;
	}


	/**
	 *  Devuelve TRUE si el recurso indicado es un directorio
	 *
	 *  @param string bpath Backendpath
	 *  @return boolean
	 */
	function isDir($bpath) {
		if ($this->_virtual->isVirtual($bpath)) {

			$entity = $this->_getEntity($bpath);
			if (is_null($entity)) return false;
			$ret = $entity->get('isdir');
		} else {

			$this->_repository->clearErrors();
			$ret = $this->_repository->isDir($bpath);
		}

		return $ret;
	}

	/**
	 * Devuelve TRUE si el recurso indicado es un fichero
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	function isFile($bpath) {

		if ($this->_virtual->isVirtual($bpath)) {

			$entity = $this->_getEntity($bpath);
			if (is_null($entity)) return false;
			$ret = $entity->get('isfile');
		} else {

			$this->_repository->clearErrors();
			$ret = $this->_repository->isFile($rpath);
		}

		return $ret;
	}

	/**
	 * Devuelve TRUE si el recurso indicado existe en el backend
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	function exists($bpath) {
		if ($this->_virtual->isVirtual($bpath)) {

			$entity = $this->_getEntity($bpath);
			$ret = (boolean) $entity;
		} else {

			$this->_repository->clearErrors();
			$ret = $this->_repository->exists($bpath);
		}

		return $ret;
	}

	/**
	 * Devuelve TRUE si el recurso indicado puede ser leido
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	function isReadable($bpath) {

		if ($this->_virtual->isVirtual($bpath)) return true;

		$user_name = XVFS_Backend_xnodes::_getUser();

		$this->_repository->clearErrors();
		$ret = $this->_repository->isReadable($bpath, $user_name);

		return $ret;
	}

	/**
	 * Devuelve TRUE si el recurso indicado se puede escribir
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	function isWritable($bpath) {
		if ($this->_virtual->isVirtual($bpath)) return true;

		$user_name = XVFS_Backend_xnodes::_getUser();

		$this->_repository->clearErrors();
		$ret = $this->_repository->isWritable($bpath, $user_name);

		return $ret;
	}

	/**
	 * Devuelve una entidad XVFS a partir del backendpath
	 *
	 * @param string bpath Backendpath
	 * @return object
	 */
	function & read($bpath) {

		$entity = null;
		$nodeEntity = $this->_getEntity($bpath);
		if (is_null($nodeEntity)) return $entity;

		if ($nodeEntity->get('islink')) {
			$entity = new XVFS_Entity_Link($bpath);
		} else if ($nodeEntity->get('isfile')) {
			$entity = new XVFS_Entity_File($bpath);
		} else if ($nodeEntity->get('isdir')) {
			$entity = new XVFS_Entity_Dir($bpath);
		}

		$entity->loadData($nodeEntity->asArray());

		$entity->set('path', $bpath);
		$entity->set('bpath', $this->pathInBackEnd($bpath));
		$entity->set('mimetype', $nodeEntity->getMIME());
		$entity->set('icon', $nodeEntity->get('icon'));
		$entity->set('descriptor', $nodeEntity->getDescriptor());

		// NOTE: En la tabla Nodes existen estos dos atributos pero parece que no son usados
		$entity->set('creationdate', time());
		$entity->set('lastmodified', time());


		if (!$nodeEntity->get('isdir')) return $entity;


		// Se cargan los hijos del nodo
		$collection = array();

		// Cualquier path considerado virtual devolvera una coleccion de hijos como paths y no como idnodes
		if ($this->_virtual->isVirtual($bpath)) {

			$col = $nodeEntity->get('collection');

			// Las entidades virtuales no tienen idNode asi pues la coleccion de
			// nodos hijos son paths reales, hay que transformarlos a backendpaths.
			foreach ($col as $rpath) {
				$collection[] = $this->pathInBackend($bpath);
			}

		} else {

			$col = $nodeEntity->getCollection();

			// El repositorio devuelve una coleccion de IDs de nodos, hay que
			// transformar estos IDs a backends paths.
			foreach ($col as $idNode) {

				$nodeEntity = $this->_repository->read($idNode);
				$rpath = $nodeEntity->get('path');
				$bpath = $this->pathInBackend($rpath);
				$collection[] = $bpath;
			}

		}

		$entity->set('collection', $collection);
		return $entity;
	}

	/**
	 * Obtiene el contenido de una entidad
	 *
	 * @param string bpath BackendPath
	 * @return string
	 */
	function & getContent($bpath) {

		$content = null;
		$entity = $this->_getEntity($bpath);

		if (is_null($entity)) return $content;
		if ($entity->get('isdir')) return $content;

		$content = $entity->getContent();
		return $content;
	}

	/**
	 * Establece el contenido de una entidad.
	 * Es un alias para update().
	 *
	 * @param string bpath Backendpath
	 * @param string content Contenido a asignar
	 * @return int Longitud de la cadena content
	 */
	function setContent($bpath, $content) {

		$ret = $this->update($bpath, $content);
		if ($ret > 0) {
			$ret = strlen($content);
		} else {
			$ret = 0;
		}

		return $ret;
	}

	/**
	 * Devuelve el descriptor de una entidad
	 *
	 * @param string bpath Backendpath
	 * @return string
	 */
	function getDescriptor($bpath) {

		$entity = $this->_getEntity($bpath);
		$descriptor = false;

		if (!is_null($entity)) $descriptor = $entity->getDescriptor();
		return $descriptor;
	}

	/**
	 * Devuelve el tipo mime de una entidad
	 *
	 * @param string bpath Backendpath
	 * @return string
	 */
	function getMIME($bpath) {

		$mime = null;
		$entity = $this->_getEntity($bpath);

		if (!is_null($entity)) $mime = $entity->getMIME();
		return $mime;
	}

	/**
	 * Escribe un directorio.
	 *
	 * @param string bpath Backendpath
	 * @return int ID del nuevo nodo o el codigo de error
	 */
	function mkdir($bpath) {

		// NOTE: No devuelve error en caso de que la coleccion exista -> webDAV!
		if ($this->exists($bpath)) return XVFS_NONE;

		if ($this->_virtual->isVirtual($bpath)) {
			XVFS_Log::debug("XVFS::mkdir($bpath) - El recurso es virtual.");
			$ret = $this->_virtualMkdir($bpath);
			if ($ret > 0) $this->_virtual->update();
			return $ret;
		}

		$username = XVFS_Backend_xnodes::_getUser();

		$entity = new NodeEntity_Dir();
		$entity->set('name', basename($bpath));
		$entity->set('path', $bpath);
		$this->_repository->clearErrors();
		$ret = $this->_repository->mkdir($entity, $username, null);

		if ($ret > 0) $this->_virtual->update();

		return $ret;
	}

	/**
	 * Escribe un fichero y opcionalmente se le asigna un contenido
	 *
	 * @param string bpath Backendpath
	 * @param string content
	 * @return int ID del nuevo nodo o el codigo de error
	 */
	function append($bpath, $content=null) {

		// NOTE: Por defecto se actualiza el recurso si este ya existe -> webDAV!
		if ($this->exists($bpath)) {
			XVFS_Log::debug("XVFS::append($bpath) - El recurso existe, se procede a actualizarlo.");
			$ret = $this->update($bpath, $content);
			return $ret;
		}

		if ($this->_virtual->isVirtual($bpath)) {
			XVFS_Log::debug("XVFS::append($bpath) - El recurso es virtual.");
			$ret = $this->_virtualAppend($bpath, $content);
			if ($ret > 0) $this->_virtual->update();
			return $ret;
		}

		$username = XVFS_Backend_xnodes::_getUser();
		if (is_null($content)) $content = "\n";

		$entity = new NodeEntity_File($bpath);
		$entity->set('name', basename($bpath));
		$entity->set('path', $bpath);

		$entity->setContent($content);
		$this->_repository->clearErrors();
		$ret = $this->_repository->append($entity, $username, 0664);

		if ($ret > 0) {
			$entity->update();
			$this->_virtual->update();
		}

		return $ret;
	}

	/**
	 * Actualiza el contenido de una entidad.
	 *
	 * @param string bpath Backendpath
	 * @param string content
	 * @return int ID del nodo actualizado o el codigo de error
	 */
	function update($bpath, $content=null) {

		if (!$this->exists($bpath)) return XVFS_NOT_EXISTS;

		if ($this->_virtual->isVirtual($bpath)) return $this->_virtualAppend($bpath, $content);

		$username = XVFS_Backend_xnodes::_getUser();
		if (is_null($content)) $content = "\n";

		$entity = $this->_getEntity($bpath);
		$entity->setContent($content);
		$this->_repository->clearErrors();
		$ret = $this->_repository->update($entity, $username, null);
		if ($ret > 0) $entity->update();

		return $ret;
	}

	/**
	 * Elimina una entidad
	 *
	 * @param string bpath Backendpath
	 * @return int ID del nodo eliminado o el codigo de error
	 */
	function delete($bpath) {

		if ($this->_virtual->isVirtual($bpath)) {
			XVFS_Log::debug("XVFS::delete($bpath) - El recurso es virtual.");
			$ret = $this->_virtualDelete($bpath);
			if ($ret > 0) $this->_virtual->update();
			return $ret;
		}

		$user_name = XVFS_Backend_xnodes::_getUser();
		$this->_repository->clearErrors();
		$ret = $this->_repository->delete($bpath, $user_name);

		if ($ret > 0) $this->_virtual->update();

		return $ret;
	}

	/**
	 * Renombra una entidad.
	 *
	 * @param string bpath BackendPath
	 * @param string newName Nuevo nombre
	 * @return int ID del nodo renombrado o el codigo de error
	 */
	function rename($bpath, $newName) {

		$userName = XVFS_Backend_xnodes::_getUser();
		$entity = $this->_getEntity($bpath);
		if (is_null($entity)) return XVFS_NOT_EXISTS;

		if ($entity->get('isvirtual')) return XVFS_IS_VIRTUAL;
		$this->_repository->clearErrors();
		$ret = $this->_repository->rename($entity, $newName, $userName);

//		NodeCache::insert($node);

		return $ret;
	}

	/**
	 * Mueve una entidad
	 *
	 * @param string source BackendPath del nodo origen
	 * @param string target BackendPath del nodo de destino
	 * @return int ID del nodo movido o el codigo de error
	 */
	function move($source, $target) {

		$this->_repository->clearErrors();

		$ret = $this->copy($source, $target);
		if ($ret < 0) {
			XVFS_Log::error("XVFS::move($bpath, $new_target) - Ocurrio un error copiando el recurso.");
			return $ret;
		}

		$ret = $this->delete($source);
		if ($ret < 0) {
			XVFS_Log::error("XVFS::move($bpath, $new_target) - Ocurrio un error eliminando el recurso.");
			return $ret;
		}

		return $ret;
	}

	/**
	 * Copia una entidad, si esta es un directorio la copia es recursiva.
	 * Cuando la copia es recursiva intenta que, ante un fallo, se copien
	 * el maximo de recursos posibles. Este comportamiento es "webDAV compliant",
	 * RFC2518 - 8.8.3 Copy for Collections.
	 *
	 * @param string source BackendPath del nodo origen
	 * @param string target BackendPath del nodo de destino
	 * @return int ID del nodo copiado o el codigo de error
	 */
	function copy($source, $target) {

		if (!$this->exists($source)) {
			XVFS_Log::error("XVFS::copy($source, $target) - El origen no existe.");
			return XVFS_NOT_EXISTS;
		}

		$e_source = $this->_getEntity($source);

		if ($e_source->get('isfile')) {

			if (is_null($e_source)) return XVFS_INVALID_SOURCE;

			$content = $this->getContent($source);

			if (!$this->exists($target)) {

				XVFS_Log::debug("XVFS::copy($source, $target) - Se procede a crear el recurso $target.");

				$target_isvirtual = $this->_virtual->isVirtual($bpath);

				// Si el fichero no existe se debe hacer una comprobacion para los StructuredDocument.
				// Estos ficheros no pueden ser renombrados si no es a traves del contenedor.
				if ($target_isvirtual && (dirname($source) == dirname($target))) {
					if (basename($source) != basename($target)) {
						XVFS_Log::warning("XVFS::copy($source, $target) - Se ha intentado renombrar un documento virtual: ABORTADO!.");
						return XVFS_IS_VIRTUAL;
					}
				} else {
					$ret = $this->append($target, $content);
				}

			} else {

				XVFS_Log::debug("XVFS::copy($source, $target) - Se procede a actualizar el recurso $target.");
				$ret = $this->update($target, $content);
			}

		} else if ($e_source->get('isdir')) {

			if (!$this->exists($target)) {
				XVFS_Log::debug("XVFS::copy($source, $target) - Se procede a crear la coleccion $target.");
				$ret = $this->mkdir($target);
			}

			$entity = $this->read($source);
			$col = $entity->get('collection');

			XVFS_Log::debug("XVFS::copy($source, $target) - Se procede a copiar recursivamente la coleccion $source.");
			foreach ($col as $bpath) {

				/* Recursivo */
				$new_target = $target . '/' . basename($bpath);
				XVFS_Log::debug("XVFS::copy($bpath, $new_target) - Se procede a copiar el recurso $new_target.");
				$ret = $this->copy($bpath, $new_target);
				/* Recursivo */

				if ($ret <= 0) {
					$error = $this->_repository->getLastError();
					$msg = sprintf('XVFS::copy(%s, %s) - [%s] %s.', $bpath, $new_target, $error['errno'], $error['error']);
					XVFS_Log::error($msg);
				}
			}
		}

		return $ret;
	}


	// ===== Operaciones con nodos virtuales =====


	/**
	 * Crea un nuevo contenedor bajo un XmlRootFolder, XimletRootFolder, ...
	 * No se permite crear directorios a niveles mas bajos.
	 *
	 * @param string rpath Ruta del nuevo directorio.
	 * @return int ID del nuevo nodo o el codigo de error generado.
	 */
	function _virtualMkdir($rpath) {

		// Se permite solo la creacion de contenedores XML, XIMLET, ...
		$parent = $this->_virtual->getEntity(dirname($rpath));
		if (is_null($parent)) return XVFS_PARENT_NOT_FOUND;
		if (!$parent->get('isvirtualroot')) return XVFS_PARENT_NOT_FOUND;

		$xmlcontainer = new NodeEntity_dir($rpath);
		$xmlcontainer->set('name', basename($rpath));
		$xmlcontainer->set('path', $rpath);
		$xmlcontainer->set('idparent', $parent->get('idnode'));

		$username = XVFS_Backend_xnodes::_getUser();
		$this->_repository->clearErrors();
		$ret = $this->_repository->mkdir($xmlcontainer, $username, null);
		return $ret;
	}

	/**
	 * Creacion y/o actualizacion de nodos bajo rutas virtuales.
	 *
	 * @param string rpath Ruta del nodo a crear/modificar
	 * @param string content Contenido del nodo
	 * @return int ID del nodo creado/modificado o el codigo de error generado
	 */
	function _virtualAppend($rpath, $content) {

		$username = XVFS_Backend_xnodes::_getUser();
		$base = $this->_virtual->isVirtual($rpath);
		$this->_repository->clearErrors();

		// Este metodo devuelve informacion de los posibles nodos existentes bajo una ruta virtual
		// y de la misma ruta que se esta indicando.
		$docinfo = $this->_virtual->getXmlDocumentsInfo($rpath);
//		logdump($docinfo, $rpath);
//		return true;


		// El documento debe nombrarse como el contendor, ver comportamiento en ximdex
		// cuando se renombra un XMLContainer o XimletContainer.
		$container_name = $this->_stripLanguageSuffix($docinfo['container']);
		$rpath_name = $this->_stripLanguageSuffix($rpath);
		if (!is_null($docinfo['container']) && $container_name['name'] != $rpath_name['name']) {
			XVFS_Log::warning("XVFS::_virtualAppend($rpath) - El documento se renombrara para que coincida con el nombre del contenedor: {$container_name['name']}");
			$rpath = dirname($rpath) . "/{$container_name['name']}-{$rpath_name['suffix']}";
		}


		$rootfolder = $this->_virtual->getEntity($docinfo['rootfolder']);

		// Si existen nodos se actualiza su contenido
		$ret = null;
		$nodes =& $docinfo['nodes'];

		if (count($nodes) > 0) {

			foreach ($nodes as $idnode => $info) {

				// Si el nodo no tiene ningun canal asociado no es necesario
				// actualizar el contenido, se actualizara mas abajo.
				if (count($info['channels']) > 0) {

					$node = NodeEntity::getEntity($idnode);
					$bpath = $this->pathInBackend($node->get('path'));
					$node->setContent($content);
					$ret = $this->_repository->update($node, $username, null);
				}
			}

			// Si se actualizo algun nodo se sale de la funcion
			if (!is_null($ret)) return $ret;
		}

		$pathinfo =& $docinfo['pathinfo'];
		$channels = array_keys($pathinfo['channels']);


		// Si existen nodos pero no se ha actualizado ninguno se asume que se esta
		// creando un nuevo canal
		if (count($nodes) > 0) {

			$lang = array_keys($pathinfo['languages']);
			$lang = reset($lang);
			foreach ($nodes as $idnode => $info) {

				$node = NodeEntity::getEntity($idnode);
				if ($node->get('idlanguage') == $lang) {

					$ret = $this->_repository->addChannel($node, $channels, $username);
					// Si se creo correctamente el canal actualizamos el contenido del nodo
					if ($ret) {
						$node->setContent($content);
						$ret = $this->_repository->update($node, $username, null);
					}
					return $ret;
				}
			}
		}

		// Si no se actualiza ningun nodo, ni se crea ningun canal, se crean nuevos idiomas.
		// Se crearan nodos con los idiomas y canales que se especifican en el ruta.
		$langs =& $pathinfo['languages'];


		// Crear el contenedor si no existe
		if (is_null($docinfo['container'])) {

			$path = pathinfo($rpath);
			$path = $path['dirname'] . '/' . str_replace('.' . $path['extension'], '', $path['basename']);

			$xmlcontainer = new NodeEntity_dir($path);
			$xmlcontainer->set('name', basename($path));
			$xmlcontainer->set('path', $path);
			$xmlcontainer->set('idparent', $rootfolder->get('idnode'));

			$ret = $this->_repository->mkdir($xmlcontainer, $username, null);
			if ($ret < 0) return $ret;
			$xmlcontainer->update();

		} else {
			$container = $this->pathInBackend($docinfo['container']);
			$xmlcontainer = $this->_getEntity($container);
		}

		// Crea tantos nodos como idiomas se han obtenido del padre
		foreach ($langs as $idlang => $lang) {

			// Este es el path real del documento a guardar
			// Elimina posibles coletillas de idiomas
			$basename = $this->_stripLanguageSuffix($rpath);
			$basename = $basename['name'];
			$path = "$base/$basename";

			$entity = new NodeEntity_File($path);
			$entity->set('idparent', $xmlcontainer->get('idnode'));
			$entity->set('path', $path);
			$entity->set('name', $basename);
			$entity->set('idlanguage', $idlang);
			$entity->set('channels', $channels);
			$entity->setContent($content);

			$ret = $this->_repository->append($entity, $username, 0664);
			if ($ret > 0) $entity->update();

		}

		if ($ret > 0) {
			// Se actualiza la coleccion de entidades virtuales
			$this->_virtual->_loadXmlContainers($rootfolder);
		}

		return $ret;
	}

	/**
	 * Elimina nodos bajo rutas virtuales.
	 *
	 * @param string rpath Ruta a eliminar
	 * @return int ID del nodo eliminado o codigo del error generado
	 */
	function _virtualDelete($rpath) {

		$entity = $this->_virtual->getEntity($rpath);
		if (is_null($entity)) return XVFS_NOT_EXISTS;

		$username = XVFS_Backend_xnodes::_getUser();
		$this->_repository->clearErrors();

		if ($entity->get('isdir')) {

			// Solo se permite eliminar el XmlContainer, esto es eliminar
			// un documento con todos sus idiomas y enlaces.
			// ***************************************************************************
			// Si se intenta eliminar un directorio que represente a un idioma o canal
			// el resultado seria eliminar el documento o la asociacion al canal,
			// pero el efecto en la interfaz de usuario seria que no desapareceria el directorio.
			// ***************************************************************************
			if (!$entity->get('isvirtualcontainer')) return XVFS_NOT_EXISTS;

			// Se elimina el contenedor y todos sus descendientes
			$ret = $this->_repository->delete($entity, $username);

		} else {

			// Se esta eliminando la asociacion a un canal
			$ret = $this->_repository->deleteChannel($entity, $username);
		}

		if ($ret > 0) {
			// Se actualiza la coleccion de entidades virtuales
			$parent = $entity->getParent();
			$this->_virtual->_loadXmlContainers($parent);
		}

		return $ret;
	}

	/**
	 * Search nodes 
	 * @param string query 
	 * @return object
	 */
	function search($query) {
		
		$results = $this->_repository->search($query);
		$entities = array();
		
		foreach ($results['data'] as $element) {
		
			// Transform absolute path from ximdex to backend path
			$abspath = $element['abspath'];
			$path = preg_replace('#^/ximDEX'.$this->_be_base.'#', '', $abspath);
			$path = $this->realPath($path);
			
			$entity = $this->read($path);
			if (empty($entity)) {
				continue;
			}
			$entities[] = $entity;
		}
		
		$results['data'] = $entities;
		return $results;
	}
	
	function count($query) {
 
		$qp = QueryProcessor::getInstance('SQL');
		$data = $qp->count($query);

		return $data;
		
	}
	
}

?>
