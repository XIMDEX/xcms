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



/**
*  @brief Class for access to virtual folders of the backend xnodes.
*/

class XVFS_VirtualPathAdapter {

	/**
	* @var array Rutas base de los RootContainers
	*/
	var $_base;

	/**
	* @var array Tabla de objetos virtuales
	*/
	var $_entities;

	/**
     * @var array Tabla de indices de entidades reales
     */
    var $_rentities;

    /**
     * @var array Coleccion de idiomas existentes en el sistema
     */
    var $_languages;

    /**
     * @var array Coleccion de canales existentes en el sistema
     */
    var $_channels;


    /**
     * Constructor.
     * Inicializa la tabla de entidades con la raiz de todos los StructuredDocument.
     */
    function XVFS_VirtualPathAdapter($key=false) {

    	if ($key != M_PI) die('Use XVFS_VirtualPathAdapter::getInstance()');
    	$this->update();
    }

    /**
     * Obtiene la instancia de la clase
     */
    function & getInstance() {

    	static $_instance = null;

    	if (is_null($_instance)) {
    		$_instance = new XVFS_VirtualPathAdapter(M_PI);
    	}

    	return $_instance;
    }

    /**
     * Recarga la estructura virtual de documentos
     */
    function update() {

    	$this->_indexes = array();
    	$this->_entities = array();
    	$this->_base = array();

    	$this->_loadLanguages();
    	$this->_loadChannels();
    	$this->_loadXmlRootFolders();
    }

    /**
     * Devuelve un objeto NodeEntity que hace referencia a un nodo virtual.
     *
     * @param node node Path virtual del nodo
     * @return object
     */
    function & getEntity($node) {

		$entity = null;

		// En primer lugar se comprueba si la entidad ya esta cacheada
		if (isset($this->_entities[$node])) {
			$entity =& $this->_entities[$node];
		}

		if (!is_null($entity)) {
			// Si la entidad representa un canal se carga la coleccion entidades descendientes
			if ($entity->get('ischannel')) {
				$this->_loadXmlDocuments($entity);
			}
			return $entity;
		}

		// La entidad no esta cacheada, se comprueba si es una ruta virtual
		$base = $this->isVirtual($node);
		if (false === $base) return $entity;

		// La ruta es virtual, se comprueba si el padre existe y es virtual, si no es asi
		// la ruta indicada no puede ser virtual
    	$parent = dirname($node);
    	if (!isset($this->_entities[$parent])) return $entity;

    	// A traves del padre se obtiene la coleccion de entidades descendientes y se comprueba que
    	// la ruta indicada esta en esta coleccion
    	$parent = $this->_entities[$parent];
		$this->_loadXmlDocuments($parent);
		if (isset($this->_entities[$node])) $entity =& $this->_entities[$node];

		return $entity;
    }

	/**
	 * Indica si un path pertenece al sistema virtual de nodos basandose
	 * en la ruta base de un StructuredDocument.
	 * Devuelve FALSE si la ruta no pertenece al sistema virtual o la ruta
	 * del nodo raiz por el contrario.
	 *
	 * @param node node Path a comprobar
	 * @return mixed
	 */
	function isVirtual($node) {

		$exists = false;
		$keys = array_keys($this->_base);

		$i = 0;
		$count = count($keys);

		while ($i < $count && false === $exists) {

			// TODO: Revisar condiciones regexp
			$regexp = "#^{$keys[$i]}/#m";
			if (preg_match($regexp, "$node/") > 0) {
				$exists = $keys[$i];
			}
			$i++;
		}

//		if (is_null($exists)) $exists = false;
		return $exists;
	}

    /**
     * Obtiene todos los idiomas disponibles
     */
    function _loadLanguages() {

    	$sql = 'select idLanguage, isoName as lang from Languages';
    	$db = new DB();
    	$db->query($sql);
    	$this->_languages = array();

    	while (!$db->EOF) {
    		$this->_languages[$db->getValue('idLanguage')] = $db->getValue('lang');
    		$db->next();
    	}
    }

    /**
     * Devuelve un array con todos los nodos disponibles
     */
    function getLanguages() {
    	return $this->_languages;
    }

