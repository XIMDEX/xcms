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



require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/entities/NodeEntity_File.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/entities/NodeEntity_Dir.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/entities/NodeEntity_Link.class.php');

/**
 * @abstract
 */
class NodeEntity {

	/**
	 * @var array Estructura que guarda la informacion del nodo
	 */
	var $_data;

	/**
	 * @var object Objeto DB, usado en el metodo _loadData()
	 */
	var $_db;

	/**
	 * @var string Directorio temporal para descriptores
	 */
	var $_tmpdir;

	/**
	 * Constructor. Acepta un idNode o un Path como parametro a partir del
	 * cual se obtendra el resto de informacion del nodo.
	 *
	 * @param mixed node IdNode o Path absoluto en ximDEX
	 */
	function NodeEntity($node=null) {

		$this->_db = new DB();

		// Directorio temporal para los descriptores
		$this->_tmpdir = XIMDEX_ROOT_PATH . '/data/tmp/xvfs';
//		$this->_tmpdir = '/tmp/xvfs_' . md5(Config::getValue('AppRoot'));
		if (!is_dir($this->_tmpdir)) mkdir($this->_tmpdir);

		// Inicializa las propiedades del objeto
		$this->_init();

		if (strlen($node) == 0) $node = null;
		$this->_loadData($node);
	}

	/**
	 * Iniciliza la estructura de atributos
	 */
	function _init() {

		$data =& $this->_data;
		$data = array();

		$data['idnode'] = null;
		$data['idparent'] = null;
		$data['path'] = null;
		$data['name'] = null;
		$data['idnodetype'] = null;
		$data['nodetype'] = null;
		$data['nodeclass'] = null;
		$data['content'] = null;
		$data['descriptor'] = null;
		$data['mimetype'] = null;
		$data['icon'] = null;
		$data['isdir'] = false;
		$data['isfile'] = false;
		$data['islink'] = false;
		$data['exists'] = false;
	}

	/**
	 * Devuelve el valor del atributo indicado.
	 *
	 * @param string key Nombre del atributo a recuperar.
	 * @return mixed El valor del atributo o NULL si no existe el atributo.
	 */
	function & get($key) {
		$ret = null;
		if (isset($this->_data[$key])) $ret = $this->_data[$key];
		return $ret;
	}

	/**
	 * Establece el valor de un atributo.
	 *
	 * @param string key Nombre del atributo.
	 * @param mixed value Valor que se le asignara.
	 * @return Devuelve el valor establecido o NULL si el atributo no existe.
	 */
	function set($key, $value) {
		$this->_data[$key] = $value;
		return $value;
	}

	/**
	 * Devuelve TRUE si la entidad existe
	 *
	 * @return boolean
	 */
	function exists() {
		return $this->get('exists');
	}

    /**
     * Devuelve el padre del nodo o NULL en caso de que no tenga.
     *
     * @return object
     */
    function & getParent() {
    	$_parent = !is_null($this->get('idparent')) ? $this->get('idparent') : dirname($this->get('path'));
    	$parent = new NodeEntity_Dir($_parent);
//    	if (!$parent->exists()) $parent = null;
    	return $parent;
    }

    /**
     * Obtiene una entidad a traves de un ID o un Path cuando no se conoce
     * el tipo de entidad.
     *
     * @static
     * @param mixed node ID o Path del nodo
     * @return object Retorna una entidad File, Dir o Link o NULL si la entidad no existe
     */
    function & getEntity($node) {

    	if (is_a($node, 'NodeEntity')) return $node;

    	// NOTE: Cuidado con el orden en que se comprueba el tipo de entidad...
    	// Si se comprueba si es File antes de un Dir se obtendra un fichero, aunque
    	// esto no sea cierto.
    	// Se debe a que NodeEntity_File::getDescriptor() crea un fichero temporal
    	// en el filesystem si no se encuentra el nodo en la base de datos.
		$_node = new NodeEntity_Link($node);
		if (!$_node->get('exists')) $_node = new NodeEntity_Dir($node);
		if (!$_node->get('exists')) $_node = new NodeEntity_File($node);
		if (!$_node->get('exists')) $_node = null;

		return $_node;
    }

    /**
     * Obtiene el descriptor de la entidad
     *
     * @abstract
     * @return string
     */
    function getDescriptor() {
    }

    /**
     * Obtiene el tipo MIME de la entidad
     *
     * @abstract
     * @return string
     */
    function getMIME() {
    }

