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



ModulesManager::file('/inc/model/Links.inc');

class Action_linkreport extends ActionAbstract {

	const ITEMS_PER_PAGE = '20';

	function index () {

     	$idNode = $this->request->getParam("nodeid");
		$actionID = $this->request->getParam("actionid");

		$values = array(
			'id_node' => $idNode,
			'actionID' => $actionID,
			'go_method' => 'searchresult'
		);

		$this->render($values, NULL, 'default-3.0.tpl');
		$this->addCss('/actions/linkreport/resources/css/linkreport.css');

    }

	function searchresult() {

		$idNode = $this->request->getParam("nodeid");
		$actionID = $this->request->getParam("actionid");

		$node = new Node($idNode);

		$this->addCss('/actions/linkreport/resources/css/linkreport.css');
		$this->addJs('/actions/linkreport/resources/js/listHandler.js');

		$values = array(
			'id_node' => $idNode,
			'id_action' => $actionID,
			'node_name' => $node->GetNodeName(),
			'field' => $this->request->getParam("field"),
			'criteria' => $this->request->getParam("criteria"),
			'stringsearch' => $this->request->getParam("stringsearch"),
			'all' => ($this->request->getParam('all') != 'on' ? NULL : 1),
			'rec' => ($this->request->getParam('rec') != 'on' ? NULL : 1)
		);

		$this->render($values, NULL, 'default-3.0.tpl');
	}

	function get_links() {

		$timeout=200;//microseconds
     	$idNode = $this->request->getParam("nodeid");
		$actionID = $this->request->getParam("actionid");
		$field = $this->request->getParam("field");
		$criteria = $this->request->getParam("criteria");
		$stringsearch = $this->request->getParam("stringsearch");
		$all = $this->request->getParam('all');
		$rec = $this->request->getParam('rec');
		$page = $this->request->getParam('page');
		$items = $this->request->getParam('items');

		$criteria = $criteria == 'undefined' ? NULL : $criteria;
		$field = $field == 'undefined' ? NULL : $field;
//		$stringsearch = $stringsearch == 'undefined' ? NULL : $stringsearch;
		$items = (!isset($items) || $items == 'undefined')  ? self::ITEMS_PER_PAGE : $items;
		$page = (!isset($page) || $page == 'undefined')  ? 1 : $page;

		$userID = XSession::get("userID");
		$node = new Node($idNode);

		// get link folders

		$folderList = empty($rec) ? array($idNode) : self::folderNodes($idNode);

		// get links

		$ximLinks = array();

		$nodeType = new NodeType();
		$nodeType->setByName('Link');
		$idNodeType = $nodeType->get('IdNodeType');

		$nodesTableCondition = 'IdNodeType = %s';

		if (!empty($stringsearch)) {

			$linksTableCondition = " AND $field";

			switch ($criteria) {
				case "contains":
					$linksTableCondition .= " like '%%$stringsearch%%'";
					break;
				case "nocontains":
					$linksTableCondition .= " not like '%%$stringsearch%%'";
					break;
				case "equal":
					$linksTableCondition .= " = '$stringsearch'";
					break;
				case "nonequal":
					$linksTableCondition .= " != '$stringsearch'";
					break;
				case "startswith":
					$linksTableCondition .= " like '$stringsearch%%'";
					break;
				case "endswith":
					$linksTableCondition .= " like '%%$stringsearch'";
					break;
				default:
					$linksTableCondition .= " = '$stringsearch'";
					break;
			}

			$nodesTableCondition .= $field != 'Url' ? $linksTableCondition : '';
		}

		$links = array();
		foreach ($folderList as $idFolder) {

			$finds = $node->find('IdNode', $nodesTableCondition . ' AND IdParent = %s', array($idNodeType, $idFolder), MONO);

			if ($field == 'Url') {
				$link = new Link();
				$finds = $link->find('IdLink', 'IdLink in (' . implode(',', $finds) . ') '. $linksTableCondition, NULL, MONO);
			}

			if (!empty($finds) && sizeof($finds) > 0) {
				$links = array_merge($links, $finds);
			}
		}

		$data = array('results' => array());
		$records = sizeof($links);

		if ($records > 0) {

			$pages = array_chunk($links, $items);

			foreach ($pages as $chunk) {

				foreach ($chunk as $idLink) {

					$link = new Link($idLink);
					$found =  $link -> get('ErrorString');

					$user = new User($userID);
					$arr_roles = $user->GetRolesOnNode($idNode);
					$n_roles = count($arr_roles);
					$r = 0;
					$has = false;

					while (($r<$n_roles) && !$has) {
						$role = new Role($arr_roles[$r]);
						$has = $role->HasAction(6073);
						$r++;
					}

					$linkNode = new Node($idLink);

					$ximLinks[] = array('nodeid' => array('value' => $idLink), 'name' => $linkNode->get('Name'),
						'has' => $has, 'desc' => $linkNode->get('Description'),
						'url' => $link->get('Url'), 'found' => $found);
				}

				if(!empty($ximLinks) ) {
					$data['results'][$page] = $ximLinks;
					$page++;
				}
				$ximLinks = array();
			}
		}

		$data['records'] = $records;
		$data['items'] = $items;
		$data['pages'] = isset($pages) ? sizeof($pages) : 1;
		$this->sendJSON($data);
	}

	private function folderNodes($idNode) {

		$node = new Node($idNode);
		$childList = $node->GetChildren();

		$nodeList = array($idNode);

		if (count($childList) > 0) {
			foreach($childList as $idChild) {

				$childNode = new Node($idChild);

				if ($childNode->nodeType->get('Name') == "LinkFolder") {

					$nodeList = array_merge($nodeList, self::folderNodes($idChild));
				}
			}
		}

		return $nodeList;
	}
}
?>

