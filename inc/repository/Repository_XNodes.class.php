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



require_once(XIMDEX_ROOT_PATH . '/inc/repository/Repository.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/persistence/datafactory.php');
//
include_once(XIMDEX_ROOT_PATH . '/inc/auth/Auth.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/io/BaseIO.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/io/BaseIOInferer.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');

class Repository_XNodes extends Repository {

	/**
	 * @var array Cache de nodos
	 */
	var $_cache;

	/**
	 * @var boolean Flag para indicar el reinicio de la coleccion de hijos
	 */
	var $_reset;

	/**
	 * @var object Instancia de BaseIO
	 */
	var $_io;

	/**
	 * @var object Instancia de DB
	 */
	var $_db;


	/**
	 * Constructor.
	 * Es posible construir el repositorio a traves de un Path, un idNodo o un objeto NodeEntity.
	 *
	 * NOTE: La mayoria de los metodos de esta clase aceptan, como parametro identificador del nodo, un idNodo, un Path o un objeto NodeEntity.
	 *
	 * @param mixed root Identificador del nodo raiz del repositorio
	 */
	function Repository_XNodes($root) {

		$this->_db = new DB();
		$root = NodeEntity::getEntity($root);
		parent::Repository($root);

		if (!$this->isDir($root)) {
			$this->_root = null;
			$this->addError(REP_INVALID_ROOT);
			XMD_Log::error('Repository_XNodes - ' . $this->getErrorString(REP_INVALID_ROOT));
//			die('Se ha intentado crear un objeto Repository_XNodes con una raiz no valida.');
		}

		$this->_io = new BaseIO();
	}

	/**
	 * Devuelve una instancia NodeEntity_Dir de la raiz del repositorio.
	 */
	function & getRoot() {
		return $this->_root;
	}

	/**
	 * Obtiene el ID de usuario a traves del nombre
	 *
	 * @param string userName Nombre de usuario
	 */
	function _getUserID($userName) {
		$user = new User();
		$user->setByLogin($userName);
		$userId = $user->getID();
		return $userId;
	}

	/**
	 * Obtiene un objeto Node a partir de un id o un path.
	 * El nodo debe pertenecer al repositorio o se devolvera NULL.
	 *
	 * TODO: isReadable?
	 *
	 * @param mixed node Identificador de un nodo, como ID o como Path
	 * @return object
	 */
	function & read($node) {

    	$_node = NodeEntity::getEntity($node);
    	$null = null;
    	if (!$this->exists($_node)) {
			$this->addError(REP_NOT_EXISTS);
			return $null;
    	}
		$this->addError(REP_NONE);
		return $_node;
	}

	/**
	 * Comprueba que un determinado nodo existe y es hijo de la raiz del repositorio
	 * o la misma raiz.
	 *
	 * @param mixed entity El identificador del nodo que se quiere comprobar
	 * @return boolean TRUE si el nodo existe
	 */
	function exists($entity) {

		$ret = false;

		$entity = NodeEntity::getEntity($entity);
		if (!is_object($entity)) {
			$this->addError(REP_NOT_EXISTS);
			return $ret;
		}

		if (!$entity->exists()) {
			$this->addError(REP_NOT_EXISTS);
			return $ret;
		}

		// Comprobacion en el cache de nodos
//		$node = NodeCache::select($idNode);
//		if (!is_null($node)) {
//			if (isset($node['fromrepository'])) {
//				$exists = (boolean) $node['fromrepository'];
//				if ($exists) return true;
//			}
//		}

		$idNode = $entity->get('idnode');
		$idRoot = $this->_root->get('idnode');

		// Se comprueba si el nodo es hijo de la raiz o la propia raiz
		$sql = "select count(1) from FastTraverse where IdChild = '$idNode' and IdNode = '$idRoot'";

		$db =& $this->_db;
		$db->Query($sql);

		if (!$db->EOF) $ret = (boolean) $db->GetValue(0);

//		if (!$ret) $this->addError(REP_NOT_IN_REP);

		// si el nodo existe se inserta en la cache
//		$nodePath = $this->_transformNode($idNode);
//		$nodePath = $nodePath['path'];
//
//		$node = new Node($idNode);
//		$node = $node->loadData();
//		$node['bpath'] = $nodePath;
//		$node['fromrepository'] = $ret;
//		NodeCache::insert($node);

//		unset($db);
		return $ret;
	}

