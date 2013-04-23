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




ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/inc/persistence/XSession.class.php');
ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/parsers/ParsingXimMenu.class.php');
ModulesManager::file('/inc/model/orm/UnverifiedUsers_ORM.class.php');
ModulesManager::file('/actions/browser3/inc/GenericDatasource.class.php');


class Action_composer extends ActionAbstract {

	const COMPOSER_INDEX = 'loadaction.php';

	public function index() {
		XSession::check();

		$ximid = Config::GetValue("ximid");
		$versionname = Config::GetValue("VersionName");
		$userID = XSession::get('userID');
		$theme = $this->request->getParam('theme');
		$theme = $theme ? $theme : 'ximdex_theme';

		//Stopping any active debug_render
		XSession::set('debug_render', NULL);
		XSession::set('activeTheme', $theme);

		$values = array('composer_index' => self::COMPOSER_INDEX,
			'ximid' => $ximid,
			"versionname" => $versionname,
			"userID" => $userID,
			"debug" => XSession::checkUserID(),
			'theme' => $theme);

		$this->render($values, "index_widgets", "only_template.tpl");
	}

	public function changeTheme() {
		$theme = $this->request->getParam('theme');
		XSession::set('activeTheme', $theme);
	}




	public function readTreedata($idNode, $children=false, $desde=null, $hasta=null, $nelementos=null, $find=null) {
		XSession::check();
		$userID = XSession::get('userID');

		if (! isset($this->displayEncoding)) {
			$this->displayEncoding = Config::getValue('displayEncoding');
		}

		// The data to be returned
		$data = array(
			'node' => $this->_echoNodeTree($idNode, $this->displayEncoding),
			'children' => array()
		);

		if ($children !== true) {
			return $data;
		}

		$selectedNode = new Node($idNode);
		if (property_exists($selectedNode, 'nodeType') && is_object($selectedNode->nodeType)) {
			$isDir = $selectedNode->nodeType->isFolder() ? '1' : '0';
		} else {
			$isDir = '0';
			XMD_Log::warning(sprintf(_('A Node without NodeType was requested: idNode=%s, nodeType=%s'), $idNode, $selectedNode->nodeType));
		}

		//Filtering by debufilter
		if ($idNode == 1 && !empty($find) && XSession::checkUserID()) {
			$_nodes = $selectedNode->GetChildren();
			if (count($_nodes) > 0) {
				foreach ($_nodes as $idNode) {
					//Extracting number of each node to add it on xml
					$data['children'][] = $this->_echoNodeTree($idNode, $this->displayEncoding);
				}
			}
			return $data;
		}

		$user = new User($userID);
		$group = new Group();

		if (! XSession::get("nodelist")) {

			$groupList = $user->GetGroupList();
			// Removing general group
			if (is_array($groupList)) {
				$groupList = array_diff($groupList, array($group->GetGeneralGroup()));
			}

			$nodeList = array();
			// Putting on nodeList each performable node
			if ($groupList) {
				foreach ($groupList as $groupID) {
					$group = new Group($groupID);
					$nodeList = array_merge((array)$nodeList, (array)$group->GetNodeList());
				}
			}

			if (isset($nodeList) && is_array($nodeList)) {
				$nodeList = array_unique($nodeList);
			}

			// Adding node's fathers
			if (isset($nodeList)) {
				foreach ($nodeList as $idNode) {
					$node = new Node($idNode);
					$padre = $node->get('IdParent');
					while ($padre) {
						if (! in_array($padre, $nodeList)) {
							$nodeList = array_merge((array)$nodeList, (array)$padre);
						}
						$node = new Node($padre);
						$padre = $node->get('IdParent');
					}
				}
				XSession::set("nodelist", $nodeList);
			}

		} else {
			$nodeList = XSession::get("nodelist");
		}



		if (! $selectedNode->numErr) {

			//Getting childrens
			$children = $selectedNode->GetChildrenInfoForTree();

			if ($children) {
				$countChildrens = count($children);
				$ti = new Timer();
				$ti->start();
				for($i = 0; $i < $countChildrens; $i ++) {
					$nodeName[$i] = $children[$i]['name'];
					$systemType[$i] = 1000 - $children[$i]['system'];

				}
			}


			//Ordering the array and array slice
			$ti = new Timer();
			$ti->start();
			if (isset($nodeName) && is_array($nodeName)) {
				$nodeName_min = $nodeName;
				array_multisort($systemType, $nodeName_min, $children);
			}
			if (($desde !== null) && ($hasta !== null)) {
				$children = array_slice($children, $desde, $hasta - $desde + 1);
				$systemType = array_slice($systemType, $desde, $hasta - $desde + 1);
				$nodeName_min = array_slice($nodeName, $desde, $hasta - $desde + 1);
			}

			//**********************************************************************
			$l = count($children);
			$numArchivos = 0;
			if (($l > $nelementos) && ($nelementos != 0)) {
				//Paginated request
				$partes = floor($l / $nelementos);

				if ($l % $nelementos != 0) {
					$partes = $partes + 1;
				}

				for($k = 1; $k <= $partes; $k ++) {

					$nodoDesde = $children[$numArchivos]['id'];
					$textoDesde = $nodeName_min[$numArchivos];

					$expr = $numArchivos + $nelementos - 1;

					if ($l > $expr) {
						$nodoHasta = $children[$expr]['id'];
						$textoHasta = $nodeName_min[$expr];
						$hasta_aux = $expr;
					} else {
						$nodoHasta = $children[$l - 1]['id'];
						$textoHasta = $nodeName_min[$l - 1];
						$hasta_aux = $l - 1;
					}

					$data['children'][] = array(
						'name' => $textoDesde . ' -> ' . $textoHasta,
						'parentid' => $idNode,
						'nodeFrom' => $nodoDesde,
						'nodeTo' => $nodoHasta,
						'startIndex' => $numArchivos,
						'endIndex' => $hasta_aux,
						'src' => sprintf(
							'%s?method=treedata&amp;nodeid=%s&#38;from=%s&#38;to=%s',
							self::COMPOSER_INDEX, $selectedNode->GetParent(), $numArchivos, $hasta_aux
						),
						'nodeid' => '0',
						'icon' => 'folder_a-z.png',
						'openIcon' => 'folder_a-z.png',
						'state' => '',
						'children' => '5',
						'isdir' => $isDir
					);

					$numArchivos = $numArchivos + $nelementos;
				}

			} else {
				$user_perm_van = $user->HasPermission("view all nodes");
				
				if (($desde !== null) && ($hasta !== null)) {
					$nodeList = XSession::get("nodelist");
					$endFor = $hasta - $desde + 1;
					
					for($i = 0; $i < $endFor; $i ++) {

						$my_in = (is_array($nodeList) && in_array($children[$i], $nodeList));
						$user_ison_node = $user->IsOnNode($children[$i]['id'], true);

						if ($user_perm_van or $my_in or $user_ison_node) {

							$selectedNode = new Node($children[$i]['id']);
							$data['children'][] = $this->_echoNodeTree($selectedNode, $this->displayEncoding);
						}
					}
				} else {
					
					$countChildrens = sizeof($children);
					for($i = 0; $i < $countChildrens; $i ++) {
						if (isset($nodeList)) {
							$my_in = (is_array($nodeList) && in_array($children[$i], $nodeList));
						} else {
							$my_in = false;
						}
						$user_ison_node = $user->IsOnNode($children[$i], true);						
						if ($user_perm_van or $my_in or $user_ison_node) {
							$selectedNode = new Node($children[$i]['id']);
							$data['children'][] = $this->_echoNodeTree($selectedNode, $this->displayEncoding);
						}
					}
				}
			}
		}

		return $data;
	}

