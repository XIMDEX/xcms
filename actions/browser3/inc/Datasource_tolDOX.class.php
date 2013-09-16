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




ModulesManager::file('/actions/browser3/inc/Datasource_XVFS.class.php');
ModulesManager::file('/inc/Toldox.class.php', 'tolDOX');

 //** Data source for TOL connection.
  
class Datasource_tolDOX extends Datasource_XVFS implements IDatasource {
	
	protected $sir = null;
	
	public function __construct($conf=array()) {
		parent::__construct($conf);
		$this->sir = new Toldox();
	}
	
	public function read($request, $recursive = true) {

		$bpath = $request->getParam('nodeid');
		$getChildren = $request->getParam('children');
		$from = $request->getParam('from');
		$to = $request->getParam('to');
		$items = $request->getParam('items');
		$find = $request->getParam('find');
		
		// Pagination
		
		if (is_numeric($bpath)) {
			$node = new Node($bpath);
			$bpath = $node->getPath();
			$bpath = preg_replace('#^\/ximDEX#', '', $bpath);
		}

		
		$entity = $this->sir->read($bpath, Toldox::getToldoxRoot(), true);
//		$entity = XVFS::read($bpath);
//		if ($entity !== null && !($entity->get('idnode') > 0)) {
//			$entity = $this->sir->importEntity($entity, Toldox::getToldoxRoot(), true, XVFS::getContent($bpath));
//		}
		
		if (!is_object($entity)) {
			return array();
		}
		$children = array();
		
		if ($getChildren === true && $recursive) {
			foreach ($entity->get('collection') as $childPath) {
		
				$child = $this->sir->read($childPath, Toldox::getToldoxRoot(), true);
//				$child = XVFS::read($childPath);
//				if ($child !== null && !($child->get('idnode') > 0)) {
//					$child = $this->sir->importEntity($child, Toldox::getToldoxRoot(), true, XVFS::getContent($bpath));
//				}
				
				if (!is_object($child)) {
			
					// Probably a socket file or something like that
					XMD_Log::info(_("The path cannot be read") . $childPath);
				
				} else {
			
					$children[] = $this->normalizeNode($child->asArray());
				}
			}
		}
		
		$entity->set('collection', $children);
		$entity = $this->normalizeNode($entity->asArray());
		return $entity;
	}
	
	public function search($request) {
		
		$handler = $request->getParam('handler');
		$query = $request->getParam('query');

		if (is_string($query)) {
			$query = stripslashes($query);
		}
		$data = $this->sir->search($query);
		
		if (!is_array($data['data'])) {
			$data['data'] = array();
		}
		
		for ($i=0, $l=count($data['data']); $i<$l; $i++) {
			$data['data'][$i] = $this->normalizeSearchEntity($data['data'][$i]);
		}
		
		return $data;
	}
	
	protected function normalizeSearchEntity($entity) {
	
		$data = $entity->asArray();

		$data['children'] = 0;
		$data['isdir'] = isset($data['isdir']) ? $data['isdir'] : 0;
//		$data['name'] = false;
		$data['__nodeid'] = isset($data['nodeid']) ? $data['nodeid'] : null;
		$data['nodeid'] = $data['bpath'];
		$data['nodetype'] = 0;
		$data['nodetypeid'] = 0;
//		$data['parentid'] = 0;
		$data['relpath'] = $data['bpath'];
		$data['abspath'] = $data['bpath'];
		$data['icon'] = 'doc.png';
		
		return $data;		
	}

}

?>