    /**
     * Obtiene todos los canales disponibles
     */
    function _loadChannels() {

    	$sql = 'select idChannel, DefaultExtension as channel from Channels';
    	$db = new DB();
    	$db->query($sql);
    	$this->_channels = array();

    	while (!$db->EOF) {
    		$this->_channels[$db->getValue('idChannel')] = $db->getValue('channel');
    		$db->next();
    	}
    }

    /**
     * Devuelve un array con todos los canales disponibles
     */
    function getChannels() {
    	return $this->_channels;
    }

    /**
     * Obtiene todos los nodos RootFolders, necesario para tener una base y
     * decidir que documento pertenece a este sistema virtual.
     */
    function _loadXmlRootFolders() {

    	// TODO: XmlRootFolder, XimletRootFolder, .... ???
    	$sql = 'select idNode from Nodes where idNodeType in (5018, 5054)';
    	$db = new DB();
		$db->Query($sql);

    	while (!$db->EOF) {

    		$idnode = $db->getValue('idNode');
    		$entity = NodeEntity::getEntity($idnode);
    		$path = $entity->get('path');

    		if (!isset($this->_base[$path])) {

				$entity->set('collection', array());
	    		$entity->set('isvirtualroot', true);
	    		$entity->set('languages', $this->getLanguages());
	    		$entity->set('channels', $this->getChannels());

	    		// Carga todos los XmlContainer descendientes
	    		$this->_loadXmlContainers($entity);
	    		$this->_entities[$path] = $entity;
	    		$this->_base[$path] =& $this->_entities[$path];
    		}

    		$db->next();
    	}
    }

    /**
     * Contenedores descendientes de los RootFolders
     */
    function _loadXmlContainers(&$container) {

    	$idcontainer = $container->get('idnode');
    	$sql = "select idnode from Nodes where idparent = $idcontainer";
    	$paths = array();
    	$entities = array();

    	$db = new DB();
    	$db->query($sql);

    	while (!$db->EOF) {

    		$idnode = $db->getValue('idnode');
    		$node = NodeEntity::getEntity($idnode);
    		$node->set('parentpath', $container->get('path'));
    		$node->set('xmlrootfolder', $container->get('path'));

    		if ($node->get('isdir')) {
	    		$node->set('collection', array());
	    		$node->set('isvirtualcontainer', true);
	    		// Se cargan todos los idiomas y canales disponibles
	    		$node->set('languages', $this->getLanguages());
	    		$node->set('channels', $this->getChannels());
    		}

    		$entities[] = $node;
    		$path = $node->get('path');
    		$paths[] = $path;

    		// Si es un directorio se carga la coleccion de rutas virtuales descendientes,
    		// estas son las rutas hacia cada idioma
    		if ($node->get('isdir')) $this->_loadLanguageCollection($node);
    		$this->_entities[$path] = $node;

    		$db->next();
    	}

    	$container->set('collection', $paths);
    }

    /**
     * Coleccion de rutas virtuales de directorios de idiomas
     */
    function _loadLanguageCollection(&$entity) {

    	$base = $entity->get('path');
    	$langs = $entity->get('languages');
    	$col = array();

    	foreach ($langs as $idlang => $lang) {

    		$lang_path = "$base/$lang";
    		$col[] = $lang_path;

    		$lang_entity = new NodeEntity_Dir($lang_path);
    		$lang_entity->set('parentpath', $entity->get('path'));
    		$lang_entity->set('xmlrootfolder', $entity->get('xmlrootfolder'));
    		$lang_entity->set('xmlcontainer', $entity->get('path'));
    		$lang_entity->set('name', $lang);
    		// TODO: Obtener el nodetype del idioma de alguna otra forma? -> sql
    		$lang_entity->set('idnodetype', 5400);
    		$lang_entity->set('nodetype', 'VirtualLanguage');
    		$lang_entity->set('nodeclass', 'foldernode');
    		$lang_entity->set('mimetype', 'httpd/unix-directory');
    		$lang_entity->set('isvirtual', true);
    		$lang_entity->set('isvirtuallanguage', true);
    		$lang_entity->set('idlanguage', $idlang);
    		$lang_entity->set('islanguage', true);
    		$lang_entity->set('ischannel', false);
    		$lang_entity->set('collection', array());
    		$lang_entity->set('channels', $entity->get('channels'));
    		$lang_entity->set('exists', true);

    		$this->_loadChannelCollection($lang_entity);
    		$this->_entities[$lang_path] = $lang_entity;
    	}

    	$entity->set('collection', $col);
    }