	/**
	 * Devuelve una coleccion de IDs, hijos del nodo indicado en el parametro entity.
	 * Si no se especifica ningun nodo como parametro se devuelven los hijos de la raiz del repositorio.
	 *
	 * @param mixed idNode Nodo del cual se quieren obtener los hijos
	 * @return array Coleccion de nodos hijos
	 */
	function & _getChildrens($entity=null) {

		$entity = NodeEntity::getEntity($entity);
		$entity = is_null($entity) ? $this->_root : $entity;
		$childrens = array();

		// El nodo indicado debe ser la raiz o algun descendiente y debe ser un directorio
		if (!$this->isDir($entity)) {
			$this->addError(REP_NOT_DIR);
			return $childrens;
		}

		$childrens = $entity->getChildrens();
		return $childrens;
	}

	/**
	 * Itera sobre una coleccion de nodos descendientes de la raiz.
	 * En cada lectura devuelve una referencia al nodo al que apunta el puntero de la coleccion.
	 * Si se quiere cargar otra coleccion de nodos se debe llamar a reset() pasandole
	 * el nodo raiz de la nueva coleccion.
	 * Si se llama a reset() sin parametros se resetea el puntero de la coleccion y
	 * una llamada a read devolvera el primer node.
	 *
	 * @return object Devuelve una referencia a una entidad o NULL en caso de que no existan mas nodos.
	 */
	function & next() {

		if (!is_array($this->_childrens)) {

			$this->_childrens =& $this->_getChildrens($this->_root);
			$this->_reset = true;
		}

		if ($this->_reset) {
			$entity = current($this->_childrens);
			$this->_reset = false;
		} else {
			$entity = next($this->_childrens);
		}

		$entity = NodeEntity::getEntity($entity);

		if (!is_null($entity)) {
			$name = $entity->get('name');
		} else {
			$entity = false;
			$this->_childrens = null;
		}

		return $entity;
	}

	/**
	 * Resetea el puntero de la coleccion cargada actualmente al inicio de la misma.
	 * Opcionalemente recarga la coleccion con un nuevo conjunto de nodos
	 * si se pasa como argumento una raiz distinta.
	 *
	 * @param mixed entity Nodo raiz a partir del cual se cargaran todos sus hijos en la coleccion
	 */
	function reset($entity) {
		$entity = NodeEntity::getEntity($entity);
		$this->_childrens = $this->_getChildrens($entity);
		$this->_reset = true;
	}

	/**
	 * Devuelve TRUE si el nodo es un directorio
	 *
	 * @param mixed entity
	 * @return boolean
	 */
	function isDir($entity) {

		$entity = NodeEntity::getEntity($entity);
		if (!$this->exists($entity)) return false;

		$idNode = $entity->get('idnode');
		$ret = $entity->get('isdir');
		$ret_str = $ret ? 'TRUE' : 'FALSE';

//		XMD_Log::debug("Repository_XNodes::isDir($idNode) => ret: $ret_str");
		return $ret;
	}

	/**
	 * Devuelve TRUE si el nodo no es un directorio
	 *
	 * @param mixed entity
	 * @return boolean
	 */
	function isFile($entity) {

		$entity = NodeEntity::getEntity($entity);
		if (!$this->exists($entity)) return false;

		$idNode = $entity->get('idnode');
		$ret = $entity->get('isfile');
		$ret_str = $ret ? 'TRUE' : 'FALSE';

//		XMD_Log::debug("Repository_XNodes::isFile($idNode) => ret: $ret_str");
		return $ret;
	}

	/**
	 * Comprueba si un usuario puede leer un determinado nodo
	 *
	 * @param mixed entity Nodo a comprobar
	 * @param string userName Usuario que inicio sesion
	 * @return boolean
	 */
	function isReadable($entity, $userName) {

		$entity = NodeEntity::getEntity($entity);
		if (!$this->exists($entity)) return false;

		$idUser = $this->_getUserID($userName);
		$idNode = $entity->get('idnode');
		$ret = Auth::canRead($idUser, array('node_id' => $idNode));

		$ret_str = $ret ? 'TRUE' : 'FALSE';
		XMD_Log::debug("Repository_XNodes::isReadable($idNode, $userName) => ret: $ret_str");

		return $ret;
	}