	/**
	 * Devuelve el array con la informacion del nodo.
	 *
	 * @return array
	 */
	function asArray() {
		$ret = $this->_data;
		return $ret;
	}

	/**
	 * Devuelve un objeto Node.
	 *
	 * @return object
	 */
	function & asNode() {
		$node = new Node($this->get('idnode'));
		return $node;
	}

	/**
	 * Metodo publico para actualizar la informacion del nodo desde la base de datos
	 */
	function update() {
		$idNode = $this->get('idnode');
		$idNode = empty($idNode) ? $this->get('path') : $idNode;
		$this->clear();
		$this->_loadData($idNode);
	}

	/**
	 * Limpia toda la informacion del nodo, usada por los descendientes de esta clase
	 */
	function clear() {

		$data =& $this->_data;

		foreach ($data as $key=>$value) {
			$data[$key] = null;
		}

		$data['isdir'] = false;
		$data['isfile'] = false;
		$data['islink'] = false;
		$data['exists'] = false;
	}

	/**
	 * Devuelve un array con los canales asociados al nodo
	 */
	function getChannels() {

		$idnode = $this->get('idnode');
		$sql = "select c.idChannel, c.DefaultExtension as Channel
				from RelStrDocChannels rc left join Channels c using(idChannel)
				where idDoc = $idnode";

		$db =& $this->_db;
		$db->query($sql);

		$channels = array();
		while (!$db->EOF) {
			$channels[$db->getValue('idChannel')] = $db->getValue('Channel');
			$db->next();
		}

		return $channels;
	}

	/**
	 * Carga la entidad con la informacion contenida en un array
	 *
	 * @param array data Array de datos
	 */
	function loadData($data) {

		if (!is_array($data)) return false;

		$this->clear();

		foreach ($data as $key=>$value) {
			$this->_data[$key] = $value;
		}
	}

	/**
	 * Obtiene toda la informacion del nodo a partir del valor pasado en el constructor.
	 *
	 * @param mixed node IdNode o Path absoluto en ximDEX.
	 */
	function _loadData($node) {

		$db =& $this->_db;

		// Las subconsultas se implementaron en la version 4.1 de MySQL
		if ((boolean) ((float) $db->getServerVersion() >= 4.1)) {
			$path2id = '_pathToId';
			$id2path = '_idToPath';
		} else {
			$path2id = '_pathToId_Alt';
			$id2path = '_idToPath_Alt';
		}
		// Se asume que un valor numerico de $node indica un ID
		// y que un valor de tipo cadena indica un path.
		if (is_numeric($node)) {

			$this->set('idnode', $node);
			$node = $this->$id2path($node);
			$this->set('path', $node['path']);
		} else if(is_string($node)) {

			$this->set('path', $node);
			$node = $this->$path2id($node);
			$this->set('idnode', $node['idnode']);
		}

		if ($this->get('exists')) {

			// Asegura que el path sea correcto
			$path = $this->get('path');
			if ($path[strlen($path)-1] == '/' && $this->get('idnode') != 1) $path = substr($path, 0, strlen($path)-1);
			$this->set('path', str_replace('//', '/', $path));

			$this->set('idparent', $node['idparent']);
			$this->set('idnodetype', $node['idnodetype']);
			$this->set('nodetype', $node['nodetype']);
			$this->set('nodeclass', $node['nodeclass']);
			$this->set('name', $node['name']);
			$this->set('isdir', $node['isdir']);
			$this->set('islink', $node['islink']);
			$this->set('rlpath', $node['rlpath']);
			$this->set('idlanguage', $node['idlanguage']);
			$this->set('channels', $this->getChannels());
			$this->set('icon', $node['icon']);

			if (!$node['isdir']) $this->set('isfile', true);

		} else {

			// solo se anula el idNode, con el Path se puede definir la creacion de un nuevo nodo
			$this->set('idnode', null);
		}
	}