	function treedata() {

		//Getting the request
		$idNode = $this->request->getParam('nodeid');
		$desde = $this->request->getParam('from');
		$hasta = $this->request->getParam('to');
		$nelementos = $this->request->getParam('items');
		$find = $this->request->getParam('find');

		$data = $this->readTreedata($idNode, true, $desde, $hasta, $nelementos, $find);

		//Creating response
		$this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->response->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
		$this->response->set('Cache-Control', array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
		$this->response->set('Pragma', 'no-cache');
		$this->response->set('Content-type', 'text/xml');
		$this->response->sendHeaders();

		$xmlNodes = '';
		foreach ($data['children'] as $node) {
			$attributes = '';
			foreach ($node as $key=>$value) {
				$attributes .= sprintf('%s="%s" ', $key, $value);
			}
			$xmlNodes .= sprintf('<tree %s/>', $attributes);
		}

		$xml = sprintf('<?xml version="1.0" encoding="' . $this->displayEncoding . '"?><tree>%s</tree>', $xmlNodes);
		echo $xml;
	}

	public function includeDinamicJs() {

		//A bad way to solve the problem, warning, achtung
		$jsFile = $this->request->getParam('js_file') ? $this->request->getParam('js_file') : $this->request->getParam('amp;js_file');

		if(empty($jsFile) )
			$jsFile = "widgetsVars";		

		$jsFile = sprintf('/xmd/template/Smarty/helper/%s.tpl', $jsFile);

		// The class AssociativeArray does not return an array, then it obtains _GET value
		$params = isset($_GET['xparams']) ? $_GET['xparams'] : $_GET['amp;xparams'];

		$values = array();
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				if (!is_array($value)) $value = array($value);
				$aux = array();
				foreach ($value as $k=>$v) {
					$aux[$k] = Serializer::encode(SZR_JSON, $v);
				}
				$values[$key] = $aux;
			}
		}
		$values['js_file'] = $jsFile;

