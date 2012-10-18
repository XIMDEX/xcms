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



require_once(XIMDEX_ROOT_PATH . '/inc/repository/NodeEntity.class.php');

class NodeEntity_Dir extends NodeEntity {

	/**
	 * @var array Nodos hijos
	 */
	var $_collection;
	
	/**
	 * 
	 */
	var $_reset;
	
	
	/**
	 * Constructor. Acepta un idNode o un Path como parametro a partir del
	 * cual se obtendra el resto de informacion del nodo.
	 * 
	 * @param mixed node IdNode o Path absoluto en ximDEX
	 */
    function NodeEntity_Dir($node=null) {
    	
    	parent::NodeEntity($node);
    	
    	$this->_collection = null;
    	
    	if (!$this->get('exists')) {
	    	// Si no existe indico que es un directorio -> Se esta creando...
    		$this->set('isdir', true);
    	} else if ($this->get('exists') && !$this->get('isdir')) {
    		// Si el nodo existe y no es un directorio se limpia la estructura
    		$this->clear();
    	}
    }
    
    /**
     * Devuelve el descriptor del directorio
     * TODO: Redefinir este metodo
     * 
     * @return string
     */
    function getDescriptor() {
    	
    	// Si esta cacheado se devuelve directamente
    	$descriptor = $this->get('descriptor');
    	if (!is_null($descriptor)) return $descriptor;
    	
    	$descriptor = $this->_tmpdir . '/' . md5($this->getMIME());
    	//'/tmp/xvfs_' . $this->get('name');
		if (!file_exists($descriptor)) {
			$ret = mkdir($descriptor);
			if (!$ret) return $ret;
		}
		
		// Se cachea el descriptor
		$this->set('descriptor', $descriptor);
    	return $descriptor;
    }
    
    /**
     * Devuelve el tipo mime del nodo, este siemrpe sera un directorio
     * 
     *@return string
     */
    function getMIME() {
    	$mime = 'httpd/unix-directory';
    	return $mime;
    }
    
    /**
     * Devuelve la coleccion de hijos del nodo como un array de IDs.
     * 
     * @return array
     */
    function & getCollection() {
    	
    	$this->_db = new DB();
    	$db =& $this->_db;
    	$id = $this->get('idnode');
    	
   		$ret = array();
    	
    	// Esta consulta obtiene si el nodo es o no un directorio, pero ...
//    	$sql = "select n.idParent, n.idNode, n.Name, n.idNodeType,
//					if(nt.isFolder = 1 or nt.isVirtualFolder = 1 or nt.System = 1, 1, 0) as isDir
//				from FastTraverse f left join (
//					Nodes n left join NodeTypes nt using(idNodeType)
//				) on n.idNode = f.idChild
//				where f.idNode = $id
//					and n.idParent = $id
//					and f.depth = 0";
    	
    	// ... ya que unicamente se devolvera el idNode, se simplifica la consulta...    	
    	$sql = "select n.idParent, n.idNode, n.Name, n.idNodeType
				from FastTraverse f left join Nodes n on n.idNode = f.idChild
				where f.idNode = $id
					and n.idParent = $id
					and f.depth = 1";
		
		$db->Query($sql);
		while (!$db->EOF) {
			
			// TODO: Sera necesaria la comprobacion para links
			$idNode = $db->getValue('idNode');
			$ret[] = $idNode;
			
			$db->Next();
		}
		
		unset($this->_db);
		return $ret;
    }
    
    /**
     * Establece la coleccion de nodos hijos
     * 
     * @param array collection
     */
    function setCollection($collection) {
    	$this->_collection = $collection;
    }
    
    /**
     * Itera sobre la coleccion de hijos del nodo.
     * Devuelve un objeto Entidad o FALSE en caso de que no se encuentren hijos.
     * 
     * @return object
     */
    function & next() {
    	
    	$collection =& $this->_collection;
    	
    	if (is_null($collection)) {
    		
    		$collection = $this->getCollection();
    		$this->_reset = true;
    	}
    		
    	if ($this->_reset) {
    		$entity = current($collection);
    		$this->_reset = false;
    	} else {    		
    		$entity = next($collection);
    	}
    	
		$entity = NodeEntity::getEntity($entity);
		
    	if (is_null($entity)) {
    		$childrens = null;
    		$entity = false;
    	}
    	return $entity;    	
    }
    
    /**
     * Resetea la coleccion de hijos. En lugar de devolver al principio el
     * puntero se obliga a que se recargue, de esta forma se actualizan
     * posibles cambios en los nodos hijos.
     */
    function reset() {
    	unset($this->_collection);
    }
    
}

?>