	/**
	 * Transforma un Path absoluto en ximDEX en un idNode y obtiene
	 * toda la informacion relativa al nodo.
	 *
	 * @return array Devuelve un array con el valor de los atributos del nodo.
	 */
	function _pathToId() {


		$db =& $this->_db;
		$path = $this->get('path');
		// Esta es la raiz de ximDEX...
		if ($path == '/') {
			$this->set('idnode', 1);
			$res = $this->_idToPath();
			return $res;
		}
		
		$resources = explode('/', $path);
		
		// explode() inserta un elemento vacio si el path es absoluto.
		// ... y debe ser absoluto ...
		if ($path[0] == '/') array_shift($resources);
//		dump($resources);

		$total = count($resources);
		$i = 0;
		$res = array();
		$arrayIndex = 0;
		$estimatedName = '';
		
		while ($i < $total) {
			$parentName = $resources[$i];	// Nombre del padre del nodo, el segundo elemento del array
			// Si es el primer elemento el padre es ximDEX
			$aux = isset($res[$i-1]['idparent']) ? $res[$i-1]['idparent'] : null;
			$parentId = ($i > 0) ? $aux : '1';


			// Si la ruta solo incluye un nivel el padre sera siempre ximDEX
			// TODO: Arreglar la asignacion de $nodeName
			$aux = isset($resources[$i+1]) ? $resources[$i+1] : null;
			$nodeName = ($total > 1) ? $aux : $parentName;
			if (empty($nodeName)) {$i++; continue;}

			if (preg_match('/(.*)-id(.*)-id(.*).(.*)/', $nodeName, $matches) > 0) {
				$nodeName = sprintf("%s-id%s",$matches[1], $matches[2]);
				$estimatedName = $nodeName;
			}

			$selIdNode = ($total > 1) ? 'n.idNode' : 'f.idNode';

			// Se obtienen los datos del nodo a traves de FastTraverse.
			// A partir de esta tabla se pueden obtener todos los IDs hijos del padre,
			// cruzando con Nodes y filtrando por el nombre del nodo se obtiene el ID.
			// La profundidad del nodo inmediatamente superior siempre es 1 en FastTraverse
			$sql = "select n.idNode, n.idParent, n.idNodeType, n.Name,
							if(nt.isFolder = 1 or nt.isVirtualFolder = 1, 1, 0) as isDir,
							nt.Name as nodeType, nt.class as nodeClass,nt.icon as icon,
							if(isnull(n.SharedWorkflow), 0, 1) as isLink,
							n.SharedWorkflow as linkTarget,
							sd.idLanguage
					from (Nodes n left join NodeTypes nt using(idNodeType))
						left join StructuredDocuments sd on n.idNode = sd.idDoc
					where idParent in (
						select $selIdNode
						from FastTraverse f left join Nodes n on f.idChild = n.idNode
						INNER JOIN NodeTypes nt on n.IdNodeType = nt.IdNodeType
						where f.depth < 2 
							and (n.name = '$parentName'
							and f.idNode = '$parentId') or
							nt.IsVirtualFolder = 1
					)
					and n.Name = '$nodeName'";
			$db->Query($sql);
			if (!$db->EOF && $db->numRows == 1) {
				$res[$i] = array();
				$res[$i]['idnode'] = $db->getValue('idNode');
				$res[$i]['idparent'] = $db->getValue('idParent');
				$res[$i]['idnodetype'] = $db->getValue('idNodeType');
				$res[$i]['nodetype'] = $db->getValue('nodeType');
				$res[$i]['nodeclass'] = $db->getValue('nodeClass');
				$res[$i]['name'] = $db->getValue('Name');
				$res[$i]['isdir'] = (boolean)$db->getValue('isDir');
				$res[$i]['islink'] = (boolean)$db->getValue('isLink');
				$res[$i]['rlpath'] = $db->getValue('linkTarget');
				$res[$i]['idlanguage'] = $db->getValue('idLanguage');
				$res[$i]['icon'] = $db->getValue('icon');
			}
			$i++;
//			dump($sql);

		}

		// $res contiene informacion sobre todos los nodos del path...
		// quizas no es buena idea perderlo...
//		dump($res);
		$res = end($res);
		// Si no coincide el nombre del nodo es que este no existe
		if ($res['name'] == basename($path) || ($res['name'] == $estimatedName)) $this->set('exists', true);
		return $res;
	}