	/**
	 * Comprueba si un usuario puede escribir un determinado nodo
	 *
	 * @param mixed entity Nodo a comprobar
	 * @param string userName Usuario que inicio sesion
	 * @return boolean
	 */
	function isWritable($entity, $userName) {

		$entity = NodeEntity::getEntity($entity);
		if (!$this->exists($entity)) return false;

		$idUser = $this->_getUserID($userName);
		$idNode = $entity->get('idnode');
		$nodeType = $entity->get('nodetype');
		$ret = Auth::canWrite($idUser, array('node_id' => $idNode, 'node_type' => $nodeType));

		$ret_str = $ret ? 'TRUE' : 'FALSE';
		XMD_Log::debug("Repository_XNodes::isWritable($idNode, $userName) => ret: $ret_str");

		return $ret;
	}

	/**
	 * No implementado
	 */
	function search($query) {

		$handler = 'SQL';	// SQL / Solr / ¿¿ XVFS ??
		$output = 'JSON';		// JSON / XML

		if (is_string($query)) {
			$query = XmlBase::recodeSrc($query, \App::getValue( 'workingEncoding'));
			$query = str_replace('\\"', '"', $query);
		}

		$qh = QueryProcessor::getInstance($handler);
		if (is_object($qh)) {
			$format = $output == 'XML' ? 'XML' : 'ARRAY';
			$results = $qh->search($query, $format);
		} else {
			$results = array('error'=>1, 'msg'=>'No se encuentra el manejador de consultas.');
		}
		
		return $results;
	}

	/**
	 * No implementado
	 */
	function view() {
		die('Repository::view() Not imlemented');
	}


	// ===== Operaciones con nodos =====


	/**
	 * Workaround!, Intenta obtener un esquema por defecto.
	 * TODO: Esta funcion es temporal, modificarla cuando se implementen las propiedades.
	 */
	function _getDefaultVisualTemplate() {

		$visualtemplate = \App::getValue( 'defaultWebdavPVD');

		if (is_null($visualtemplate)) $visualtemplate = \App::getValue( 'defaultPVD');

		if (is_null($visualtemplate)) {
			$sql = 'select IdNode from Nodes where IdNodeType = 5045 order by IdNode limit 1';
			$db =& $this->_db;
			$db->query($sql);

			while (!$db->EOF && is_null($visualtemplate)) {
				$visualtemplate = $db->getValue('IdNode');
				$db->next();
			}
		}

		// Si despues de todo no se consigue un esquema se devuelve null
		return $visualtemplate;
	}

