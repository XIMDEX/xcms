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




ModulesManager::file('/actions/composer/Action_composer.class.php');


class Datasource_tags extends AbstractDatasource implements IDatasource {

	private $fc = null;

	public function __construct($conf=array()) {

	}

	public function read($request, $recursive = true) {
		
		$bpath = $request->getParam('nodeid');
		$getChildren = $request->getParam('children');
		
		$entity = XVFS::read($bpath);
		$children = array();
		
		if ($getChildren === true && $recursive) {
			$collection = $entity->get('collection');
			
			if (sizeof($collection) > 0) {
			
				foreach ($collection as $childPath) {
			
					$child = XVFS::read($childPath);
					if (!is_object($child)) {
				
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

	public function search($request, $bpath = '/Tags', $descend=true) {

		$result = XVFS::search($request->getParam('query'), $bpath, $descend);

		foreach ($result['data'] as $element) {

			$entity['idnode'] = $element['TAGID'];
			$entity['nodeid'] = '/Tags/' . $element['TAGID'];
			$entity['name'] = $element['TAGNAME'];
			$entity['exists'] = true;
			$entity['idparent'] = $element['PARENTID'];
			
			$entities[] = $this->normalizeNode($entity);
		}

		$result['data'] = isset($entities) ? $entities : array();

		return $result;
	}

	protected function normalizeNode($node) {

		$aa = is_array($node) ? NULL : str_replace('/', '', $node);

//		$node['parentid'] = is_array($node) && isset($node['idparent']) ? $node['idparent'] : '/Tags';
		$node['nodetypeid'] = is_array($node) && isset($node['idnodetype']) ? $node['idnodetype'] : $aa;
		$node['name'] = is_array($node) && isset($node['name']) ? $node['name'] : $aa;
		$node['text'] = is_array($node) && isset($node['name']) ? $node['name'] : $aa;
		$node['icon'] = 'folder.png';

		return $node;
	}
	
	public function parents($request) {
		error_log('DS_tags::parents');
	}

	public function nodetypes($request) {
		error_log('DS_tags::nodetypes');
	}

	public function write($request) {
		error_log('DS_tags::write');
	}
}
?>