	/**
	 * Transforma un idNode en un Path absoluto en ximDEX y obtiene
	 * toda la informacion relativa al nodo.
	 *
	 * @return array Devuelve un array con el valor de los atributos del nodo.
	 */
	function _idToPath() {

		$db =& $this->_db;
		$id = $this->get('idnode');
		$res = array();

		$sql = "select t.*, nt.Name as nodeType, nt.class as nodeClass, nt.icon as icon,
				if(nt.isFolder = 1 or nt.isVirtualFolder = 1, 1, 0) as isDir,
				sd.idLanguage
				from (
					select n.idParent, f.idNode, n.Name, f.depth, n.idNodeType,
							if(isnull(n.SharedWorkflow), 0, 1) as isLink,
							n.SharedWorkflow as linkTarget
					from FastTraverse f left join Nodes n using(idNode)
					where f.idChild = $id and f.idNode > 1 and f.idNode <> $id
					union
					select n.idParent, n.idNode, n.name, '-1' as depth, n.idNodeType,
							if(isnull(n.SharedWorkflow), 0, 1) as isLink,
							n.SharedWorkflow as linkTarget
					from Nodes n
					where n.idnode = $id
				) t left join NodeTypes nt using(idNodeType)
					left join StructuredDocuments sd on t.idNode = sd.idDoc
				order by t.depth desc";

//		dump($sql);

		$db->Query($sql);
		$path = '';
		$i = 0;

		while (!$db->EOF) {

			$res[$i] = array();
			$res[$i]['idnode'] = $db->getValue('idNode');
			$res[$i]['idparent'] = $db->getValue('idParent');
			$res[$i]['idnodetype'] = $db->getValue('idNodeType');
			$res[$i]['nodetype'] = $db->getValue('nodeType');
			$res[$i]['nodeclass'] = $db->getValue('nodeClass');
			$res[$i]['name'] = $db->getValue('Name');
			$res[$i]['isdir'] = (boolean)$db->getValue('isDir');
			$res[$i]['islink'] = (boolean)$db->getValue('isLink');
			$res[$i]['rlpath'] = $db->getValue('linkTarget');
			$res[$i]['idlanguage'] = $db->getValue('idLanguage');
			$res[$i]['icon'] = $db->getValue('icon');

			$path .= "/{$res[$i]['name']}";
			$db->Next();
		}

		// $res contiene informacion sobre todos los nodos del path...
		// quizas no es buena idea perderlo...
		$res = end($res);
		$res['path'] = $path == '/ximDEX' ? '/' : $path;
		if (!empty($res['path'])) $this->set('exists', true);
		return $res;
	}


	/**
	 * Transforma un Path absoluto en ximDEX en un idNode y obtiene
	 * toda la informacion relativa al nodo.
	 *
	 * Version alternativa para antiguos servidores que no soportan subconsultas.
	 *
	 * @return array Devuelve un array con el valor de los atributos del nodo.
	 */
	function _pathToId_Alt() {

		$db =& $this->_db;
		$path = $this->get('path');
		$resources = explode('/', $path);

		// Esta es la raiz de ximDEX...
		if ($path == '/') {
			$this->set('idnode', 1);
			$res = $this->_idToPath();
			return $res;
		}

		// explode() inserta un elemento vacio si el path es absoluto.
		// ... y debe ser absoluto ...
		if ($path[0] == '/') array_shift($resources);
//		dump($resources);

		$total = count($resources);
		$i = 0;
		$res = array();

		while ($i < $total) {

			$parentName = $resources[$i];	// Nombre del padre del nodo, el segundo elemento del array

			// Si es el primer elemento el padre es ximDEX
			$aux = isset($res[$i-1]['idparent']) ? $res[$i-1]['idparent'] : null;
			$parentId = ($i > 0) ? $aux : '1';

			// Si la ruta solo incluye un nivel el padre sera siempre ximDEX
			// TODO: Arreglar la asignacion de $nodeName
			$aux = isset($resources[$i+1]) ? $resources[$i+1] : null;
			$nodeName = ($total > 1) ? $aux : $parentName;
			$selIdNode = ($total > 1) ? 'n.idNode' : 'f.idNode';


			// Se obtiene el idNode cuyo padre es $parentId...
			$sql = "select $selIdNode as idParent
					from FastTraverse f left join Nodes n on f.idChild = n.idNode
					where f.depth = 1
						and n.name = '$parentName'
						and f.idNode = $parentId";

			$db->Query($sql);
			if (!$db->EOF) $idParent = $db->getValue('idParent');

			// Se obtienen los datos del nodo cuyo padre es el nodo calculado anteriormente
			$sql = "select n.idNode, n.idParent, n.idNodeType, n.Name,
							if(nt.isFolder = 1 or nt.isVirtualFolder = 1, 1, 0) as isDir,
							nt.Name as nodeType, nt.class as nodeClass, nt.icon as icon,
							if(isnull(n.SharedWorkflow), 0, 1) as isLink,
							n.SharedWorkflow as linkTarget,
							sd.idLanguage
					from Nodes n left join NodeTypes nt using(idNodeType)
						left join StructuredDocuments sd on n.idNode = sd.idDoc
					where idParent = $idParent
							and n.Name = '$nodeName'";


			$db->Query($sql);
			if (!$db->EOF && $db->numRows == 1) {
				$res[$i] = array();
				$res[$i]['idnode'] = $db->getValue('idNode');
				$res[$i]['idparent'] = $db->getValue('idParent');
				$res[$i]['idnodetype'] = $db->getValue('idNodeType');
				$res[$i]['nodetype'] = $db->getValue('nodeType');
				$res[$i]['nodeclass'] = $db->getValue('nodeClass');
				$res[$i]['name'] = $db->getValue('Name');
				$res[$i]['isdir'] = (boolean)$db->getValue('isDir');
				$res[$i]['islink'] = (boolean)$db->getValue('isLink');
				$res[$i]['rlpath'] = $db->getValue('linkTarget');
				$res[$i]['idlanguage'] = $db->getValue('idLanguage');
				$res[$i]['icon'] = $db->getValue('icon');
			}

			$i++;
//			dump($sql);

		}

		// $res contiene informacion sobre todos los nodos del path...
		// quizas no es buena idea perderlo...
//		dump($res);
		$res = end($res);
		// Si no coincide el nombre del nodo es que este no existe
		if ($res['name'] == basename($path)) $this->set('exists', true);
		return $res;
	}

