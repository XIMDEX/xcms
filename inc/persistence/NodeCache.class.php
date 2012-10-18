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



 

require_once(XIMDEX_ROOT_PATH . '/inc/persistence/XSession.class.php');

/**
 * Mantiene un cache de nodos en variables de sesion.
 * Se almacenan arrays con la informacion de los nodos cuya clave es el path.
 * Ademas, dentro del array node_index, se guardan referencias a las estructuras
 * anteriores pero segun su idNode.
 */
class NodeCache {
	
	function NodeCache() {
		die('La clase NodeCache no se puede instanciar.');
	}
	
	/**
	 * Devuelve un valor boleano que indica si el nodo esta cacheado.
	 * 
	 * @param mixed node Nodo a consultar, puede ser el path o el ID.
	 * @return boolean
	 */
	function exists($node) {
		
		$ret = is_null(NodeCache::select($node)) ? false : true;
		return $ret;
	}
	
	/**
	 * Devuelve la informacion de un nodo cacheado.
	 * 
	 * @param mixed node Nodo a consultar, puede ser el path o el ID.
	 * @return boolean Devuelve la estructura con la informacion del nodo o null si no esta cacheado
	 */
	function select($node) {
		
		$ret = null;
		
		if (is_numeric($node)) {
		
			// Se asume que se consulta por un idNode
			$node_index = XSession::get('node_index');
			if (!is_array($node_index)) return null;
			
			if (array_key_exists($node, $node_index)) $ret = $node_index[$node];
		
		} else {
			
			// Se consulta por un Path
			$ret = XSession::get($node);
		}
		
		return $ret;
	}
	
	/**
	 * Cachea un nodo
	 * 
	 * @param array node Array con la informacion del nodo que se va a cachear
	 */
	function insert($node) {
		
		$id = $node['nodeid'];
		$path = $node['bpath'];
		
		$node_index = null;
		if (XSession::exists('node_index')) $node_index = XSession::get('node_index');
		if (!is_array($node_index)) $node_index = array();
		
		XSession::set($path, &$node);
		
		$node_index[$id] = &$node;
		XSession::set('node_index', &$node_index);		
	}
	
	/**
	 * elimina un nodo del cache
	 * 
	 * @param mixed node Nodo a consultar, puede ser el path o el ID.
	 */
	function delete($node) {
				
		$node = NodeCache::select($node);
		if (is_null($node)) return;
		
		$id = $node['nodeid'];
		$path = $node['bpath'];
		
		$node_index = null;
		if (XSession::exists('node_index')) $node_index = XSession::get('node_index');
		if (!is_array($node_index)) $node_index = array();
		
		if (array_key_exists($id, $node_index)) unset($node_index[$id]);
		XSession::set('node_index', &$node_index);
		
		if (XSession::exists($path)) XSession::delete($path);
		
	}
	
}
 
?>