    /**
     * Coleccion de rutas virtuales de directorios de canales
     */
    function _loadChannelCollection(&$entity) {

    	$base = $entity->get('path');
    	$channels = $entity->get('channels');
    	$col = array();

    	foreach ($channels as $idchannel => $channel) {

    		$channel_path = "$base/$channel";
    		$col[] = $channel_path;

    		$channel_entity = new NodeEntity_Dir($channel_path);
    		$channel_entity->set('parentpath', $base);
    		$channel_entity->set('xmlrootfolder', $entity->get('xmlrootfolder'));
    		$channel_entity->set('xmlcontainer', $entity->get('xmlcontainer'));
    		$channel_entity->set('name', $channel);
    		$channel_entity->set('idnodetype', 5400);
    		$channel_entity->set('nodetype', 'VirtualChannel');
    		$channel_entity->set('nodeclass', 'foldernode');
    		$channel_entity->set('mimetype', 'httpd/unix-directory');
    		$channel_entity->set('isvirtual', true);
    		$channel_entity->set('isvirtualchannel', true);
    		$channel_entity->set('idlanguage', $entity->get('idlanguage'));
    		$channel_entity->set('idchannel', $idchannel);
    		$channel_entity->set('islanguage', false);
    		$channel_entity->set('ischannel', true);
    		$channel_entity->set('collection', array());
    		$channel_entity->set('exists', true);

    		$this->_entities[$channel_path] = $channel_entity;
    	}

    	$entity->set('collection', $col);
    }

    /**
     * Obtiene todas las entidades reales, los XmlDocument, segun el idioma y el canal.
     */
    function _loadXmlDocuments(&$channel) {

    	$idlang = $channel->get('idlanguage');
    	$idchannel = $channel->get('idchannel');
    	$parentpath = $channel->get('xmlcontainer');

    	if (!isset($this->_entities[$parentpath])) return;

    	$parent =& $this->_entities[$parentpath];
    	$idparent = $parent->get('idnode');

    	$sql = "select n.idNode
				from Nodes n left join StructuredDocuments sd on sd.idDoc = n.idNode
				left join RelStrDocChannels rc on sd.idDoc = rc.idDoc
				where n.idParent = $idparent
					and sd.idLanguage = $idlang
					and rc.idChannel = $idchannel";

    	$col = array();
    	$db = new DB();
    	$db->query($sql);

    	while (!$db->EOF) {

    		$node = NodeEntity::getEntity($db->getValue('idNode'));
    		// Path real
    		$node->set('rpath', $node->get('path'));
    		// Path virtual
    		$path = $channel->get('path') . '/' . $node->get('name');
    		$node->set('path', $path);
    		$node->set('idlanguage', $idlang);
    		$node->set('idchannel', $idchannel);
    		$node->set('xmlrootfolder', $channel->get('xmlrootfolder'));
    		$node->set('xmlcontainer', $channel->get('xmlcontainer'));

    		$col[] = $path;
    		$this->_entities[$path] = $node;

    		$db->next();
    	}

    	$channel->set('collection', $col);
    }