	/**
	 * Transforma un idNode en un Path absoluto en ximDEX y obtiene
	 * toda la informacion relativa al nodo.
	 *
	 * Version alternativa para antiguos servidores que no soportan subconsultas.
	 *
	 * @return array Devuelve un array con el valor de los atributos del nodo.
	 */
	function _idToPath_Alt() {

		$db =& $this->_db;
		$id = $this->get('idnode');
		$res = array();

		// Se obtiene la informacion basica de cada nodo que compone el path
		$sql = "select n.idParent, f.idNode, n.Name, f.depth, n.idNodeType,
						if(isnull(n.SharedWorkflow), 0, 1) as isLink,
						n.SharedWorkflow as linkTarget
				from FastTraverse f left join Nodes n using(idNode)
				where f.idChild = $id and f.idNode > 1 and f.idNode <> $id
				union
				select n.idParent, n.idNode, n.name, '-1' as depth, n.idNodeType,
						if(isnull(n.SharedWorkflow), 0, 1) as isLink,
						n.SharedWorkflow as linkTarget
				from Nodes n
				where n.idnode = $id
				order by depth desc";

//		dump($sql);

		$db->Query($sql);
		$path = '';
		$i = 0;

		while (!$db->EOF) {

			$res[$i] = array();
			$res[$i]['idnode'] = $db->getValue('idNode');
			$res[$i]['idparent'] = $db->getValue('idParent');
			$res[$i]['idnodetype'] = $db->getValue('idNodeType');
//			$res[$i]['nodetype'] = $db->getValue('nodeType');
//			$res[$i]['nodeclass'] = $db->getValue('nodeClass');
			$res[$i]['name'] = $db->getValue('Name');
//			$res[$i]['isdir'] = (boolean)$db->getValue('isDir');
			$res[$i]['islink'] = (boolean)$db->getValue('isLink');
			$res[$i]['rlpath'] = $db->getValue('linkTarget');

			$path .= "/{$res[$i]['name']}";
			$i++;
			$db->Next();
		}

		$total = count($res);
		for ($i=0; $i<$total; $i++) {

			$idNodeType = $res[$i]['idnodetype'];
			$idNode = $res[$i]['idnode'];

			// Se completan los datos del nodeType
			$sql = "select nt.Name as nodeType, nt.class as nodeClass,nt.icon as icon,
						if(nt.isFolder = 1 or nt.isVirtualFolder = 1, 1, 0) as isDir,
						sd.idLanguage
					from NodeTypes nt,
						StructuredDocuments sd
					where nt.idNodeType = $idNodeType
						and sd.idDoc = $idNode";

			$db->Query($sql);

			$res[$i]['nodetype'] = $db->getValue('nodeType');
			$res[$i]['nodeclass'] = $db->getValue('nodeClass');
			$res[$i]['isdir'] = (boolean)$db->getValue('isDir');
			$res[$i]['idlanguage'] = $db->getValue('idLanguage');
			$res[$i]['icon'] = $db->getValue('icon');
		}

		// $res contiene informacion sobre todos los nodos del path...
		// quizas no es buena idea perderlo...
		$res = end($res);
		$res['path'] = $path == '/ximDEX' ? '/' : $path;
		if (!empty($res['path'])) $this->set('exists', true);
		return $res;
	}

}

?>