		// NOTE: it does not work!!!
//		$this->response->set('Content-type', 'application/javascript');

		$output = $this->render($values, 'include_dinamic_js', 'only_template.tpl', true);

		header('Content-type: application/javascript');
		echo $output;
		die();
	}

	/**
	 * Returning an array of widget dependencies
	 */
	public function wdeps() {
		$widget = $this->request->getParam('widget');
		$deps = Widget::getDependencies($widget);
		$deps = Serializer::encode(SZR_JSON, $deps);
		$this->response->set('Content-type', 'application/json');
		$this->response->sendHeaders();
		print($deps);
		exit;
	}

	/**
	 * Returning a widget config file
	 * @param string wn Widget name
	 * @param string wi Widget ID
	 * @param string a Action name
	 * @param string m Module name
	 */
	public function wconf() {

		$wn = $this->request->getParam('wn');
		$wi = $this->request->getParam('wi');
		$a = $this->request->getParam('a');
		$m = $this->request->getParam('m');

		$data = Widget::getWidgetconf($wn, $wi, $a, $m);

		$patron = '/_\(\s*([\'"])(.*)(?<!\\\\)\1\s*(\\/[*](.*)[*]\\/)?\s*\)/Usi';

		$data = 	preg_replace_callback( $patron,
				create_function( '$coincidencias', '$_out = null; eval(\'$_out = \'.$coincidencias[0].";"); return \'"\'.$_out.\'"\';'),
				$data );


//		header('Content-type: application/json');
		header('Content-type: text/javascript');
		print($data);
		exit;
	}

	/**
	 * Storing or retrieve session variables
	 * @param string wn Widget name
	 * @param string wi Widget ID
	 * @param string a Action name
	 * @param string m Module name
	 */
	public function sess() {

		$name = $this->request->getParam('name');
		$value = $this->request->getParam('value');

		if ($value !== null) {

			// setter
			$data = XSession::get('browser');
			if (!is_array($data)) $data = array();
			$data[$name] = $value;
			XSession::set('browser', $data);

		} else {

			// Getter
			$data = XSession::get('browser');
			if (!is_array($data)) $data = array();
			$value = isset($data[$name]) ? $data[$name] : null;
			$data = Serializer::encode(SZR_JSON, array($name => $value));
			$this->response->set('Content-type', 'application/json');
			$this->response->sendHeaders();
			print($data);
			exit;
		}
	}

	public function ximmenu() {

		XSession::check();

		$pxm = new ParsingXimMenu(XIMDEX_ROOT_PATH . '/conf/ximmenu.xml');
		$ximmenu = $pxm->processMenu(true);

		header('Content-type: text/xml');
		print $ximmenu;
	}



	function modules() {
		XSession::check();

		$data = ModulesManager::getModules();

		$this->sendJSON($data);
		die();
	}


	public function nodetypes() {
		XSession::check();

		$userID = XSession::get('userID');

		$user = new User();
		$user->SetID($userID);

		$dbObj = new DB();
		$sql = "select IdNodeType, Name, Icon
			from NodeTypes
			where IdNodeType in (select IdNodeType from Nodes where IdParent >= 10000)
			order by Name";
		$dbObj->Query($sql);
		$ret = array();
		while (!$dbObj->EOF) {
			$ret[] = array(
				'idnodetype' => $dbObj->getValue('IdNodeType'),
				'name' => $dbObj->getValue('Name'),
				'icon' => $dbObj->getValue('Icon')
			);
			$dbObj->next();
		}

		$ret = Serializer::encode(SZR_JSON, array('nodetypes' => $ret));
		$this->response->set('Content-type', 'application/json');
		$this->response->sendHeaders();
		print($ret);
		exit;
	}

	/**
	 * Returning a XMl document with all parents of a specific node id
	 */
	public function parents() {
/*
		$idNode = (int) $this->request->getParam('nodeid');
		$node = new Node($idNode);

		$dom = new DOMDocument('1.0', Config::getValue('workingEncoding'));
		$parents = $dom->createElement('node');
		$dom->appendChild($parents);

		if ($node->get('IdNode') > 0) {

			$attr = $dom->createAttribute('name');
			$attr->value = $node->getNodeName();
			$parents->appendChild($attr);
			$attr = $dom->createAttribute('nodeid');
			$attr->value = $idNode;
			$parents->appendChild($attr);
			$attr = $dom->createAttribute('path');
			$attr->value = $node->getPath();
			$parents->appendChild($attr);

			$parentId = $node->getParent();
			while ($parentId > 0) {

				$p = new Node($parentId);

				$parent = $dom->createElement('parent');
				$parents->appendChild($parent);
				$attr = $dom->createAttribute('name');
				$attr->value = $p->getNodeName();
				$parent->appendChild($attr);
				$attr = $dom->createAttribute('nodeid');
				$attr->value = $parentId;
				$parent->appendChild($attr);
				$attr = $dom->createAttribute('isdir');
				$attr->value = '1';
				$parent->appendChild($attr);

				$parentId = $p->getParent();
			}
		}
*/

		$idNode = (int) $this->request->getParam('nodeid');
		$node = new Node($idNode);

		$data = array('node' => array());

		if ($node->get('IdNode') > 0) {

			$data['node']['name'] = $node->getNodeName();
			$data['node']['nodeid'] = $idNode;
			$data['node']['path'] = $node->getPath();
			$data['node']['parents'] = array();

			$parentId = $node->getParent();
			while ($parentId > 0) {

				$p = new Node($parentId);

				$data['node']['parents'][] = array(
					'name' => $p->getNodeName(),
					'nodeid' => $parentId,
					'isdir' => '1'
				);

				$parentId = $p->getParent();
			}
		}

		$data = Serializer::encode(SZR_JSON, $data);
		$this->response->set('Content-type', 'application/json');
		$this->response->sendHeaders();
		echo $data;
	}

	function getPath() {
		$idNode = $this->request->getParam('id_node');
		$idNodeType = $this->request->getParam('nodetype');
		if (strstr($idNodeType, ',')) {
			$nodeTypes = explode(',', $idNodeType);
		} else {
			$nodeTypes = array($idNodeType);
		}

		$node = new Node($idNode);
		if (!in_array($node->get('IdNodeType'), $nodeTypes)) {
			$this->render(array('node' => ''));
			return;
		}
		$this->render(array('node' => $node->getPath()));
	}

	function getTraverseForNode() {
		$idNode = $this->request->getParam('id_node');
		$node = new Node($idNode);
		$this->render(array('nodes' => $node->TraverseToRoot()));
	}

	function getUserName(){
		$id=XSession::get('userID');
		$user=new User($id);
                if (ModulesManager::isEnabled('ximDEMOS')){
                    $email = $user->GetEmail();
                    $unverifiedUser = new UnverifiedUsers_ORM();
                    $result = $unverifiedUser->find("name","email=%s",array($email));
                    $this->render(array('username' => $result[0]["name"]));
                }else{
                    $this->render(array('username' => $user->GetLogin()));
                }
                
	}

	function getDefaultNode() {

		$defaultNodeName= Config::GetValue("DefaultInitNodeName");
		$defaultNodePath= Config::GetValue("DefaultInitNodePath");
		$userID = XSession::get('userID');
		$user = new User($userID);
		$groupList = $user->GetGroupList();
		$groupName=false;
		$nodes = array();
		
		
		$this->actionCommand = "xmleditor2";

		if ($this->tourEnabled($userID))
		{
                    if (ModulesManager::isEnabled('ximDEMOS')){
			foreach ($groupList as $idGroup) {
			    if ($idGroup != 101){
				$group = new Group($idGroup);
				$groupName = $group->GetGroupName();
			    }
			}

			if ($groupName){                            
                                $fullPath="/ximdex/projects/Picasso_{$groupName}".$defaultNodePath;                            
                                $node = new Node();
                                $nodes = $node->GetByNameAndPath($defaultNodeName, $fullPath);
			}
                    }else{
                        $fullPath="/ximdex/projects/Picasso".$defaultNodePath;
                        $node = new Node();
                        error_log($defaultNodeName." ".$defaultNodePath);
		    	$nodes = $node->GetByNameAndPath($defaultNodeName, $fullPath);
                        
                    }
		}

		$this->render(array('nodes' => $nodes));
		
	}

	function getTraverseForPath() {
		$path = $this->request->getParam('nodeid');
		$cachePath = XIMDEX_ROOT_PATH.ModulesManager::path('tolDOX').'/resources/cache/';
		$file = sprintf('%s%s_%s', $cachePath, str_replace('/', '_', $path), 'Traverse');
		$modeTags = false;
		if (preg_match('/\/Tags/', $path) > 0) {
			$modeTags = true;
			if (is_file($file)) {
				$data = FsUtils::file_get_contents($file);
				header('Content-type: application/json');
				echo $data;
				return;
			}
		}
		$entities[] = array();
		$this->request->setParam('nodeid', $path);
		while(($entity = GenericDatasource::read($this->request, false)) != NULL) {
			$entities[] = $entity;
			$path = $entity['parentid'];
			$this->request->setParam('nodeid', $path);
			if (isset($entity['bpath'])) {
				if ($entity['bpath'] == '/' || $entity['bpath'] == '/Tags') {
					break;
				}
			}
		}

		// Returning partial reversed entities array
		$nodeQuantity = count($entities) - 1;
		$reversedEntities = array();
		for ($i = $nodeQuantity; $i > 0; $i--) {
			if ($entities[$i]['nodeid'] == 1) {
				continue;
			}
			$reversedEntities[] = array(
				'backend' => isset($entities[$i]['backend']) ? $entities[$i]['backend'] : null,
				'bpath' => isset($entities[$i]['bpath']) ? $entities[$i]['bpath'] : null,
				'nodeid' => $entities[$i]['nodeid']
			);
		}

		$data = Serializer::encode(SZR_JSON, array('nodes' => $reversedEntities));
		if ($modeTags) {
			FsUtils::file_put_contents($file, $data);
		}
		$this->render(array('nodes' => $reversedEntities));
	}


	/** ******************************************* PRIVATE METHODS ******************************** */

	private function _printXmlToolbar($nodeID) {
		//global $userID;
		$userID = XSession::get("userID");
		/// echo $userID . "id";
		$node = new Node($nodeID);
		$user = new User($userID);
		$group = new Group();
		$role = new Role();
		$action = new Action();
		$contentToolBar = '';

//		XMD_Log::debug("XMD:toolbar: Data for node($nodeID). BEGIN");

		$contentToolBar .= '<node nodeid="' . $node->GetID() . '" name="' . $node->GetNodeName() . '" path="' . $node->GetPath() . '">';

		$groups = $user->GetGroupListOnNode($nodeID);
		// Debugging
		if (is_array($groups)) {
			foreach ($groups as $groupID) {
				$role->SetID($user->GetRoleOnGroup($groupID));
				$group->SetID($groupID);
				$contentToolBar .= '<role roleid="' . $role->GetID() . '" name="' . $role->GetName() . '" groupid="' . $group->GetID() . '" groupname="' . $group->GetGroupName() . '">';

				$actions = $role->GetActionsOnNode($nodeID);

				$actionIDS = array();
				$actionName = array();
				$actionIcon = array();
				$actionDesc = array();
				$actionOrder = array();

				if ($actions)
					foreach ($actions as $actionID) {
						$action = new Action($actionID);
						$actionIDS[] = $actionID;
						$actionName[] = $action->get('Name');
						$actionIcon[] = $action->GetIcon();
						$actionDesc[] = $action->get('Description');
						$actionOrder[] = $action->get('Sort');
					}

				if (is_array($actionIDS))
					array_multisort($actionOrder, $actionName, $actionIDS, $actionIcon, $actionDesc);

				// Modifying the loop limit, replacing $actionIDS with $actions, (because it had repeated actions, then it overwrite it) :Luis:
				for($i = 0; $i < sizeof($actionIDS); $i ++) {
					if ($actionOrder[$i] && $this->_notExcludedAction($actionIDS[$i], $nodeID))
						$contentToolBar .= '<action actionid="' . $actionIDS[$i] . '" name="' . $actionName[$i] . '" icon="' . $actionIcon[$i] . '" description="' . $actionDesc[$i] . '" />';
				}

				$contentToolBar .= '</role>';
			}
		}

		$contentToolBar .= '</node>';


		//Encoding the request to the diplayEncoding from Config, $this->displayEncoding is readed in the parent constructor
		$contentToolBar = XmlBase::recodeSrc($contentToolBar, $this->displayEncoding);

		echo $contentToolBar;
		XMD_Log::debug("XMD:toolbar: Data for node($nodeID). END");

	}

	private function _notExcludedAction($actionID, $nodeID) {
		$node = new Node($nodeID);
		$nodeTypeName = $node->nodeType->GetName();
		$devolver = 1;
		if ($nodeTypeName == "XimletContainer") {
			$parent = new Node($node->GetParent());
			$nodeTypeNameParent = $parent->nodeType->GetName();
			$action = new Action($actionID);
			$command = $action->GetCommand();

			if ($nodeTypeNameParent == "XimNewsColector" && $command == "deletenode") {
				$devolver = 0;
			}
		}

		return $devolver;
	}

	private function _echoNodeTree($node, $encoding) {
		if (is_numeric($node)) {
			$idNode = $node;
			$node = new Node($node);
			if (!($node->get('IdNode') > 0)) {
				return;
			}
		} else {
			if (strtolower(get_class($node)) != 'node')  {
				return;
			}
		}
		// We could do binding to load all this object
		//Encoding the node name with display Encoding about config table
		$node_id = $node->get('IdNode');
		$node_parent = $node->get('IdParent');
		$node_icon = $node->getIcon();
		$node_state = $node->get('IdState');
		$node_childs = count($node->GetChildren());

		if(( $node_childs > 0 && $node_id < 10000 ) || $node_id == 13) {
			$node_name = _($node->get('Name'));
		}else {
			$node_name = XmlBase::recodeSrc($node->get('Name'), $encoding);
		}
		$path = XmlBase::recodeSrc($node->getPath(), $encoding);
		$idNodeType = $node->get('IdNodeType');

		$isDir = $node->nodeType->isFolder() == 1 ? '1' : '0';
		$properties = $node->getAllProperties();
		$propertiesString = '';

		$processedProperties = array();
		if (is_array($properties)) {
			foreach($properties as $key => $values) {
				$processedProperties[$key] = is_array($values) ? implode(',', $values) : $values;
			}
		}

		$data = array(
			'name' => $node_name,
			'nodeid' => $node_id,
			'nodetypeid' => $idNodeType,
			'parentid' => $node_parent,
			'icon' => $node_icon,
			'state' => $node_state,
			'isdir' => $isDir,
			'children' => $node_childs,
			'path' => $path
		);

		$data = array_merge($data, $processedProperties);

		return $data;
	}

}
