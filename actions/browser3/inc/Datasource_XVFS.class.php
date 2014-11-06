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



ModulesManager::file('/inc/xvfs/XVFS.class.php');

 // Data source for XVFS connection.
 
class Datasource_XVFS extends AbstractDatasource implements IDatasource {
	
	private $mountpoints = null;
	private $instance = null;
	
	public function __construct($conf=array()) {
		parent::__construct($conf);
		foreach ($this->conf['MOUNTPOINTS'] as $mp) {
			$ret = XVFS::mount($mp['mountpoint'], $mp['uri']);
			if (!$ret) {
				// Log this
			}
		}
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
		
		$entity = XVFS::read($bpath);
		if (!$entity) {
			return NULL;
		}
		$children = array();
		
		if ($getChildren === true && $recursive) {
			$collection = $entity->get('collection');
			
			if (sizeof($collection) > 0) {
			
				foreach ($collection as $childPath) {
			
					$child = XVFS::read($childPath);

					if (!is_object($child)) {
				
						// Probably a socket file or something like that
						XMD_Log::info(_("The path cannot be read") . $childPath);
					
					} else {
				
						$children[] = $this->normalizeNode($child->asArray());
					}
				}
			}
		}
		
		$entity->set('collection', $children);
		$entity = $this->normalizeNode($entity->asArray());
		return $entity;
	}
	
	protected function normalizeNode($node) {
	
//		$node['text'] = $node['name'];
//		unset($node['name']);
		// Keep the node id for normalizeEntities
		$node['__nodeid'] = isset($node['idnode']) ? $node['idnode'] : null;
		
		$node['nodeid'] = is_numeric($node['__nodeid']) ? $node['__nodeid'] : $node['bpath'];
		unset($node['idnode']);
		
		$node['nodetypeid'] = isset($node['idnodetype']) ? $node['idnodetype'] : null;
		unset($node['idnodetype']);
		$node['parentid'] = isset($node['idparent']) ? $node['idparent'] : null;
		unset($node['idparent']);
		
		if (!isset($node['icon'])) {
			if (isset($node['isdir'])) {
				$node['icon'] = 'folder.png';
			} else {
				$node['icon'] = 'doc.png';
			}
		}

		return $node;
	}
	
	public function parents($request) {
		
		$data = array('node' => array());
		
		$bpath = $request->getParam('nodeid');
		$entity = XVFS::read($bpath);
		
		if (!is_object($entity)) {
			// Probably a socket file or something like that
			XMD_Log::info(_("The path cannot be read") . $bpath);
			return $data;
		}

		$data['node']['name'] = $entity->get('name');
		$data['node']['nodeid'] = $entity->get('bpath');
		$data['node']['path'] = $entity->get('bpath');
		$data['node']['parents'] = array();

		$parentPath = dirname($entity->get('bpath'));
		while ($parentPath !== false) {

			$entity = XVFS::read($parentPath);

			$data['node']['parents'][] = array(
				'name' => $entity->get('name'),
				'nodeid' => $entity->get('bpath'),
				'isdir' => '1'
			);

			$p = dirname($parentPath);
			if ($p == $parentPath) {
				$parentPath = false;
			} else {
				$parentPath = $p;
			}
		}
		
		return $data;
	}
	
	public function nodetypes($request) {
		// Returning the system nodetypes
		$ds = GenericDatasource::getDatasource(GenericDatasource::DS_COMPOSER);
		return $ds->nodetypes($request);
	}
	
	public function search($request) {
		/*$ds = GenericDatasource::getDatasource(GenericDatasource::DS_TOLDOX);
        return $ds->search($request);*/

	}
	
	public function write($request) {
	
	}

}

?>