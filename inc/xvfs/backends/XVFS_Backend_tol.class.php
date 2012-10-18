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



require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_interface.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_ftp.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_file.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryProcessor.class.php');


/**
 * @brief Backend for access to repository of the module tolDOX.
 *
 * Implementation of ximDEX backend.
 *
 * NOTE: Las operaciones que crean entidades no devuelven un error si el recurso
 * indicado ya existe en el repositorio, sino que devuelven un codigo indicando que
 * el metodo se ejecuto correctamente.
 * Esto es un workaround necesario cuando se llama al backend desde webDAV.
 *
 */
class XVFS_Backend_tol
	extends XVFS_Backend_file
	implements Backend_XVFS_interface, Backend_XVFS_interface_searcheable {

	function __construct($uri) {
		$uri['scheme'] = 'file';
		
		parent::__construct($uri);
	}
	
	/**
	* @param string query
	* @brief Interface for implement the search in a XVFS backend.
	*/

	function search($query) {

		$handler = isset($_POST['handler']) ? $_POST['handler'] : 'TOL';

		$qp = QueryProcessor::getInstance($handler);

		if (is_string($query)) {
			$query = $qp->getQueryOptions(stripslashes($query));
		}
		
		
		// Workaround: Normalize filters conditions
		
		for ($i=0, $l=count($query['filters']); $i<$l; $i++) {
			$filter =& $query['filters'][$i];
			switch (strtoupper($filter['field'])) {
				case 'NAME':
					$filter['field'] = 'path';
					$filter['comparation'] = 'contains';
					break;
			}			
		}
		
		if (isset($query['sorts'])) {

			for ($i=0, $l=count($query['sorts']); $i<$l; $i++) {
				$filter =& $query['sorts'][$i];
				switch (strtoupper($filter['field'])) {
					case 'NAME':
						$filter['field'] = 'path';
						break;
				}			
			}
		}
		
		// Workaround: Normalize filters conditions
		$result = $qp->search($query);

		foreach ($result['data'] as $element) {

			$path = $element['PATH'];
			$entity = $this->read($path);

			if (empty($entity)) {
				XMD_Log::warning('Empty entity detected for path ' . $path);
				continue;
			}
			
			$entity->set('tolid', $element['DOCID']);
			$entity->set('titulo', $element['TITULO']);
			$entity->set('nombretipo', $element['NOMBRETIPO']);
			$entity->set('publicado', $element['PUBLICADO'] == 1 ? 'Si' : 'No');
			$entity->set('fechaalta', $element['FECHAALTA']);
			$entity->set('fecharevision', $element['FECHAREVISION']);
			$entity->set('fechadocumento', $element['FECHADOCUMENTO']);
			
			$entities[] = $entity;
		}
		
		$result['data'] = isset($entities) ? $entities : array();
		return $result;
	}
	
	function count($query) {
		$handler = isset($_POST['handler']) ? $_POST['handler'] : 'TOL';
		$qp = QueryProcessor::getInstance($handler);
		$data = $qp->count($query);

		return $data;
		
	}
	

}

?>