	/**
	 * Crea un nuevo node de tipo FILENODE.
	 * La entidad pasada debe tener asignado el nombre y el padre, bien por
	 * medio de IDs o por medio del Path.
	 *
	 * @param object Entity Objeto NodeEntity que se quiere agregar al repositorio
	 * @param string userName Usuario que inicio sesion
	 * @param string mode No usado
	 * @return int Devuelve el ID del nuevo nodo o el codigo de error producido
	 */
	function append($entity, $userName, $mode) {

		$path = $entity->get('path');

		// El nodo no puede existir
		if ($entity->exists()) {

			$this->addError(REP_EXISTS);
			$idnode = $entity->get('idnode');
			XMD_Log::error("Repository_XNodes::append($path) - " . $this->getErrorString(REP_EXISTS));
			return REP_EXISTS;
		}

		// Se necesita el nombre del nuevo nodo y el ID del padre
		$parent = $entity->getParent();
		$parentPath = $parent->get('path');
		if (!$parent->exists()) {
			$this->addError(REP_PARENT_NOT_FOUND);
			XMD_Log::error("Repository_XNodes::append($path) - " . $this->getErrorString(REP_PARENT_NOT_FOUND));
			return REP_PARENT_NOT_FOUND;
		}

		$name = $entity->get('name');
		if (is_null($name)) {

			$name = $entity->get('path');
			if (is_null($name)) {
				$this->addError(REP_NO_NODE_NAME);
				XMD_Log::error("Repository_XNodes::append($path) - " . $this->getErrorString(REP_NO_NODE_NAME));
				return REP_NO_NODE_NAME;
			}

			$name = basename($name);
			$entity->set('name', $name);
		}

		// El padre debe existir en el repositorio y ser un directorio
		if (!$this->isDir($parent)) {
			$this->addError(REP_NOT_DIR);
			XMD_Log::error("Repository_XNodes::append($parentPath) - El padre del nodo indicado no es un directorio.");
			return REP_NOT_DIR;
		}

		$entity->set('idparent', $parent->get('idnode'));

		// Inferencia de nodetype a traves de BaseIO
		$inferer = new BaseIOInferer();
		$infere_res = $inferer->infereType(($entity->get('isdir') ? 'FOLDER' : 'FILE'), $parent->get('idnode'), $entity->getDescriptor());

		if (is_null($infere_res)) {
			$this->addError(REP_INFERE_ERROR);
			XMD_Log::error("Repository_XNodes::append($path) - " . $this->getErrorString(REP_INFERE_ERROR));
			return REP_INFERE_ERROR;
		}


		if ($entity->get('idTemplate') > 0) {
			$visualtemplate = $entity->get('idTemplate');
		} else {
			// Obtiene el esquema por defecto
			$visualtemplate = $this->_getDefaultVisualTemplate();
		}
		if (empty($visualtemplate)) {
			XMD_Log::error('No se ha encontrado pvd/rng para crear el nodo, compruebe que la instancia est� correctamente configurada');
		}

		// Ok, let's rock
		$struct_io = array (
					'NAME' => $name,
					'PARENTID' => $parent->get('idnode'),
					'NODETYPENAME' => $infere_res['NODETYPENAME'],
					'CHILDRENS' => array(
						array('NODETYPENAME' => 'PATH', 'SRC' => $entity->getDescriptor())
					)
				);

		// Valores necesarios para VirtualPathAdapter
		$nodetype = strtoupper($infere_res['NODETYPENAME']);
		switch($nodetype) {
			case 'XMLCONTAINER':
			case 'XIMLETCONTAINER':

				if (is_null($visualtemplate)) {
					// TODO: Cambiar el tipo de error devuelto
					// No se puede crear el contenedor
					$this->addError(REP_UNKNOWN);
					XMD_Log::error("Repository_XNodes::append($path) - No fue posible asociar el contenedor a una plantilla.");
					return REP_UNKNOWN;
				}

				$struct_io['CHILDRENS'][] = array ('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $visualtemplate);
				break;

			case 'XMLDOCUMENT':
			case 'XIMLET':
		}

//		logdump($nodetype, print_r($struct_io, true));

		$userId = $this->_getUserID($userName);

		$io =& $this->_io;
		$ret = $io->build($struct_io, $userId);
//debug::log($struct_io, $ret, $io->messages->messages);
		if ($ret > 0) {
			// Si BaseIO devuelve un entero positivo se trata de un idnode
			$entity->set('idnode', $ret);
			$entity->update();
			$this->addError(REP_NONE);
			XMD_Log::info("Repository_XNodes::append($path) - Se ha creado un nuevo nodo con idNode = $ret");
		} else {
			// Si BaseIO devuelve un entero negativo se trata de un error
			$this->_baseio_error = $ret;
			$this->addError(REP_BASEIO_ERROR);
			XMD_Log::error("Repository_XNodes::append($path) - " . $this->getErrorString(REP_BASEIO_ERROR));
			$msg = end($io->messages->messages);
			XMD_Log::error("Repository_XNodes::append($path) - {$msg['message']}");
			$ret = REP_BASEIO_ERROR;
		}


		// Si se pudo crear el nodo lo inserto en el cache
//		if ($return) $this->_cacheQuery('insert', $ret);
		return $ret;
	}

	/**
	 * Crea un nuevo nodo de tipo FOLDERNODE.
	 * La entidad pasada como parametro debe tener asignado el idParent y
	 * el nombre del nuevo nodo a crear.
	 *
	 * @param object Entity Objeto NodeEntity que se quiere agregar al repositorio
	 * @param string userName Usuario que inicio sesion
	 * @param string mode No usado
	 * @return int Devuelve el ID del nuevo nodo o el codigo de error producido
	 */
	function mkdir($entity, $userName, $mode) {

		if (!is_a($entity, 'NodeEntity_Dir')) {
			$this->addError(REP_NOT_DIR);
			XMD_Log::info("Repository_XNodes::append() - " . $this->getErrorString(REP_NOT_DIR));
			return REP_NOT_DIR;
		}
		$ret = $this->append($entity, $userName, $mode);
		return $ret;
	}

	/**
	 * Actualiza un nodo FILENODE o FOLDERNODE
	 *
	 * @param object entity Objeto NodeEntity que se quiere actualizar
	 * @param string userName Usuario que inicio sesion
	 * @param string mode No usado
	 * @return int Devuelve el ID del nodo actualizado o el codigo de error producido
	 */
	function update($entity, $userName, $mode) {

		$entity = NodeEntity::getEntity($entity);

		// Debe existir en el repositorio
		if (!$this->exists($entity)) {
			$this->addError(REP_NOT_IN_REP);
			XMD_Log::error("Repository_XNodes::update() - " . $this->getErrorString(REP_NOT_IN_REP));
			return REP_NOT_IN_REP;
		}

		$struct_io = array (
					'ID' => $entity->get('idnode'),
					'NODETYPENAME' => $entity->get('nodetype'),
//					'NAME' => $entity->get('name'),	=> No rula con el nombre
					'CHILDRENS' => array(
							array('NODETYPENAME' => 'PATH', 'SRC' => $entity->getDescriptor())
						)
					);


//		logdump("Repository_XNodes::update() - Pre-baseIO call - " . print_r($struct_io, true));

		$userId = $this->_getUserID($userName);

		$io =& $this->_io;
		//debug::log($struct_io, $userId);
		$ret = $io->update($struct_io, $userId);

		if ($ret > 0) {
			$this->addError(REP_NONE);
			XMD_Log::info("Repository_XNodes::update() - Se ha actualizado correctamente el nodo con idNode = $ret");
			$entity->update();
		} else {
			// Si BaseIO devuelve un entero negativo se trata de un error
			$this->_baseio_error = $ret;
			$this->addError(REP_BASEIO_ERROR);
			XMD_Log::error("Repository_XNodes::update() - " . $this->getErrorString(REP_BASEIO_ERROR));
			$msg = end($io->messages->messages);
			XMD_Log::error("Repository_XNodes::update() - {$msg['message']}");
			$ret = REP_BASEIO_ERROR;
		}

//		logdump(print_r($struct_io, true), $io->messages->messages);
		return $ret;
	}

	/**
	 * Elimina un nodo.
	 *
	 * @param object entity Objeto NodeEntity a eliminar
	 * @param string userName Usuario que inicio sesion
	 * @return int Devuelve el ID del nodo eliminado o el codigo de error producido
	 */
	function delete($entity, $userName) {

		$entity = NodeEntity::getEntity($entity);
		if (!$this->exists($entity)) {
			$this->addError(REP_NOT_IN_REP);
			XMD_Log::error("Repository_XNodes::delete() - " . $this->getErrorString(REP_NOT_IN_REP));
			return REP_NOT_IN_REP;
		}

		$struct_io = array (
					'ID' => $entity->get('idnode'),
					'NODETYPENAME' => $entity->get('nodetype')
					);

		if ((boolean)$entity->get('idchannel')) {
			$childrens = array ('NODETYPENAME' => 'CHANNEL', 'ID' => $entity->get('idchannel'), 'OPERATION' => 'remove');
			$struct_io['CHILDRENS'] = $childrens;
		}

//		logdump($entity, $struct_io);

		$userId = $this->_getUserID($userName);

		$io =& $this->_io;
		$ret = $io->delete($struct_io, $userId);

		if ($ret > 0) {
			$this->addError(REP_NONE);
			$idNode = $entity->get('idnode');
			XMD_Log::info("Repository_XNodes::delete() - Se ha aliminado correctamente el nodo con idNode = $idNode");
			$entity->update();
		} else {
			// Si BaseIO devuelve un entero negativo se trata de un error
			$this->_baseio_error = $ret;
			$this->addError(REP_BASEIO_ERROR);
			XMD_Log::error("Repository_XNodes::delete() - " . $this->getErrorString(REP_BASEIO_ERROR));
			$msg = end($io->messages->messages);
			XMD_Log::error("Repository_XNodes::delete() - {$msg['message']}");
			$ret = REP_BASEIO_ERROR;
		}

//		logdump($ret, $io->messages->messages);

		// Se elimina el nodo de la cache
//		if ($return) $this->_cacheQuery('delete', $idNode);

		return $ret;
	}

	/**
	 * Copia un nodo al contenedor especificado.
	 * El origen y el destino deben existir en el repositorio.
	 *
	 * TODO: El metodo copyNode recibe como parametro el ID del contenedor de destino,
	 * 		 pero parece que no se puede especificar de ninguna forma las propiedades
	 * 		 del nuevo nodo que se va a crear, como el nombre, etc...
	 * 		 ademas copyNode no devuelve el ID del nuevo nodo creado.
	 *
	 * @param object source Nodo a copiar
	 * @param object dest Nodo de destino
	 * @param string userName Nombre del usuario que inicio sesion
	 * @param object copied Es una referencia al nuevo nodo copiado
	 * @return int Devuelve el ID del nuevo nodo o el codigo de error producido
	 */
	function copy($source, $dest, $userName, &$copied) {

		$source = NodeEntity::getEntity($source);
		$source_name = $source->get('name');
		$source_path = $source->get('path');

		// Comprobar si $dest es un XPath o un Objeto node para obtener el padre
		if (is_string($dest)) {

			$dest_name = basename($dest);
			$parent = dirname($dest);
			$parent = NodeEntity::getEntity($parent);

		} else if (is_a($dest, 'NodeEntity')) {

			$dest_name = $dest->get('name');
			$parent = $dest->getParent();
			$dest = $dest->get('path');

		} else {

			// No se puede determinar cual es el padre del nodo destino
			// $dest no puede ser un idnode ya que eso implica que el nodo existe
			$this->addError(REP_NO_TARGET);
			XMD_Log::error("Repository_XNodes::copy($source_path, ?) - " . $this->getErrorString(REP_NO_TARGET));
			return REP_NO_TARGET;
		}


		// El origen y el contenedor de destino deben pertenecer al repositorio.
		if (!$this->exists($source)) {
			$this->addError(REP_NO_SOURCE);
			XMD_Log::error("Repository_XNodes::copy($source_path, $dest) - " . $this->getErrorString(REP_NO_SOURCE));
			return REP_NO_SOURCE;
		}

		if (!$this->exists($parent)) {
			$this->addError(REP_NO_TARGET);
			XMD_Log::error("Repository_XNodes::copy($source_path, $dest) - " . $this->getErrorString(REP_NO_TARGET));
			return REP_NO_TARGET;
		}

		// NOTE: Importante: BaseIO necesita que sean enteros
		$idSource = (int)$source->get('idnode');
		$idParent = (int)$parent->get('idnode');

		// El origen no puede ser la raiz del repositorio.
		if ($this->_root->get('idnode') == $idSource) {
			$this->addError(REP_INVALID_SOURCE);
			XMD_Log::error("Repository_XNodes::copy($source_path, $dest) - " . $this->getErrorString(REP_INVALID_SOURCE));
			return REP_INVALID_SOURCE;
		}


		XMD_Log::debug("Repository_XNodes::copy($source_path, $dest) - Se procede a copiar el nodo");

		//
		// Realiza una copia de nodos con la accion copyNode()
		//
//		require_once(XIMDEX_ROOT_PATH . '/actions/copy/baseIO.php');
//		$lastCopied = $this->_getLastCopiedResource($parent, $source_name);
//		$messages = copyNode($idSource, $idParent, /*$recurrence*/ null);
//		$errors = !(boolean) $messages->count(MSG_TYPE_ERROR);

		//
		// Realiza la copia de nodos con un metodo propio de esta clase
		// NOTE: Este metodo es algo mas rapido que copyNode() y soluciona el problema de la asignacion de nombres
		//
		$ret = $this->_copy($source, $dest, $userName);
		$errors = $ret > 0 ? true : false;

		$ret = null;
		if ($errors) {

			$sourceid = $source->getParent();
			$sourceid = $sourceid->get('idnode');

			/**
			 * Workaround!	Usar con la accion copyNode()!
			 *
			 * El metodo copyNode() no da la opcion de especificar un nuevo nombre si el contenedor
			 * de destino es el mismo que el de origen.
			 * Asi que es necesario renombrar el nuevo recurso en el caso de que el contenedor
			 * de destino y de origen sea el mismo.
			 */
//			if ($idParent == $sourceid) {
//
//				$torename = "{$source_name}_copia_" . ++$lastCopied;
//				$torename = $dest->get('path') . '/' . $torename;
//				$copied = NodeEntity::getEntity($torename);
//				$ret = $this->rename($torename, $dest_name, $userName);
//			}

			$copied = NodeEntity::getEntity($dest);
			$ret = $copied->get('idnode');
			$this->addError(REP_NONE);
			XMD_Log::info("Repository_XNodes::copy($source_path, $dest) - Se ha copiado el nodo correctamente.");

		} else {
			$ret = REP_UNKNOWN;
			XMD_Log::error("Repository_XNodes::copy($source_path, $dest) - No fue posible copiar el nodo.");
		}

		return $ret;
	}

	/**
	 * Workaround!
	 *
	 * El metodo copyNode() no da la opcion de especificar un nuevo nombre si el contenedor
	 * de destino es el mismo que el de origen.
	 * Este metodo devuelve el ultimo recurso que se copio antes de realizar la nueva copia
	 * y asi conocer el nombre resultante.
	 */
	function _getLastCopiedResource($container, $source_name) {

		$count = -1;
		$col = $container->getCollection();

		foreach ($col as $idnode) {

			$res = NodeEntity::getEntity($idnode);
			$name = $res->get('name');
			$regexp = "/{$source_name}_copia_(\d)$/im";
			$i = preg_match($regexp, $name, $match);

			if ($i > 0 && $match[1] > $count) {
				$count = $match[1];
			}
		}

		return $count;
	}

	/**
	 * Alternativa a la accion copyNode().
	 * Mejora un poco el rendimiento y soluciona el problema de la asignacion de nombres
	 * ya que no crea el nuevo nodo con un nombre predeterminado, sino con el que indica
	 * el cliente en la llamada.
	 *
	 * @param object source Nodo a copiar
	 * @param string target XPath de destino (No indica el contenedor de destino, sino el nodo final!)
	 * @param string userName Nombre del usuario que realiza la peticion
	 */
	function _copy($source, $target, $userName) {

		if ($source->get('isfile')) {

			$dest = new NodeEntity_File($target);
//			$dest->set('name', basename($target));
			$dest->setContent($source->getContent());

			$ret = $this->append($dest, $userName, null);

		} else if ($source->get('isdir')) {

			$dest = new NodeEntity_Dir($target);
//			$dest->set('name', basename($target));

			$ret = $this->mkdir($dest, $userName, null);
			if ($ret <= 0) return $ret;

//			$dest->update();

			$col = $source->getCollection();
			foreach ($col as $idnode) {

				$node = NodeEntity::getEntity($idnode);
				$name = $node->get('name');

				$new_target = "$target/$name";
				/* Recursivo */
				$ret = $this->_copy($node, $new_target, $userName);
				/* Recursivo */
			}

		}

		return $ret;
	}

	/**
	 * Mueve un nodo al contenedor especificado.
	 *
	 * TODO: Parece como si $moved no se estuviese pasando por referencia ???
	 * 		 Pos claro, porque no se establece bien en copy...
	 *
	 * @param object source Nodo a copiar
	 * @param object dest Nodo de destino
	 * @param string userName Nombre del usuario que inicio sesion
	 * @param object moved Es una referencia al nodo que se ha movido
	 * @return int Devuelve el ID del nodo movido o el codigo de error producido
	 */
	function move($source, $dest, $userName, &$moved) {

		$moved = null;

		$ret = $this->copy($source, $dest, $userName, $moved);
		if ($ret < 0) {
			return $ret;
		}

		$ret = $this->delete($source, $userName);
		if ($ret < 0) {
			return $ret;
		}

		if ($ret > 0) {
			$this->addError(REP_NONE);
			$ret = $moved->get('idnode');
		}
		return $ret;
	}

	/**
	 * Renombra el nodo pasado como argumento.
	 *
	 * @param object entity Objeto NodeEntity que se quiere renombrar
	 * @param string newName Nuevo nombre
	 * @param string userName Nombre del usuario que inicio sesion
	 * @return int Devuelve el ID del nodo renombrado o el codigo de error producido
	 */
	function rename($entity, $newName, $userName) {

		$ret = false;

		$entity = NodeEntity::getEntity($entity);

		if (!$this->exists($entity)) {
			$this->addError(REP_NOT_IN_REP);
			XMD_Log::error("Repository_XNodes::rename() - " . $this->getErrorString(REP_NOT_IN_REP));
			return REP_NOT_IN_REP;
		}

		// Comprueba que no exista ya un nodo con este nombre
		$_node = dirname($entity->get('path')) . '/' . $newName;
		$_node = NodeEntity::getEntity($_node);

		if ($this->exists($_node)) {
			$this->addError(REP_EXISTS);
			XMD_Log::error("Repository_XNodes::rename() - " . $this->getErrorString(REP_EXISTS));
			return REP_EXISTS;
		}

		if (($ret = $this->isWritable($entity, $userName)) && is_null($_node)) {

			$node = $entity->asNode();
			$idNode = $entity->get('idnode');
			$ret = $node->RenameNode($newName);

			if ($ret) {
				$this->addError(REP_NONE);
				XMD_Log::info("Repository_XNodes::rename() - Se ha renombrado el nodo con idNode = $idNode correctamente.");
				$entity->update();
				$ret = $entity->get('idnode');
			} else {
				XMD_Log::error("Repository_XNodes::rename() - No se ha podido renombrar el nodo con idNode = $idNode.");
			}
		}

		return $ret;
	}


	// ===== Operaciones con StructuredDocuments =====


	/**
	 * Associate one or more channels to a node.
	 * Este metodo es usado por el sistema virtual de nodos.
	 *
	 * @param object entity Entidad que se modificara
	 * @param array channels Collection of channel ids to be associate
	 * @param string username Usuario que realiza la peticion
	 * @return int Devuelve el ID del nodo modificado o el codigo de error producido
	 */
	function addChannel(&$entity, $channels, $username) {

		$entity = NodeEntity::getEntity($entity);

		// Debe existir en el repositorio
		if (!$this->exists($entity)) {
			$this->addError(REP_NOT_IN_REP);
			XMD_Log::error("Repository_XNodes::addChannel() - " . $this->getErrorString(REP_NOT_IN_REP));
			return REP_NOT_IN_REP;
		}

		$childrens = array();
		foreach ($channels as $idchannel) {
			$childrens[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idchannel);
		}

		$struct_io = array (
					'ID' => $entity->get('idnode'),
					'NODETYPENAME' => $entity->get('nodetype'),
					'CHILDRENS' => $childrens
					);

		$userId = $this->_getUserID($username);

		$io =& $this->_io;
		$ret = $io->update($struct_io, $userId);

		if ($ret > 0) {
			$this->addError(REP_NONE);
			$idNode = $entity->get('idnode');
			XMD_Log::info("Repository_XNodes::addChannel() - A channel has been set correctly to the node $idNode");
			$entity->update();
		} else {
			$this->_baseio_error = $ret;
			$this->addError(REP_BASEIO_ERROR);
			XMD_Log::error("Repository_XNodes::addChannel() - " . $this->getErrorString(REP_BASEIO_ERROR));
			$msg = end($io->messages->messages);
			XMD_Log::error("Repository_XNodes::addChannel() - {$msg['message']}");
			$ret = REP_BASEIO_ERROR;
		}

//		logdump('addChannel', print_r($struct_io, true), $ret, $io->messages->messages);

		return $ret;
	}

	/**
	 * Deletes the association between a channel and a node.
	 *
	 * @param object entity Objeto NodeEntity que se quiere actualizar
	 * @param string username Usuario que realiza la peticion
	 * @return int Devuelve el ID del nodo actualizado o el codigo de error producido
	 */
	function deleteChannel($entity, $userName) {

		$entity = NodeEntity::getEntity($entity);

		// Debe existir en el repositorio
		if (!$this->exists($entity)) {
			$this->addError(REP_NOT_IN_REP);
			XMD_Log::error("Repository_XNodes::deleteChannel() - " . $this->getErrorString(REP_NOT_IN_REP));
			return REP_NOT_IN_REP;
		}

		$struct_io = array (
					'ID' => $entity->get('idnode'),
					'NODETYPENAME' => $entity->get('nodetype'),
					'CHILDRENS' => array(
							array ('NODETYPENAME' => 'CHANNEL', 'ID' => $entity->get('idchannel'), 'OPERATION' => 'remove')
						)
					);

//		logdump(print_r($struct_io, true));

		$userId = $this->_getUserID($userName);

		$io =& $this->_io;
		$ret = $io->update($struct_io, $userId);

		if ($ret > 0) {
			$this->addError(REP_NONE);
			XMD_Log::info("Repository_XNodes::deleteChannel() - Removed the channel correctly for node $ret");
			$entity->update();
		} else {
			// Si BaseIO devuelve un entero negativo se trata de un error
			$this->_baseio_error = $ret;
			$this->addError(REP_BASEIO_ERROR);
			XMD_Log::error("Repository_XNodes::deleteChannel() - " . $this->getErrorString(REP_BASEIO_ERROR));
			$msg = end($io->messages->messages);
			XMD_Log::error("Repository_XNodes::deleteChannel() - {$msg['message']}");
			$ret = REP_BASEIO_ERROR;
		}

//		logdump($ret, $io->messages->messages);
		return $ret;
	}
}

?>