    /**
     * Devuelve informacion del contenido de una ruta virtual:
     *
     * 1.	Por una parte devuelve la informacion de los nodos existentes bajo esa
     * 		ruta, indicando sus idiomas y canales asociados.
     *
     * 2.	Por otra parte devuelve la estructura de directorios existente bajo
     * 		la ruta indicada, directorios de idiomas y canales que se encuentran
     * 		en niveles mas bajos.
     *
     * 3.	Tambien obtiene el contenedor, si existe, descendiente del RootFolder.
     */
    function & getXmlDocumentsInfo($vpath) {

    	$info = array();
    	$nodes = array();

    	$containerPath = null;
    	$rootPath = null;

    	$vpath = dirname($vpath);
    	$entity = $this->getEntity($vpath);

    	if (!is_null($entity)) {

	    	// Se obtiene la informacion de los nodos existentes bajo la ruta indicada
	    	$nodetype = strtoupper($entity->get('nodetype'));

//	    	logdump($nodetype);
	    	switch($nodetype) {
	    		case 'XMLROOTFOLDER':
	    		case 'XIMLETROOTFOLDER':
	    			// Se asume que se esta creando una nueva entidad,
	    			// no se devuelve ninguna informacion.
	    			$rootPath = $entity->get('path');
	    			break;

	    		case 'XMLCONTAINER':
	    		case 'XIMLETCONTAINER':
	    			// Todos los idiomas, todos los canales
	    			$containerPath = $entity->get('path');
	    			$rootPath = $entity->get('xmlrootfolder');
	    			$col = $entity->getCollection();
	    			foreach ($col as $idnode) {
	    				$node = NodeEntity::getEntity($idnode);
	    				$nodes[$idnode] = array();
	    				$nodes[$idnode]['idlanguage'] = $node->get('idlanguage');
	    				$nodes[$idnode]['channels'] = array_keys($node->get('channels'));
	    			}
	    			break;

	    		case 'VIRTUALLANGUAGE':
	    			// Un solo idioma, todos los canales
	    			$container = $entity->get('xmlcontainer');
	    			$containerPath = $container;
	    			$container = $this->getEntity($container);

	    			$rootPath = $entity->get('xmlrootfolder');

	    			$lang = $entity->get('idlanguage');

	    			$col = $container->getCollection();
	    			foreach ($col as $idnode) {
	    				$node = NodeEntity::getEntity($idnode);
	    				if (in_array($node->get('idlanguage'), array($lang))) {
	    					$nodes[$idnode] = array();
	    					$nodes[$idnode]['idlanguage'] = $lang;
	    					$nodes[$idnode]['channels'] = array_keys($node->get('channels'));
	    				}
	    			}
	    			break;

	    		case 'VIRTUALCHANNEL':
	    			// Un solo idioma, un solo canal
	    			$container = $entity->get('xmlcontainer');
	    			$containerPath = $container;
	    			$container = $this->getEntity($container);

	    			$rootPath = $entity->get('xmlrootfolder');

	    			$lang = $entity->get('idlanguage');
	    			$channels = array();
	    			$channels[] = $entity->get('idchannel');

	    			$col = $container->getCollection();
	    			foreach ($col as $idnode) {
	    				$node = NodeEntity::getEntity($idnode);
	    				if (in_array($node->get('idlanguage'), array($lang))) {
	    					$nodes[$idnode] = array();
	    					$nodes[$idnode]['idlanguage'] = $lang;
	    					$channels = array_intersect(array_keys($node->get('channels')), $channels);
	    					$nodes[$idnode]['channels'] = $channels;
	    				}
	    			}
	    			break;
	    	}

    	}

    	// Se obtiene la informacion de los idiomas y canales existentes bajo la ruta indicada.
    	// No se tienen en cuenta los posibles nodos que puedan existir, sino la estructura que
    	// existe bajo la ruta indicada.
    	$langs = $this->getLanguages();
    	$channels = $this->getChannels();

    	$paths = explode('/', $vpath);
    	array_shift($paths);
		$item = end($paths);

    	$pathinfo = array();

    	if (in_array($item, $langs)) {

    		// El ultimo elemento del path representa un idioma
    		$key = array_search($item, $langs);
    		$pathinfo['languages'] = array($key => $item);
    		$pathinfo['channels'] = $channels;

    		if (is_null($rootPath)) $rootPath = dirname(dirname($vpath));

    	} else if (in_array($item, $channels)) {

    		// El ultimo elemento del path representa un canal
    		$lang = prev($paths);
    		$key = array_search($lang, $langs);
			$pathinfo['languages'] = array($key => $lang);
    		$key = array_search($item, $channels);
    		$pathinfo['channels'] = array($key => $item);

    		if (is_null($rootPath)) $rootPath = dirname(dirname(dirname($vpath)));

    	} else {

    		// No se especifica el idioma ni el canal
    		$pathinfo['languages'] = $langs;
    		$pathinfo['channels'] = $channels;

    		if (is_null($rootPath)) $rootPath = dirname($vpath);

    	}

		$info['nodes'] = $nodes;
		$info['pathinfo'] = $pathinfo;
		$info['container'] = $containerPath;
		$info['rootfolder'] = $rootPath;
    	return $info;
    }

}

?>