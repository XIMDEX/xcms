<?php

/*********************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    			 *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	    	 *
 *                                                                            	 *
 *  This program is free software: you can redistribute it and/or modify         *
 *  it under the terms of the GNU Affero General Public License as published     *
 *  by the Free Software Foundation, either version 3 of the License, or      	 *
 *  (at your option) any later version.                                       	 *
 *                                                                            	 *
 *  This program is distributed in the hope that it will be useful,           	 *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         	 *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 		 *
 *  GNU Affero General Public License for more details.                       	 *
 *                                                                            	 *
 * See the Affero GNU General Public License for more details.                	 *
 * You should have received a copy of the Affero GNU General Public License      *
 * version 3 along with Ximdex (see LICENSE).                                 	 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       	 *
 *                                                                            	 *
 * @version $Revision: $                                                      	 *
 *                                                                            	 *
 *                                                                            	 *
 *********************************************************************************/











if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Nodes_ORM.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/model/nodetype.inc';
require_once XIMDEX_ROOT_PATH . '/inc/model/group.inc';
require_once XIMDEX_ROOT_PATH . '/inc/model/dependencies.inc';
include_once XIMDEX_ROOT_PATH . '/inc/nodetypes/sectionnode.inc';
require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/model/NodeDependencies.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/utils.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/sync/synchro.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/NodeProperty.class.php');
include_once (XIMDEX_ROOT_PATH . "/inc/fsutils/FsUtils.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/workflow/Workflow.class.php');

ModulesManager::file('/inc/model/Trash.class.php', 'ximTRASH');

define('DETAIL_LEVEL_LOW', 0);
define('DETAIL_LEVEL_MEDIUM', 1);
define('DETAIL_LEVEL_HIGH', 2);

if (!defined('COUNT')) {
	define('COUNT', 0);
	define('NO_COUNT', 1);
	define('NO_COUNT_NO_RETURN', 2);
}
class	Node extends Nodes_ORM{

	var $nodeID;			// current node ID.
	var $class;				// Class which implements the specific methos for this nodetype.
	/* @var $nodeType NodeType */
	var $nodeType;			// nodetype object.
	/* @var $dbObj DB */
	var $dbObj;				// DB object which will be used in the methods.
	var $numErr;			// Error code.
	var $msgErr;			// Error message.
	var $errorList= array(	// Class error list.
		1 => 'The node does not exist',
		2 => 'The nodetype does not exist',
		3 => 'Arguments missing or invalid',
		4 => 'Some of the children could not be deleted',
		5 => 'Database connection error',
		6 => 'No root in tree',
		7 => 'Error accessing to file system',
		8 => 'A node with the given name already exists',
		9 => 'Invalid name format',
		10 => 'Parent node does not exist',
		11 => 'The nodetype does not exist',
		12 => 'The node cannot be moved to an own internal node',
		13 => 'The node cannot be deleted',
		14 => 'It is not located under the given node',
		15 => 'A master node cannot link other',
		16 => 'A node cannot link itself',
		17 => 'This node is not allowed in this position'
		);


	/**
	 * Contruct
	 * @param $nodeID
	 * @param $fullLoad
	 * @return unknown_type
	 */
	function Node($nodeID = null, $fullLoad = true) {
		$this->errorList[1]=_('The node does not exist');
		$this->errorList[2]=_('The nodetype does not exist');
		$this->errorList[3]=_('Arguments missing or invalid');
		$this->errorList[4]=_('Some of the children could not be deleted');
		$this->errorList[5]=_('Database connection error');
		$this->errorList[6]=_('No root in tree');
		$this->errorList[7]=_('Error accessing to file system');
		$this->errorList[8]=_('A node with the given name already exists');
		$this->errorList[9]=_('Invalid name format');
		$this->errorList[10]= _('Parent node does not exist');
		$this->errorList[11]=_('The nodetype does not exist');
		$this->errorList[12]=_('The node cannot be moved to an own internal node');
		$this->errorList[13]=_('The node cannot be deleted');
		$this->errorList[14]=_('It is not located under the given node');
		$this->errorList[15]=_('A master node cannot link other');
		$this->errorList[16]=_('A node cannot link itself');
		$this->errorList[17]=_('This node is not allowed in this position');
   		$this->flagErr = FALSE;
		$this->autoCleanErr = TRUE;

		parent::GenericData($nodeID);
		//In order to do not breack compatibility with previous version
		if ($this->get('IdNode') > 0) {
			$this->nodeID = $this->get('IdNode');
		}
		if ($this->get('IdNodeType') > 0) {
			$this->nodeType = new NodeType($this->get('IdNodeType'));
			if ($this->nodeType->get('IdNodeType') > 0) {
				$nodeTypeClass = $this->nodeType->get('Class');
				$nodeTypeModule = $this->nodeType->get('Module');
				if (!empty($nodeTypeModule)) {
					$fileToInclude = sprintf('%s%s/inc/nodetypes/%s.inc', XIMDEX_ROOT_PATH,ModulesManager::path($nodeTypeModule) , strtolower($nodeTypeClass));
				} else {
					$fileToInclude = sprintf('%s/inc/nodetypes/%s.inc', XIMDEX_ROOT_PATH, strtolower($nodeTypeClass));
				}
				if (is_file($fileToInclude)) {
					include_once($fileToInclude);
				} else {
					XMD_Log::info(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
					die(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
				}
				if (!$fullLoad) return;
				$this->class = new $nodeTypeClass($this);
			}
		}
	}

	/**
	 * Returns the node name
	 * @return unknown_type
	 */
	function GetRoot(){
		$dbObj = new DB();
		$dbObj->Query("SELECT IdNode FROM Nodes WHERE IdParent IS null");
		if($dbObj->numRows) {
			return $dbObj->GetValue('IdNode');
		}

		$this->SetError(6);
		return NULL;
	}

	/**
	 * Returns the node ID we are working with
	 * @return int
	 */
	function GetID(){
		return $this->get('IdNode');
	}

	/**
	 * Changes the node we are working with
	 * @param $nodeID
	 */
	function SetID($nodeID = null){
		$this->ClearError();
		Node::Node($nodeID);
	}

	/**
	 * Returns the node name
	 * @return unknown_type
	 */
	function GetNodeName(){
		$this->ClearError();
		return $this->get('Name');
	}

	/**
	 * Returns the list of paths relative to project, of all the files and directories with belong to the node in the file system
	 * @param $channel
	 * @return unknown_type
	 */
	function GetPublishedNodeName($channel = null){
		$this->ClearError();
		if(!($this->get('IdNode') > 0)) {
			$this->SetError(1);
			return NULL;
		}
		return $this->class->GetPublishedNodeName($channel);
	}

	/**
	 * Changes node name
	 * @param $name
	 * @return unknown_type
	 */
	function SetNodeName($name){
		/// it is a renamenode alias
		return $this->RenameNode($name);
	}


	/**
	 * Returns the nodetype ID
	 * @return unknown_type
	 */
	function GetNodeType(){
		return $this->get('IdNodeType');
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetTypeName(){
		return $this->nodeType->get('Name');
	}

	/**
	 * Changes the nodetype
	 * @param $nodeTypeID
	 * @return unknown_type
	 */
	function SetNodeType($nodeTypeID){
		if (!($this->get('IdNode') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('IdNodeType', $nodeTypeID);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the node description
	 * @return unknown_type
	 */
	function GetDescription(){
		return $this->get('Description');
	}

	/**
	 * Changes the node description
	 * @param $description
	 * @return unknown_type
	 */
	function SetDescription($description){
		if (!($this->get('IdNode') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Description', $description);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the node state
	 * @return unknown_type
	 */
	function GetState(){
		$this->ClearError();
		return $this->get('IdState');
	}

	/**
	 * Changes the node workflow state
	 * @param $stateID
	 * @return unknown_type
	 */
	function SetState($stateID) {
		$dbObj = new DB();
		if(($this->get('IdNode') > 0)) {
			$sql = sprintf("UPDATE Nodes SET IdState= %d WHERE IdNode=%d OR SharedWorkflow = %d",
					$stateID, $this->get('IdNode'), $this->get('IdNode'));

 			$result = $dbObj->Execute($sql);
 			if ($result) {
 				return true;
 			}
		}

		$this->messages->add(sprintf(_('The node could not be moved to state %s'), $stateID), MSG_TYPE_ERROR);
		return false;
	}

	/**
	 * Returns the node icon
	 * @return unknown_type
	 */
	function GetIcon() {
		$this->ClearError();
		if(($this->get('IdNode') > 0)) {
			if(method_exists($this->class, 'GetIcon')) {
				return $this->class->GetIcon();
			}

			return $this->nodeType->GetIcon();
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Returns the list of channels for the node
	 * @return unknown_type
	 */
	function GetChannels(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			// NOTE: ServerNode->GetChannels($physicalId)
			return $this->class->GetChannels($this->GetID());
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Returns the node parent ID
	 * @return unknown_type
	 */
	function GetParent(){
		$this->ClearError();
		return $this->get('IdParent');
	}

	/**
	 * Changes the node parent
	 * @param $parentID
	 * @return unknown_type
	 */
	function SetParent($parentID){
		$this->ClearError();
		if ($this->get('IdNode') > 0) {
			$children = $this->GetChildByName($this->get('Name'));
			if (!empty($children)) {
				$this->SetError(8);
				return false;
			}
			$this->set('IdParent', $parentID);
			$result = $this->update();
			if (!$result) $this->messages->add(_('Node could not be moved'), MSG_TYPE_ERROR);
			$this->msgErr = _('Node could not be moved');
			$this->numErr = 1;
		}
		return true;
	}

  	/**
  	 * Returns the list of node children
  	 * @param $idtype
  	 * @param $order
  	 * @return unknown_type
  	 */
	function GetChildren($idtype = null, $order = null){
		if (!$this->get('IdNode')) {
			return array();
		}

		$where = 'IdParent = %s';
		$params = array($this->get('IdNode'));

		if (!empty($idtype)) {
			$where .= ' AND IdNodeType = %s';
			$params[] = $idtype;
		}

		$validDirs = array('ASC', 'DESC');
		if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
			$where .= sprintf(" ORDER BY %s %s", $order['FIELD'],
			isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
		}

		return $this->find('IdNode', $where, $params, MONO);
	}

	/**
	 * Returns a node list with the info for treedata
	 */
	function GetChildrenInfoForTree($idtype = null, $order = null)	{
		$validDirs = array('ASC', 'DESC');

		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$childrenList = array();
		    $sql = "select N.IdNode, N.Name as name,NT.System FROM Nodes as N inner join NodeTypes as NT on N.IdNodeType = NT.IdNodeType";
		    $sql .= " WHERE IdParent =". $this->get('IdNode');

		    if ($idtype) {
      			$sql .= " AND IdNodeType = $idtype";
      		}

			if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
				$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
				isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
			}

      		$dbObj = new DB();
 			$dbObj->Query($sql);
 			$i=0;
			while(!$dbObj->EOF) {
				$childrenList[$i]['id']=$dbObj->GetValue('IdNode');
				$childrenList[$i]['name']=$dbObj->GetValue('name');
				$childrenList[$i]['system']=$dbObj->GetValue('System');
				$i++;
				$dbObj->Next();
			}
			return $childrenList;
		} else {
			$this->SetError(1);
			return array();
		}
	}


	/**
	 * Looks for a child node with same name
	 *
	 * @param string $name name, optional. If none is passed, considered name will be current node name
	 * @return int/false
	 */
	function GetChildByName($name = NULL)
	{
		if (empty($name)) {
			$name = $this->get('Name');
		}
		$this->ClearError();
		if (($this->get('IdParent') > 0) && !empty($name)) {
			$dbObj = new DB();
			$sql = sprintf("SELECT IdNode FROM Nodes WHERE IdParent = %d AND Name = %s",
					$this->get('IdNode'), $dbObj->sqlEscapeString($name));

 			$dbObj->Query($sql);
 			if ($dbObj->numRows > 0) {
				return $dbObj->GetValue('IdNode');
 			} else {
 				return false;
 			}
		}
		/* It is a query, we do not have to consider a exception
		 else {
			$this->SetError(1);
		}*/
		return false;
	}


	/**
	 * Looks for nodes by name
	 *
	 * @param string $name name, optional. If none is passed, considered name will be current node name
	 * @return int/false
	 */
	function GetByName($name = NULL){
		if (empty($name)) {
			$name = $this->get('Name');
		}
		$this->ClearError();
		if (!empty($name)) {
			$dbObj = new DB();
			$sql = sprintf("SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes WHERE Nodes.IdNodeType = NodeTypes.IdNodeType AND  LOWER(Nodes.Name) like %s",
					$dbObj->sqlEscapeString("%".strtolower($name)."%"));

 			$dbObj->Query($sql);

 			if ($dbObj->numRows > 0) {
				$result = array();

				while (!$dbObj->EOF) {
					$node_t = new Node($dbObj->GetValue('IdNode') );
					if($node_t) $children = count($node_t->GetChildren()); else $children = 0;

					$result[] = array(	'IdNode' => $dbObj->GetValue('IdNode'),
										'Name' => $dbObj->GetValue('Name'),
										'Icon' => $dbObj->GetValue('Icon'),
										'Children' => $children,
									);
					$dbObj->Next();
				}

				return $result;

 			} else {
 				return false;
 			}
		}
		/* It is a query, no exception needed
		 else {
			$this->SetError(1);
		}*/
		return false;
	}

	function GetByNameAndPath($name=NULL,$path=NULL){
	    if(empty($name)){
		$name= $this->get("Name");
	    }
	    
	    if(empty($path)){
		$name= $this->get("Path");
	    }
	    
	    $this->ClearError();
	    if(!empty($name) && !empty($path)){
		$dbObj = new DB();
		$sql = sprintf("SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes
				WHERE Nodes.IdNodeType = NodeTypes.IdNodeType
				AND  LOWER(Nodes.Name) like %s
				AND LOWER(Nodes.Path) = %s",
				$dbObj->sqlEscapeString("%".strtolower($name)."%"),
				$dbObj->sqlEscapeString(strtolower($path)));
		$dbObj->Query($sql);

		while (!$dbObj->EOF) {
			$node_t = new Node($dbObj->GetValue('IdNode') );	
			$result[] = array(	'IdNode' => $dbObj->GetValue('IdNode')
					);
			$dbObj->Next();
		}

		return $result;
	    } else {
		return false;
	    }
	}


	/**
	 * Rteurns a list of paths relatives to the project of all the files and directories belonging to the node in filesystem
	 * @return unknown_type
	 */
	function GetPathList(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			return $this->class->GetPathList();
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Returns de node path (ximdex hierarchy!!! no file system one!!!)
	 * @return unknown_type
	 */
	function GetPath() {
		$path = $this->_GetPath();
		$idNode = $this->get('IdNode');
		if ($path) {
			//XMD_Log::debug("Model::Node::getPath(): Path for node($idNode) => $path. SUCCESS");
		} else {
			XMD_Log::debug("Model::Node::getPath(): Path cant be deduced from NULL idNode. ERROR");
		}
		return $path;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _GetPath() {

		$this->ClearError();
		$idNode = $this->get('IdNode');
		if($idNode > 0) {

			$sql = "select Name from FastTraverse ft inner join Nodes n on ft.idNode = n.idNode
					where ft.IdChild = $idNode
					order by depth desc";

			$db = new DB();
			$db->Query($sql);

			$path = '';

			while (!$db->EOF) {
				$path .= '/' . $db->getValue('Name');
				$db->Next();
			}

			return $path;
		}

		$this->SetError(1);
		return NULL;
	}
// In a process with 20 calls to each function, this consumes a 16% and getpublishedpath2 a 75% in an intermediate case

	/**
	 *
	 * @param $channelID
	 * @param $addNodeName
	 * @return unknown_type
	 */
	function GetPublishedPath($channelID = null, $addNodeName = null) {

		return $this->class->GetPublishedPath($channelID, $addNodeName);
	}

	/**
	 * If it is contained, returns the relative path from node $nodeID
	 * @param $nodeID
	 * @return unknown_type
	 */
	function GetRelativePath($nodeID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->IsOnNode($nodeID)) {
				if((!$this->GetParent()) || ($this->get('IdNode') == $nodeID)) {
					return '/'.$this->GetNodeName();
				} else {
					$parent = new Node($this->GetParent());
					return $parent->GetRelativePath($nodeID).'/'.$this->GetNodeName();
				}
			}
		else
			$this->SetError(1);
		}
		return NULL;
	}

	/**
	 * Returns if a node is contained in the node with id $nodeID
	 * @param $nodeID
	 * @return unknown_type
	 */
	function IsOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->get('IdNode') == $nodeID) {
				return true;
			} else {
				if(!$this->GetParent()) {
					return false;
				} else {
					$parent = new Node($this->GetParent());
					return $parent->IsOnNode($nodeID);
				}
			}
		}
		$this->SetError(1);
		return false;
	}

	/**
	 * Returns a path in the file system from where children are pending
	 * Function used for renderization
	 * @return unknown_type
	 */
	function GetChildrenPath(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			return $this->class->GetChildrenPath();
		}
		$this->SetError(1);
		return NULL;
	}

	 /**
	 * Returns a list of allowed nodetypes
	 *
	 * TODO -  Take into account amount to returns the ones really allowed
	 *
	 * @return array
	 */
	function GetCurrentAllowedChildren() {
		$query = sprintf("SELECT NodeType"
					. " FROM NodeAllowedContents"
					. " WHERE IdNodeType = %d",
					$this->nodeType->GetID());
		$allowedChildrens = array();
		$dbObj = new DB();

		$dbObj->Query($query);
		while(!$dbObj->EOF) {
			$allowedChildrens[] = $dbObj->GetValue('NodeType');
			$dbObj->Next();
		}
		return $allowedChildrens;
	}

	/**
	 * Renders a node in the file system
	 * @param $recursive
	 * @return unknown_type
	 */
	function RenderizeNode($recursive = null) {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->nodeType->get('IsRenderizable')) {
				if ($this->nodeType->get('HasFSEntity')) {
					$this->class->RenderizeNode();
				}
				if($recursive) {
					$children = $this->GetChildren();
					if(!empty($children) )  {
						foreach($children as $childID) {
							$child = new Node($childID);
							$child->RenderizeNode(true);
							//unset($child);
							xObject::destroy($child);
						}
					}
				}
			}
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Returns a node content
	 * @return unknown_type
	 */
	function GetContent(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			return $this->class->GetContent();
		}
		return NULL;
	}


	/**
	 *  Set a node content
	 * @param $content
	 * @param $commitNode
	 * @return unknown_type
	 */
	function SetContent($content, $commitNode = NULL){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$this->class->SetContent($content, $commitNode);
			$this->RenderizeNode();
		}
	}


	/**
	 * Checks if the node is blocked and returns the blocker user id
	 * @return unknown_type
	 */
	function IsBlocked(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$config = new Config();

			if( time() < ($config->GetValue('BlockExpireTime') + $this->get('BlockTime')) ) {
				return $this->get('BlockUser');
			} else {
				$this->unBlock();
				return NULL;
			}
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Checks if the node is blocked, and returns the blocking time
	 * @return unknown_type
	 */
	function GetBlockTime(){
		$this->ClearError();

		if($this->get('IdNode') > 0) {
			$config = new Config();

			if(time() < ($config->GetValue('BlockExpireTime') + $this->get('BlockTime')) ) {
				return $this->get('BlockTime');
			} else {
				$this->unBlock();
				return NULL;
			}
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Blocks a node and returns the blocking timestamp
	 * @param $userID
	 * @return unknown_type
	 */
	function Block($userID){
		$this->ClearError();

		if($this->get('IdNode') > 0) {

			$currentBlockUser = $this->IsBlocked();
			if(!$currentBlockUser || $currentBlockUser == $userID) {
				$this->set('BlockTime', time());
				$this->set('BlockUser', $userID);
				$this->update();
				return $this->get('BlockTime');
			} else {
				return NULL;
			}
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Delete a block.
	 * @return unknown_type
	 */
	function unBlock(){
		$this->ClearError();

		if ($this->get('IdNode') > 0) {
			$this->set('BlockTime', 0);
			$this->set('BlockUser', '');
			$this->update();
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Checks if node is renderized in the file system
	 * @return unknown_type
	 */
	function IsRenderized(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if (!$this->nodeType->get('IsRenderizable')) {
				return false;
			}
			$pathList = array();

			/// Consigue el path hasta el directorio de nodos
			$absPath = Config::GetValue("AppRoot") . Config::GetValue("NodeRoot");

			/// consigue la lista de paths del nodo
			$pathList = $this->class->GetPathList();
			if (empty($pathList)) {
				return false;
			}

			$path = $absPath . $pathList;
			if(!file_exists($path)) { /// si falta alguna devuelve false
				return false;
			}

			/// en otro caso devuelve true
			return true;
		}
		$this->SetError(1);
		return false;
	}

	function add() {
		die(_('This call should be done through CreateNode'));
		//		$this->CreateNode($this->get('Name'), $this->get('IdParent'), $this->get('IdNodeType'), $this->get('IdState'));
	}

	function update() {
		$this->set('ModificationDate', time());
		return parent::update();
	}

	/**
	 *
	 * @param $idParent
	 * @param $idNodeType
	 * @return unknown_type
	 */
	function getFirstStatus($idParent = NULL, $idNodeType = NULL) {
		if (empty($idParent)) {
			$idParent = $this->get('IdNode');
		}
		if (empty($idNodeType)) {
			$idNodeType = $this->get('IdNodeType');
		}

		$nodeType = new NodeType($idNodeType);
		if (!($nodeType->get('IsPublicable') > 0)) {
			return NULL;
		}

		$node = new Node($idParent);

		//first, I try to get it from the inherits properties
		$pipelines = $node->getProperty('Pipeline');
		if (count($pipelines) > 0) {
			$idPipeline = $pipelines[0];
			$workflow = new WorkFlow(NULL, NULL, $idPipeline);
			$idStatus = $workflow->GetInitialState();
			if ($idStatus > 0) {
				return $idStatus;
			}
		}

		//if i cant find it, i try to get it from the nodetypes
		$pipeNodeTypes = new PipeNodeTypes();
		$result = $pipeNodeTypes->find('IdPipeline', 'IdNodeType = %s', array($idNodeType), MONO);
		if (count($result) > 0) {
			$idPipeline = $result[0];
			$workflow = new WorkFlow(NULL, NULL, $idPipeline);
			$idStatus = $workflow->GetInitialState();
			if ($idStatus > 0) {
				return $idStatus;
			}
		}

		//finally, i get it from the default value
		$idPipeline = Config::getValue('IdDefaultWorkflow');
		$workflow = new WorkFlow(NULL, NULL, $idPipeline);
		return $workflow->GetInitialState();
	}

	/**
	 * Creates a new node and loads its ID in the class
	 * @param $name
	 * @param $parentID
	 * @param $nodeTypeID
	 * @param $stateID
	 * @return unknown_type
	 */
	function CreateNode($name, $parentID, $nodeTypeID, $stateID=null){
	 	$this->set('IdParent', (int)$parentID);
		$this->set('IdNodeType', (int)$nodeTypeID);
		$this->set('Name', $name);

		$nodeType = new NodeType($nodeTypeID);
		if ($nodeType->get('IsPublicable')) {
			$this->set('IdState', $this->getFirstStatus($parentID, $nodeTypeID));
		} else {
			$this->set('IdState', NULL);
		}

		if (!($name || $parentID || $nodeTypeID)) {
    		$this->SetError(3);
    		$this->messages->add(_('The name, parent or nodetype is missing'), MSG_TYPE_ERROR);
			return false;
		}

   		/// If nodetype is not existing, we are done
   		$nodeType = new NodeType($nodeTypeID);
   		if (!($nodeType->get('IdNodeType')) > 0) {
   			$this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
   			$this->SetError(11);
   			return false;
		}

		/// Checking for correct name format
		if(!$this->IsValidName($this->get('Name'), $this->get('IdNodeType')))	{
			$this->messages->add(_('Node name is not valid'), MSG_TYPE_ERROR);
			$this->SetError(9);
			return false;
		}

  		/// If parent does not exist, we are done
  		$parentNode = new Node($this->get('IdParent'));
  		if (!($parentNode->get('IdNode') > 0)) {
  			$this->messages->add(_('Parent node does not exist'), MSG_TYPE_ERROR);
  			$this->SetError(10);
  			return false;
		}

 		if(!($parentNode->GetChildByName($this->get('Name')) === false)) {
 			$this->messages->add(_('There is already a node with this name under this parent'), MSG_TYPE_ERROR);
			$this->SetError(8);
 			return false;
		}
		unset($parentNode);

		if (!$this->checkAllowedContent($nodeTypeID, $parentID)) {
			$this->messages->add(_('This node is not allowed under this parent'), MSG_TYPE_ERROR);
			$this->SetError(17);
			return false;
		}

		$this->set('CreationDate', time());
		$this->set('ModificationDate', time());
		/// Inserts the node in the Nodes table
		parent::add();
		if(!($this->get('IdNode') > 0)) {
			$this->messages->add(_('Error creating the node'), MSG_TYPE_ERROR);
			$this->SetError(5);
			return false;
		}

		$this->SetID($this->get('IdNode'));
		// Updating fastTraverse before the setcontent, because in the node cache this information is needed
	    $this->UpdateFastTraverse();

		/// All the args from this function call are passed to this nodetype create method.

		if (is_object($this->class)) {
			$argv = func_get_args();
			call_user_func_array (array (&$this->class, 'CreateNode'), $argv);
		}
		$this->messages->mergeMessages($this->class->messages);
		if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
			if ($this->get('IdNode') > 0) {
				$this->delete();
				return false;
			}
		}

	    $group = new Group();
	    $this->AddGroupWithRole($group->GetGeneralGroup());

	    // if the create node type is Section (section inside a server)
	    // it is checked if the user who created it belongs to some group
	    // to include the relation between nodes and groups
	    $nodeTypeName = $this->nodeType->get('Name');
	    if ($nodeTypeName == 'Section') {
	    	$id_usuario = XSession::get('userID');
	    	$user = new User($id_usuario);
	    	$grupos = $user->GetGroupList();
	    	// The first element of the list $grupos is always the general group
	    	// this insertion is not considered as it the relation by default
	    	if (is_array($grupos)) {
	    		reset($grupos);
	    		while(list(, $grupo) = each($grupos)) {
	    			$this->AddGroupWithRole($grupo, $user->GetRoleOnGroup($grupo));
	    		}
	    	}
	    }

	    /// Updating the hierarchy index for this node.
	    $this->RenderizeNode();

	    XMD_Log::debug("Model::Node::CreateNode: Creating node id(".$this->nodeID."), name(".$name."), parent(".$parentID.").");
	    /// Once created, its content by default is added...

	    $dbObj = new DB();
	    $query = sprintf("SELECT NodeType, Name, State, Params FROM NodeDefaultContents WHERE IdNodeType = %d",
	    			$this->get('IdNodeType'));
		$dbObj->Query($query);

	    while(!$dbObj->EOF) {
			$childNode = new Node();
			XMD_Log::debug("Model::Node::CreateNode: Creating child name(".$this->get('Name')."), type(".$this->get('IdNodeType').").");
			$childNode->CreateNode($dbObj->GetValue('Name'), $this->get('IdNode'), $dbObj->GetValue('NodeType'), $dbObj->GetVAlue('State'));
			$dbObj->Next();
		}
		$node = new Node($this->get('IdNode'));
		return $node->get('IdNode');
	}

	function delete() {
		return $this->DeleteNode(true);
	}

	/**
	 * Deletes a node and all its children
	 * @param $firstNode
	 * @return unknown_type
	 */
	function DeleteNode($firstNode = true) {
		if ($this->CanDenyDeletion() && $firstNode) {
			$this->messages->add(_('Node deletion was denied'), MSG_TYPE_WARNING);
			return false;
		}

		if (ModulesManager::isEnabled('ximTRASH')) {
			$this->messages->add(_('Moving file to trash'), MSG_TYPE_NOTICE);
			$trash = new Trash();
			// TODO Obtain the trash where sending it
			$idProject = $this->GetProject();
			if ($idProject > 0) {
				$node = new Node($idProject);
				$idTrash = $node->GetChildByName('Papelera');
			}

			$trash->set('id_trash', $idTrash);

			$trash->set('id_node', $this->get('IdNode'));
			$trash->set('id_old_parent', $this->get('IdParent'));
			$trash->set('id_node', $this->get('IdNode'));
			$trash->set('id_node_type', $this->get('IdNodeType'));
			$trash->set('name', $this->get('Name'));

			if (!$trash->add()) {
				$this->messages->add(sprintf(_('The node %s could not be moved to trash'), $this->get('IdNode')), MSG_TYPE_ERROR);
				reset($trash->messages->messages);
				while (list(, $message) = each($trash->messages->messages)) {
					$this->messages->add($message['message'], $message['type']);
				}
			}
			reset($this->messages->messages);
			while(list(, $message) = each($this->messages->messages)) {
				XMD_Log::warning($message['message']);
			}
		} else {
			XMD_Log::warning(_('Trash is deactivated'));
		}

		$this->ClearError();
		if(!($this->get('IdNode') > 0)) {
			$this->SetError(1);
			return false;
		}

		$IdChildrens = $this->GetChildren();

		if (!is_null($IdChildrens)) {

			reset($IdChildrens);
			while (list(, $IdChildren) = each($IdChildrens)) {
				$childrenNode = new Node($IdChildren);
				if ($childrenNode->get('IdNode') > 0) {
					$childrenNode->DeleteNode(false);
				} else {
					$this->SetError(4);
				}
			}
		}

		/// Deleting form frile system
		if($this->nodeType->get('HasFSEntity')) {
			$config = new Config();
			$absPath = $config->GetValue("AppRoot") . $config->GetValue("NodeRoot");
			$deletablePath = $this->class->GetPathList();

			$nodePath = $absPath . $deletablePath;

			if (is_dir($nodePath)) {
				FsUtils::deltree($nodePath);
			} else {
				FsUtils::delete($nodePath);
			}
		}

		// Deleting properties it may has

		$nodeProperty = new NodeProperty();
		$nodeProperty->deleteByNode($this->get('IdNode'));

		// frist invoking the particular Delete...
		$this->class->DeleteNode();

		// and the the general one
		$data = new DataFactory($this->nodeID);
		$data->DeleteAllVersions();
		unset($data);
		$dbObj = new DB();
		$dbObj->Execute(sprintf("DELETE FROM NodeNameTranslations WHERE IdNode = %d", $this->get('IdNode')));
		$dbObj->Execute(sprintf("DELETE FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));

		$nodeDependencies = new NodeDependencies();
		$nodeDependencies->deleteBySource($this->get('IdNode'));
		$nodeDependencies->deleteByTarget($this->get('IdNode'));

		$dbObj->Execute(sprintf("DELETE FROM FastTraverse WHERE IdNode = %d OR  IdChild = %d",
			$this->get('IdNode'), $this->get('IdNode')));

		$dependencies=New Dependencies();
		$dependencies->BorrarNodoDependiente($this->get('IdNode'));


		XMD_Log::info("Node ".$this->nodeID." deleted.");
		$this->nodeID	= null;
		$this->class	= null;
		return parent::delete();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function CanDenyDeletion(){
		if (is_object($this->class) && method_exists($this->class, 'CanDenyDeletion')) {
			return $this->class->CanDenyDeletion();
		}
		return true;
	}

	/**
	 * Returns the list of nodes which depend on the one in the object
	 * @return unknown_type
	 */
	function GetDependencies(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			return $this->class->GetDependencies();
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Returns the set of nodes which depend in a direct or indirect way on the node which are in the object
	 * (the set of verts of the dependency graph)
	 * @param $excludeNodes
	 * @return unknown_type
	 */
	function GetGlobalDependencies($excludeNodes=array()) {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$deps = array_unique($this->TraverseTree(4));
			$list = array_unique($this->TraverseTree());
			$brokenDeps = array_diff($deps,$list);
			$brokenDeps = array_diff($brokenDeps,$excludeNodes);

			if(sizeof($brokenDeps)) {
				foreach($brokenDeps as $depID) {
					if(is_array($excludeNodes) && in_array($depID,$excludeNodes)) {
						$exclude = array_merge($excludeNodes,$brokenDeps,$list);
						$dep = new Node($depID);
						$brokenDeps = array_merge($brokenDeps, $dep->GetGlobalDependencies($exclude));
						//unset($dep);
						xObject::destroy($dep);
					}
				}
			}

			return(array_unique($brokenDeps));
			}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Changes the node name
	 * @param $name
	 * @return unknown_type
	 */
	function RenameNode($name) {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if ($this->get('Name') == $name) {
				return true;
			}
			/// Checking if node name is in correct format
			if(!$this->IsValidName($name)) {
				$this->SetError(9);
				return false;
			}
			/// Checking if the parent has no other child with same name
			$parent = new Node($this->get("IdParent") );
			$idChildren = $parent->GetChildByName($name);
			if($idChildren && $idChildren != $this->get("IdNode")) {
				$this->SetError(5);
				return false;
			}

			$fsEntity = $this->nodeType->get('HasFSEntity');
			$isFile= ($fsEntity && ($this->nodeType->get('IsPlainFile') || $this->nodeType->get('IsStructuredDocument')));
			$isDir = ($fsEntity && ($this->nodeType->get('IsFolder') || $this->nodeType->get('IsVirtualFolder')));
			$isNone= (!$isFile && !$isDir);

			/// If it is a directory or file, we cannot not allow the process to stop before finishing and leave it inconsistent
			if($isDir || $isFile) {
				ignore_user_abort(true);
			}

			if($isDir) {
				/// Temporal backup of children nodes. In this case, it is passed the path and a flag to specify that it is a path
				$folderPath = Config::GetValue("AppRoot") . Config::GetValue("NodeRoot") . $this->class->GetChildrenPath();

			}

			if($isFile) {
				$absPath = Config::GetValue("AppRoot") . Config::GetValue("NodeRoot");
				$deletablePath = $this->class->GetPathList();
				FsUtils::delete($absPath . $deletablePath);
			}

			/// Changing the name in the Nodes table
			$this->set('Name', $name);
			$result = $this->update();
			/// If this node type has nothing else to change, the method rename node of its specific class is called
			$this->class->RenameNode($name);

			if($isFile) {
				/// The node is renderized, its children are lost in the filesystem
				$node = new Node($this->get('IdNode'));
 				$node->RenderizeNode();
			}

			if($isDir) {
				/// Retrieving all children from the backup we kept, identified by $backupID
				$parentNode = new Node($this->get('IdParent'));
				$newPath = Config::GetValue("AppRoot") . Config::GetValue("NodeRoot") . $parentNode->GetChildrenPath() . '/' . $name;
				rename($folderPath, $newPath);
			}

			return true;
		}
		$this->SetError(1);
		return false;
	}

	/**
	 * Moves the node
	 * @param $targetNode
	 * @return unknown_type
	 */
	function MoveNode($targetNode){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($targetNode > 0) {
				$target = new Node($targetNode);
				if(!$target->IsOnNode($this->get('IdNode'))) {
					$fsEntity = $this->nodeType->get('HasFSEntity');
					$absPath = Config::GetValue("AppRoot") . Config::GetValue("NodeRoot");
					ignore_user_abort(true);

					// Temporal children backup. In this case it is passed the path and a flag to indicate that it is a path
					$folderPath = $absPath . $this->class->GetChildrenPath();

					// FastTraverse is updated for current node
					$this->SetParent($targetNode);
					$this->UpdateFastTraverse();

					// Retrieving all children from stored backup, identified by $backupID
					$parentNode = new Node($this->get('IdParent'));
					$newPath = $absPath . $parentNode->GetChildrenPath() . '/' . $this->GetNodeName();
					@rename($folderPath, $newPath);
//					$this->TraverseTree(2);
// A language document cannot be moved

					// Node is renderized, so we lost its children in filesystem
					$this->RenderizeNode(1);

					// Updating paths and FastTraverse of children (if existing)
					$this->UpdateChildren();


					//If there is a new name, we change the node name.
					$name = FsUtils::get_name($this->GetNodeName());
					$ext = FsUtils::get_extension($this->GetNodeName());
					if($ext != null && $ext != "") $ext = ".".$ext;
					$newName = $name.$ext;
					$index = 1;
					while(($child = $target->GetChildByName($newName)) > 0 && $child != $this->get("IdNode")) {
							$newName = sprintf("%s_copia_%d%s",$name, $index, $ext);
							$index++;
					}
					//If there is no name change, we leave all as is.
					if($this->GetNodeName() != $newName) {
						$this->SetNodeName($newName);
					}

				} else {
					$this->SetError(12);
				}
			} else {
				$this->SetError(3);
			}
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Returns a list of groups associated to this node
	 * @return unknown_type
	 */
	function GetGroupList() {
		$this->ClearError();
		if ($this->get('IdNode') > 0) {
			if (!$this->nodeType->get('CanAttachGroups')) {
				$parent = $this->get('IdParent');
				if ($parent) {
					$parent = new Node($parent);
					if($parent->get('IdNode') > 0) {
						$groupList = $parent->GetGroupList();
					} else {
						$groupList = array();
					}
				} else {
					$groupList = array();
				}
			} else {
				$dbObj = new DB();
				$dbObj->Query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
				$groupList = array();
				while(!$dbObj->EOF) {
					$groupList[] = $dbObj->GetValue("IdGroup");
					$dbObj->Next();
				}
			}
			return $groupList;
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Returns the list of groups associated to this node
	 * @param $groupID
	 * @return unknown_type
	 */
	function GetRoleOfGroup($groupID){
		$this->ClearError();
		if($this->get('IdNode')) {
			if(!$this->nodeType->get('CanAttachGroups')) {
				$parent = $this->GetParent();
				if($parent) {
					if(!$this->numErr) {
						$node = new Node($parent);
						$role = $node->GetRoleOfGroup($groupID);
						if(!$node->numErr)
							return $role;
						}
					}
				}
			else
				{
				$sql = sprintf("SELECT IdRole FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d",
						$this->get('IdNode'), $groupID);
				$dbObj = new DB();
				$dbObj->Query($sql);
				if($dbObj->numRows > 0) {
					return $dbObj->GetValue("IdRole");
				} else {
					$this->SetError(5);
				}
			}
		}
		$this->SetError(1);
		return NULL;
	}

	///
	/**
	 * Returns the list of users associated to this node
	 * @param $ignoreGeneralGroup
	 * @return unknown_type
	 */
	function GetUserList($ignoreGeneralGroup = null){
		$this->ClearError();
		$group = new Group();
		if($this->get('IdNode') > 0) {
			$groupList = array();
			$groupList = $this->GetGroupList();

			/// Taking off the General Group if needed
			if($ignoreGeneralGroup) {
				$groupList = array_diff($groupList, array($group->GetGeneralGroup()));
			}

			$userList = array();
			if(!$this->numErr) {
				foreach($groupList as $groupID) {
					$group = new Group($groupID);
					$tempUserList=array();
					$tempUserList=$group->GetUserList();
					$userList = array_merge($userList,$tempUserList);
					unset($group);
				}
				return array_unique($userList);
			}
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * This function does not delete an user from Users table, this disassociated from the group
	 * @param $groupID
	 * @return unknown_type
	 */
	function DeleteGroup($groupID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->nodeType->get('CanAttachGroups')) {
				$dbObj = new DB();
				$query = sprintf("DELETE FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d",
						$this->get('IdNode'), $groupID);
				$dbObj->Execute($query);
				if($dbObj->numErr) {
					$this->SetError(5);
				}
			}

		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Associated an user to a group with a concrete role
	 * @param $groupID
	 * @param $roleID
	 * @return unknown_type
	 */
	function AddGroupWithRole($groupID, $roleID = null){
		$this->ClearError();
		if(!is_null($groupID)) {
			if($this->nodeType->get('CanAttachGroups')) {
				$dbObj = new DB();
				$query = sprintf("INSERT INTO RelGroupsNodes (IdGroup, IdNode, IdRole) VALUES (%d, %d, %d)",
					$groupID, $this->get('IdNode'), $roleID);
				$dbObj->Execute($query);
				if($dbObj->numErr) {
					$this->SetError(5);
					return false;
				}
				return true;
			}
		} else  {
			$this->SetError(1);
		}
		return false;
	}

	/**
	 * It allows to change the role a user participates in a group with
	 * @param $groupID
	 * @param $roleID
	 * @return unknown_type
	 */
	function ChangeGroupRole($groupID, $roleID)	{
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->nodeType->get('CanAttachGroups')) {
				$sql = sprintf("UPDATE RelGroupsNodes SET IdRole = %d WHERE IdNode = %d AND IdGroup = %d",
					$roleID, $this->get('IdNode'), $groupID);
				$dbObj = new DB();
				$dbObj->Execute($sql);
				if($dbObj->numErr) {
					$this->SetError(5);
				}
			}
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Returns true if an user belongs to a group
	 * @param $groupID
	 * @return unknown_type
	 */
	function HasGroup($groupID) {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$dbObj = new DB();
			$dbObj->query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdGroup = %d AND IdNode = %d",
				$groupID, $this->get('IdNode')));
			if($dbObj->numErr) {
				$this->SetError(5);
			}
			return $dbObj->numRows;
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetAllGroups() {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$dbObj = new DB();
			$dbObj->Query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
			if(!$dbObj->numErr) {
				while(!$dbObj->EOF)
				{
					$salida[] = $dbObj->GetValue("IdGroup");
					$dbObj->Next();
				}
    			return $salida;
				}
			else
				$this->SetError(5);
		}
		return NULL;
	}


 	/**
 	 * Function which makes a node to have a workflow as other node and depends on it
 	 * @param $nodeID
 	 * @return unknown_type
 	 */
	function SetWorkFlowMaster($nodeID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($nodeID != $this->get('IdNode'))	{
				$this->set('SharedWorkflow', $nodeID);
				$this->update();

				$synchro = new Synchronizer($this->get('IdNode'));
				$synchro->CopyTimeLineFromNode($nodeID);
			}
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Function which makes the node to have a new independent workflow
	 * @return unknown_type
	 */
	function ClearWorkFlowMaster(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$this->set('SharedWorkflow', '');
			$this->update();
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Function which makes the node to have a new independent workflow
	 * @param $id
	 * @return unknown_type
	 */
	function GetWorkFlowSlaves($id = null){
		return $this->find('IdNode', 'SharedWorkflow = %s', array($this->get('IdNode')), MONO);
 	}


 	/**
 	 *
 	 * @return unknown_type
 	 */
	function IsWorkflowSlave(){
		return $this->get('SharedWorkflow');
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetAllAlias(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdLanguage, Name FROM NodeNameTranslations WHERE"
					. " IdNode= %d");
			$dbObj->Query($query);
			if($dbObj->numRows) {
				$result = array();
				while(!$dbObj->EOF) {
					$result[$dbObj->GetValue('IdLanguage')] = $dbObj->GetValue('Name');
					$dbObj->Next();
				}
				return $result;
			}
		}
		return false;
	}

	/**
	 * Obtains the current node alias
	 * @param $langID
	 * @return unknown_type
	 */
	function GetAliasForLang($langID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE"
					. " IdNode= %d"
					. " AND IdLanguage = %d", $this->get('IdNode'), $langID);
			$dbObj = new DB();
			$dbObj->Query($sql);
			if ($dbObj->numErr) {
				$this->SetError(5);
			} else {
				if($dbObj->numRows) {
					return $dbObj->GetValue("Name");
				}
			}
		} else {
			$this->SetError(1);
		}
		return NULL;
	}

	/**
	 * Controls if the current node has alias
	 * @param $langID
	 * @return unknown_type
	 */
	function HasAliasForLang($langID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$sql = sprintf("SELECT IdNode FROM NodeNameTranslations WHERE"
					. " IdNode =  %d"
					. " AND IdLanguage = %d", $this->get('IdNode'), $langID);
			$dbObj = new DB();
		  	$dbObj->Query($sql);
			if ($dbObj->numErr)
				$this->SetError(1);

			return $dbObj->GetValue("IdNode");
		}
		$this->SetError(1);
		return NULL;
	}


	/**
	 *
	 * @param $langID
	 * @return unknown_type
	 */
	function GetAliasForLangWithDefault($langID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$this->ClearError();
			$sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE"
					. " IdNode = %d"
					. " AND IdLanguage = %d", $this->get('IdNode'), $langID);
			$dbObj = new DB();
		  	$dbObj->Query($sql);
			if ($dbObj->numRows > 0) {
				// si encuentra el traducido lo devuelve
				return $dbObj->GetValue("Name");
			}

			// si no, busca el lenguaje default del proyecto
			$conf = new Config();
			$langDefault = $conf->GetValue("DefaultLanguage");
			if (strlen($langDefault) != 0) {
				$lang = new Language();
				$lang->SetByIsoName($langDefault);
				$sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE"
	  					. " IdNode = %d"
				  		. " AND IdLanguage = %d", $this->get('IdNode'), $lang->get('IdLanguage'));
				$dbObj = new DB();
				$dbObj->Query($sql);
				if ($dbObj->numRows > 0)
				{
					// si encuentra el idioma x defecto lo devuelve
			 		return $dbObj->GetValue("Name");
				}
			}

			// y si no encuentra nada devuelve el nombre del nodo.
			return $this->GetNodeName();
		}
		$this->SetError(1);
		return NULL;
	}

	/**
	 * Setting a alias to current node.
	 * @param $langID
	 * @param $name
	 * @return unknown_type
	 */
	function SetAliasForLang($langID, $name) {
		if($this->get('IdNode') > 0) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdNode FROM NodeNameTranslations"
					. " WHERE IdNode = %d AND IdLanguage = %d", $this->get('IdNode'), $langID);
			$dbObj->Query($query);
			if ($dbObj->numRows > 0) {
				$sql = sprintf("UPDATE NodeNameTranslations "
						. " SET Name = %s"
						. " WHERE IdNode = %d"
						. " AND IdLanguage = %d",
						$dbObj->sqlEscapeString($name), $this->get('IdNode'), $langID);
			} else {
				$sql = sprintf("INSERT INTO NodeNameTranslations "
						. "(IdNode, IdLanguage, Name) "
						. "VALUES (%d, %d, %s)",
						$this->get('IdNode'), $langID, $dbObj->sqlEscapeString($name));
			}

			$dbObj = new Db();
		  	$dbObj->Execute($sql);

			if ($dbObj->numErr) {
				$this->messages->add(_('Alias could not be updated, incorrect operation'), MSG_TYPE_ERROR);
				XMD_Log::error(sprintf(_("Error in query %s or %s"), $query, $sql));
				return false;
			}
			return true;
		}

		$this->messages->add(_('The node you want to operate with does not exist'), MSG_TYPE_WARNING);
		XMD_Log::warning(_("Error: unexisting node")."{$this->IdNode}");
		return false;
	}

	/**
	 * Deletes a current node alias.
	 * @param $langID
	 * @return unknown_type
	 */
	function DeleteAliasForLang($langID) {
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$sql = sprintf("DELETE FROM NodeNameTranslations "
					. " WHERE IdNode = %d"
					. " AND IdLanguage = %d", $this->get('IdNode'), $langID);
			$dbObj = new DB();
		  	$dbObj->Execute($sql);
			if ($dbObj->numErr) {
				$this->messages->add(_('Alias could not be deleted, incorrect operation'), MSG_TYPE_ERROR);
				XMD_Log::error(sprintf(_("Error in query %s"),$sql));
				return false;
			}
			return true;
		}
		$this->messages->add(_('The node you want to operate with does not exist'), MSG_TYPE_WARNING);
		XMD_Log::warning(_("Error: unexisting node")."{$this->IdNode}");
		return false;
	}

	/**
	 * Deletes all current node aliases.
	 * @return unknown_type
	 */
	function DeleteAlias(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$sql = sprintf("DELETE FROM NodeNameTranslations "
					. " WHERE IdNode = %d", $this->get('IdNode'));
			$dbObj = new DB();
	  		$dbObj->Execute($sql);
			if ($dbObj->numErr)
				$this->SetError(5);
			}
		else
			$this->SetError(1);
	}


	/**
	 * If it is contained, it give translated names from node $nodeID in a list form
	 * @param $nodeID
	 * @param $langID
	 * @return unknown_type
	 */
	function GetAliasForLangPath($nodeID, $langID){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			if($this->IsOnNode($nodeID)) {
				if((!$this->get('IdParent')) || ($this->get('IdNode') == $nodeID)) {
					return array($this->GetAliasForLangWithDefault($langID));
				} else {
					$parent = new Node($this->get('IdParent'));
					return array_merge(
							$parent->GetAliasForLangPath($nodeID, $langID),
							array($this->GetAliasForLangWithDefault($langID)));
				}
			} else {
				$this->SetError(14);
				return array();
			}
		} else {
			$this->SetError(1);
			return array();
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetSection() {
		if (!($this->get('IdNode') > 0)) {
			return NULL;
		}

		if ($this->nodeType->get('IsSection')) {
			return $this->get('IdNode');
		}

		$idParent= $this->get('IdParent');
		if (!$idParent) {
			return NULL;
		}

		$parent = new Node($idParent);
		return $parent->GetSection();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function getServer() {

		$result = $this->_getParentByType(5014);

		if (!($result > 0)) {

			$result = $this->_getParentByType(5083);
		}

		return $result;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function getProject() {

		$result = $this->_getParentByType(5013);

		if (!($result > 0)) {

			$result = $this->_getParentByType(5083);
		}

		return $result;
	}

	/**
	 *
	 * @param $type
	 * @return unknown_type
	 */
	function _getParentByType($type = NULL) {
		if (is_null($type)) {
			XMD_Log::fatal(_('It is being tried to call a function without param'));
			return false;
		}

		if ($this->get('IdNodeType') == $type) {
			return $this->get('IdNode');
		}

		$query = sprintf("SELECT ft.IdNode FROM `FastTraverse` ft"
				. " INNER JOIN Nodes n ON ft.IdNode = n.IdNode AND n.IdNodeType = %d"
				. " WHERE ft.IdChild = %d and ft.IdNode <> %d",
				$type, $this->get('IdNode'), $this->get('IdNode'));
		$db = new DB();
		$db->query($query);
		if ($db->numRows > 0) {
			return $db->getValue('IdNode');
		}

		XMD_Log::error(sprintf(_("The nodetype %s could not be obtained for node "),$type) . $this->get('IdNode'));
		return NULL;

	}

	/**
	 * If it is depending on a project, its depth is returned
	 * @return unknown_type
	 */
	function GetDepth() {
		if (!($this->get('IdNode') > 0)) {
			return NULL;
		}

		if ($this->nodeType->get('Name') == "Server") {
			return 1;
		}

		$idParent = $this->get('IdParent');

		if (!$idParent) {
			return null;
		}

		$parent = new Node($idParent);
		$depth = $parent->GetDepth();
		if ($depth) {
			return NULL;
		}

		return ($depth+1);
	}

	/**
	 * If its pending on some project, its depth is returned
	 * @return unknown_type
	 */
	function GetPublishedDepth() {
		if (!($this->get('IdNode') > 0)) {
			return NULL;
		}

		if ($this->nodeType->get('Name') == "Server") {
			return 1;
		}

		$idParent = $this->get('IdParent');
		if(!$idParent) {
			return NULL;
		}

		$parent = new Node($idParent);
		$depth = $parent->GetPublishedDepth();
		if (!$depth) {
			return NULL;
		}
		if($this->nodeType->get('IsVirtualFolder')) {
			return $depth;
		}
		return ($depth+1);
	}

	/**
	 * Returns the list of nodes which depend on given one.
	 * If flag=3, returns just the ones associated with groups 'CanAttachGroups'
	 * If flag=4, returns all the nodes which depend on the one in the object and its lists of dependencies.
	 * If flag=5, returns all the nodes which depend on the one in the object which cannot be deleted.
	 * If flag=6, returns all the nodes which depend on the one in the object which are publishable.
	 * If flag=null, returns all the nodes which depend on the one in the object
	 * @param $flag
	 * @param $firstNode
	 * @param $filters
	 * @return unknown_type
	 */
	function TraverseTree($flag = null, $firstNode = true, $filters = ''){
		/// Making an object with current node and its ID is added
		$nodeList = array();

		if(($flag == 3) && $this->nodeType->get('CanAttachGroups')) {
			$nodeList[0] = $this->get('IdNode');
		}else if($flag == 4) {
			$nodeList[0] = $this->get('IdNode');
			$nodeList = array_merge($nodeList, $this->GetDependencies());
		}else if($flag == 5) {
			if($this->CanDenyDeletion() && $firstNode) {
				if ($this->get('IdNode') > 0) {
					$nodeList[0] = $this->get('IdNode');
				}
			}
		}else if($flag == 6) {
			if($this->nodeType->get('IsPublicable') == 1) {
				$nodeList[0] = $this->get('IdNode');
			}
		}else {
			$nodeList[0] = $this->get('IdNode');
		}

		$nodeChildren = $this->GetChildren();

		/// Doing the same for each child
		if(is_array($nodeChildren)){
			foreach ($nodeChildren as $child) {
				$childNode = new Node($child);
				$nodeList = array_merge($nodeList, $childNode->traverseTree($flag, false));
				//unset($childNode);
				xObject::destroy($childNode);
			}
		}
		return $nodeList;
	}

	/*
	 *	Gets all ancestors of the node
	 *  @param int fromNode
	 *  @return array
	 */
	function getAncestors($fromNode = null) {
		$dbObj = new DB();
		$sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild= %d ORDER BY Depth DESC",
				 $this->get('IdNode'), $this->get('IdNode'));
  		$dbObj->Query($sql);

		$list = array();

		while(!$dbObj->EOF)	{
			$list[] = $dbObj->GetValue('IdNode');
			$dbObj->Next();
		}

		return $list;
	}


	/**
	 * Returns a list with the path from root (or the one in the param fromID if it is) until the nod ein the object
	 * Keeps the list of node ids ordered by depth, including the object.
	 * @param $fromNode
	 * @return unknown_type
	 */
	function TraverseToRoot($fromNode = NULL){
		$dbObj = new DB();
		$sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild= %d AND IdNode >=10000 ORDER BY Depth DESC",
				 $this->get('IdNode'));
  		$dbObj->Query($sql);

		$list = array();
		while(!$dbObj->EOF) {
			$list[] = $dbObj->GetValue('IdNode');
			$dbObj->Next();
		}

		if($fromNode) {
			if(is_array($list) && !in_array($fromNode, $list)) {
				$this->SetError(14);
				return null;
			} else {
				$newList = array();
				foreach($list as $id) {
					if($id == $fromNode)
						$found = 1;

					if(isset($found))
						$newList[] = $id;
				}
				$list = $newList;
			}
		}

		return $list;
	}

	/**
	 *
	 * @param $depth
	 * @param $node_id
	 * @return unknown_type
	 */
	function TraverseByDepth($depth, $node_id = 1){
		$dbObj = new DB();
		$sql = sprintf("SELECT IdChild FROM FastTraverse WHERE IdNode = %d AND Depth = %d ORDER BY IdNode",
				$node_id, $depth);
		$dbObj->Query($sql);

		$list = array();
		while (!$dbObj->EOF) {
			$list[] = $dbObj->getValue('IdChild');
			$dbObj->Next();
		}

		return $list;
	}

	/**
	 * JAP 20040617, GetSections_ximTREE
	 * @param $langID
	 * @param $top
	 * @param $bottom
	 * @return unknown_type
	 */
	function GetSections_ximTREE($langID, $top, $bottom){
	    // getting the nodetypes to select
		$auxType = new NodeType();
		$auxType->SetByName("Section");
		$sectionTypeId = $auxType->get('IdNodeType');

	    $auxType = new NodeType();
		$auxType->SetByName("Server");
		$serverTypeId = $auxType->get('IdNodeType');

	    // list of nodes of type 'section' until the server (inclusive)
	    $sectionList = array();

	    // surfing the node tree, looking for the section which contain the current node
	    $parentid = $this->GetSection();

	    $profundidad = 0;

	    while($parentid) {
			$node = new Node($parentid);
			$nodetype = $node->get('IdNodeType');

			array_push($sectionList, $node->get('IdNode'));

			// seguimos subiendo en el �rbol
			if ($nodetype == $serverTypeId) {
				$parentid = null; // we are in the server, exiting
			} else {
				$parentid = $node->GetParent(); //take the parent, it will include the server
			}
			$profundidad++;
	    }

    // re-ordering the list to start from the tree top
    $sectionList = array_reverse($sectionList);

    /* DEBUG  ...
    foreach ($sectionList as $mysection)
      echo "SECCION: $mysection ";
    ... */

    // surfing through sections building the exchange XML
    $cad = "<ximTREE ximver='2.0' top='$top' bottom='$bottom'>";

    $startlevel = $profundidad-$top-1; //start section
    if ($startlevel<0) $startlevel = 0;

    $endlevel = $profundidad+$bottom; //end section

    if ($startlevel <= sizeof($sectionList))
      $section = $sectionList[$startlevel];
    else
      $section = null;

    $level = $startlevel+1;

    $branch = null;
    if ($level == sizeof($sectionList)) $branch = 1;

    //DEBUG
    // echo "SELECCIONADA SECCION $section PARA PROCESADO con TOP:$top y BOTTOM:$bottom START:$level END:$endlevel ...";

    if ($section && $level <= $endlevel)
      $cad .= $this->expandChildren_ximTREE($section, $sectionTypeId, $level, $langID, $sectionList, $endlevel, $branch);

    $cad = "$cad</ximTREE>";
    return $cad;
	}

	/**
	 *
	 * @param $nodeID
	 * @param $sectionTypeId
	 * @param $level
	 * @param $langID
	 * @param $sectionList
	 * @param $endlevel
	 * @param $branch
	 * @return unknown_type
	 */
	function expandChildren_ximTREE($nodeID, $sectionTypeId, $level, $langID, $sectionList, $endlevel, $branch=null) {
	    $node = new Node($nodeID);
	    $nodoseleccionado = $sectionList[$level];
	    $children = $node->GetChildren($sectionTypeId);

	    $cad2 = ""; // opening tag for children family "<ximCHILDREN>"

	    foreach ($children as $child) {
	      if ($child and $level < $endlevel) {
	           $childnodeid = new Node($child);
	           $childname = $childnodeid->get('Name');
	           $childnamelang = $childnodeid->GetAliasForLangWithDefault($langID);

	           if ($child == $nodoseleccionado)
	             $childseleccionado = 1;
	           else
	             $childseleccionado = 0;

	           $original_level = sizeof($sectionList)-1;
	           $distance = $level - $original_level;

	           $relationship = "relative";
	           if ($childseleccionado and $distance < 0) $relationship = "ascendant";
	           if ($childseleccionado and $distance == 0) {
	             $relationship = "me";
	             $branch = 1;
	           }
	           if ($branch and $distance > 0) $relationship = "descendant";

	           $cad2 .= "<ximNODE sectionid='$child' level='$level' distance='$distance' relationship='$relationship' onpath='$childseleccionado' type='section' name='$childname' langname='$childnamelang' langid='$langID'>";
	           $cad2 .= $node->expandChildren_ximTREE($child, $sectionTypeId, $level+1, $langID, $sectionList, $endlevel, $branch);
	           $cad2 .= "</ximNODE>";
	      }
	    }
	    //$cad2 .= "</ximCHILDREN>"; // if we want to close tag for children family
	    return $cad2;
	}


	/**
	 * Updating the table FastTraverse
	 * @return unknown_type
	 */
	function UpdateFastTraverse(){
		$this->ClearError();
		if($this->get('IdNode') > 0) {
			$sqls = sprintf("DELETE FROM FastTraverse WHERE IdChild = %d", $this->get('IdNode'));
			$dbObj = new DB();
	  		$dbObj->Execute($sqls);
			if ($dbObj->numErr) {
				$this->SetError(5);
			} else {
				$parent = $this->get('IdNode');
				$level = '0';
				do	{
					$dbObj = new DB();
					$sql = sprintf("INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (%d, %d, %d)",
							$parent, $this->get('IdNode'), $level);
					$dbObj->Execute($sql);
					unset($dbObj);
					$level++;
					$node = new Node($parent);
					$parent = $node->get('IdParent');
					//unset($node);
					xObject::destroy($node);
				} while($parent);
			}

		}else{
			$this->SetError(1);
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	function loadData(){
		$ret = array();
		$ret['nodeid'] = $this->get('IdNode');
		$ret['parent'] = $this->get('IdParent');
		$ret['type'] = $this->get('IdNodeType');
		$ret['name'] = $this->get('Name');
		$ret['state'] = $this->get('IdState');
		$ret['ctime'] = $this->get('CreationDate');
		$ret['atime'] = $this->get('ModificationDate');
		$ret['desc'] = $this->get('Description');
		$ret['path'] = $this->getPath();


		$ret['typename'] = $this->nodeType->get('Name');
		$ret['class'] = $this->nodeType->get('Class');
		$ret['icon'] = $this->nodeType->get('Icon');
		$ret['isdir'] = $this->nodeType->get('IsFolder');
		$ret['isfile'] = $this->nodeType->get('IsPlainFile');
		$ret['isvirtual'] = $this->nodeType->get('IsVirtualFolder');
		$ret['isfs'] = $this->nodeType->get('HasFSEntity');
		$ret['issection'] = $this->nodeType->get('IsSection');
		$ret['isxml'] = $this->nodeType->get('IsStructuredDocument');

		$version = $this->GetLastVersion();
		if(!empty($version) ) {
			$ret['version'] 		=   $version["Version"];
			$ret['subversion'] 	=   $version["SubVersion"];
			$ret['published'] 	=   $version["Published"];
			$ret['lastuser'] 		=   $version["IdUser"];
			$ret['date'] 			=   $version["Date"];
			$ret['lastusername'] =   $version["UserName"];
		}

		return $ret;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function DatosNodo(){
		$list = array();
		$list['IdNode']=$this->get('IdNode');
		$list['NodeName']=$this->get('Name');
		$list['NodeType']=$this->get('IdNodeType');
		$list['State']=$this->get('IdState');

		return $list;
	}

	/**
	 * Cleans last error
	 * @return unknown_type
	 */
	function ClearError(){
		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	 * Loads a class error
	 * @param $code
	 * @return unknown_type
	 */
	function SetError($code){
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 *
	 * @return unknown_type
	 */
	function HasError(){
		return $this->numErr;
	}

	/**
	 * Returns this node xml interpretation and its descendents.
	 *
	 * @return string xml with the node and descendent interpretation
	 */
	function ToXml($depth = 0, & $files, $recurrence = NULL) {
		global $STOP_COUNT;
		//TODO check if current user has permits to read this node, and if he does not, returns an empty string.
		if (!($this->get('IdNode') > 0)) {
			XMD_Log::warning(sprintf(_("It is being tried to load the unexistent node %s"),$this->get('IdNode') ));
			return false;
		}

		if (!is_array($files)) {
			$files = array();
		}

		$depth ++;
		$indexTabs = str_repeat("\t", $depth);
		if ($this->get('IdState') > 0) {
			$query = sprintf("SELECT s.Name as statusName"
						. " FROM States s"
						. " WHERE IdState = %d"
						. " LIMIT 1",
						$this->get('IdState'));
			$dbObj = new DB();
			$dbObj->Query($query);
			$statusName = $dbObj->GetValue('statusName');
			unset($dbObj);
		}

		$idNode = $this->get('IdNode');
		$nodeTypeName = $this->nodeType->get('Name');
		$nodeName = utf8_encode($this->get('Name'));
		$nodeParent = $this->get('IdParent');
		$nodeTypeClass = $this->nodeType->get('Class');
		$sharedWorkflow = $this->get('SharedWorkflow');

		$tail = '';
		if (!empty($sharedWorkflow)) {
			$tail .= sprintf(' SharedWorkflow="%s"', $sharedWorkflow);
		}

		$tail .= $this->class->getXmlTail();

		if (!empty($statusName)) {
			$tail .= sprintf(' state="%s"', utf8_encode($statusName));
		}

		// Getting node Properties

		$nodeProperty = new NodeProperty();
		$result = $nodeProperty->getPropertiesByNode($this->get('IdNode'));

		if (!is_null($result)) {
			foreach ($result as $resultData) {
				$tail .= sprintf(' %s="%s"', $resultData['Property'], $resultData['Value']);
			}
		}

		$xmlHeader = sprintf('<%s id="%d" name="%s" class="%s" nodetype="%d" parentid="%d"%s>',
					 $nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $this->nodeType->get('IdNodeType'), $nodeParent, $tail) . "\n";
		if ($STOP_COUNT == COUNT) {
			$STOP_COUNT = NO_COUNT;
		} else {
			$STOP_COUNT = NO_COUNT_NO_RETURN;
		}
		$xmlBody = $this->class->ToXml($depth, $files, $recurrence);
		if ($STOP_COUNT == NO_COUNT) {
			$STOP_COUNT = COUNT;
		} else {
			$STOP_COUNT = NO_COUNT;
		}

		/*
		 * This block of code makes if a xmlcontainer has not associated a visualtemplate,
		 * it looks automatically if some child has associated a visualtemplate and associate it to the container
		 */

		if (($this->nodeType->get('Class') == 'Xmlcontainernode') && (empty($idTemplate))) {
			$childrens = $this->GetChildren();
			if (!empty($childrens)) {
				reset($childrens);
				while (list(, $idChildrenNode) = each($childrens)) {
					$children = new Node($idChildrenNode);
					if (!($children->get('IdNode') > 0)) {
						XMD_Log::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"),$children->get('IdNode'),$this->get('IdNode')));
						continue;
					}

					if ($children->nodeType->GetIsStructuredDocument()) {
						$structuredDocument = new StructuredDocument($children->GetID());
						$idTemplate = $structuredDocument->GetDocumentType();
						$node = new Node($idTemplate);
						if (!($node->get('IdNode') > 0)) {
							XMD_Log::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"),$node->get('IdNode'),$this->get('IdNode')));
						continue;
						}
						if ($STOP_COUNT == COUNT) {
							$STOP_COUNT = NO_COUNT;
						} else {
							$STOP_COUNT = NO_COUNT_NO_RETURN;
						}
						$xmlBody .= $node->ToXml($depth, $files, $recurrence);
						if ($STOP_COUNT == NO_COUNT) {
							$STOP_COUNT = COUNT;
						} else {
							$STOP_COUNT = NO_COUNT;
						}
						//unset($node);
						xObject::destroy($node);
						break;
					}
				}
			}
		}


		if ($this->nodeType->get('IsStructuredDocument')) {
			$structuredDocument = new StructuredDocument($this->get('IdNode'));
			$idLanguage = $structuredDocument->GetLanguage();
			$node = new Node($idLanguage);
			if ($node->get('IdNode') > 0) {
				if ($STOP_COUNT == COUNT) {
					$STOP_COUNT = NO_COUNT;
				} else {
					$STOP_COUNT = NO_COUNT_NO_RETURN;
				}
				$xmlBody .= $node->toXml($depth, $files, $recurrence);
				unset($node);

				$idTemplate = $structuredDocument->GetDocumentType();
				$node = new Node($idTemplate);
				$xmlBody .= $node->ToXml($depth, $files, $recurrence);
				if ($STOP_COUNT == NO_COUNT) {
					$STOP_COUNT = COUNT;
				} else {
					$STOP_COUNT = NO_COUNT;
				}
			}
			unset($node);
		}

		$xmlFooter = sprintf('</%s>', $nodeTypeName) . "\n";
		if (!$STOP_COUNT && defined('COMMAND_MODE_XIMIO')) {
			global $PROCESSED_NODES, $LAST_REPORT, $TOTAL_NODES;
			$PROCESSED_NODES ++;

			$processedNodes = $TOTAL_NODES > 0 ? (int)(($PROCESSED_NODES * 100) / $TOTAL_NODES) : 0;
			if ($processedNodes > $LAST_REPORT) {
			echo sprintf(_("It has been processed a %s%% of the nodes"),$processedNodes);
			echo sprintf("\n");
			echo sprintf(_("The last processed node was %s"), $this->get('Name'));
			echo sprintf("\n");
					$LAST_REPORT = $processedNodes;
			}
		}


		unset($nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $statusName, $sharedWorkflow, $tail);

		// If a recursive importation was applied, here is where recurrence is performed
		if (is_null($recurrence) || (!is_null($recurrence) && $depth <= $recurrence)) {
			$childrens = $this->GetChildren();
			if($childrens) {
				reset($childrens);
				while(list(, $idChildren) = each($childrens)) {
					$childrenNode = new Node($idChildren);
					if (!($childrenNode->get('IdNode') > 0)) {
						XMD_Log::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"),$childrenNode->get('IdNode'),$this->get('IdNode')));
						continue;
					}
					$xmlBody .= $childrenNode->toXml($depth, $files, $recurrence);
					unset($childrenNode);
				}
			}
		}
		return 	$indexTabs . $xmlHeader .
					$xmlBody .
				$indexTabs . $xmlFooter;
	}

	/**
	 * Function which determines if the name $name is valid for the nodetype $nodeTypeID,
	 * nodetype is optional, if it is not passed, it is loaded from current node
	 *
	 * @param string $newName new node name
	 * @param int $nodeType
	 * @return bool
	 */
	function IsValidName($name, $idNodeType = 0){
		if ($idNodeType === 0) {
			$idNodeType = $this->nodeType->get('IdNodeType');
		}
		$nodeType = new NodeType($idNodeType);
		$nodeTypeName = $nodeType->get('Name');
		//the pattern and the string must be in the same encode
		$pattern1 = XmlBase::recodeSrc("/^[A-Za-z0-9�-��-��-���\_\-\.\s]+$/", XML::UTF8);
		$pattern2 = XmlBase::recodeSrc("/^[A-Za-z0-9�-��-��-���\_\-\.\s\@\:\/\?\+\=\#\%\*\,]+$/", XML::UTF8);
		$pattern3 =XmlBase::recodeSrc("/^[A-Za-z0-9�-��-��-���\_\-\.]+$/", XML::UTF8);
		$pattern4 =XmlBase::recodeSrc("/^[A-Za-z0-9�-��-��-���\_\-\.\@]+$/", XML::UTF8);
		$name = XmlBase::recodeSrc($name, XML::UTF8);
		unset($nodeType);
		if(	!strcasecmp($nodeTypeName, 'Action') ||
			!strcasecmp($nodeTypeName, 'Group') ||
			!strcasecmp($nodeTypeName, 'Language') ||
			!strcasecmp($nodeTypeName, 'LinkFolder') ||
			!strcasecmp($nodeTypeName, 'LinkManager') ||
			!strcasecmp($nodeTypeName, 'Role') ||
			!strcasecmp($nodeTypeName, 'WorkflowState')) {
			return (preg_match($pattern1,$name) > 0);
		} elseif (!strcasecmp($nodeTypeName, 'Link')) {
			return (preg_match($pattern2,$name) > 0);
		} elseif (!strcasecmp($nodeTypeName, 'User')) {
			return (preg_match($pattern4,$name) > 0);
		} else {
			return (preg_match($pattern3,$name) > 0);
		}
	}

	/**
	 *
	 * @param $idNodeType
	 * @param $parent
	 * @param $checkAmount
	 * @return unknown_type
	 */
 	function checkAllowedContent($idNodeType, $parent = NULL, $checkAmount = true) {
 		if (is_null($parent)) {
 			if (is_null($this->get('IdParent'))) {
 				XMD_Log::info(_('Error checking if the node is allowed - parent does not exist [1]'));
 				return false;
 			}
 			$parent = $this->get('IdParent');
 		}
		$parentNode = new Node($parent);
		if (!($parentNode->GetID() > 0)) {
			XMD_Log::info(_('Error checking if the node is allowed - parent does not exist [2]'));
			$this->messages->add(_('The specified parent node does not exist'), MSG_TYPE_ERROR);
			return false;
		}

		$nodeAllowedContents = $parentNode->GetCurrentAllowedChildren();
		if (!(count($nodeAllowedContents) > 0)) {
			XMD_Log::info(sprintf(_("The parent %s does not allow any nested node from him"),$parent));
			$this->messages->add(_('This node type is not allowed in this position'), MSG_TYPE_ERROR);
 			return false;
		}

		$nodeType = new NodeType($idNodeType);
		if (!($nodeType->GetID() > 0)) {
			XMD_Log::info(sprintf(_("The introduced nodetype %s does not exist"), $idNodeType));
			$this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
 			return false;
		}

		if (!in_array($idNodeType, $nodeAllowedContents)) {
			XMD_Log::info("The nodetype $idNodeType is not allowed in the parent (idnode =" .$parent . ") - (idnodetype =" . $parentNode->get('IdNodeType')
				. ") which allowed nodetypes are" . print_r($nodeAllowedContents, true));
			$this->messages->add(_('This node type is not allowed in this positionn'), MSG_TYPE_ERROR);
			return false;
		}

		$dbObj = new DB();
		$query = sprintf('SELECT Amount from NodeAllowedContents'
			. ' WHERE IdNodeType = %s AND NodeType = %s',
			$dbObj->sqlEscapeString($parentNode->nodeType->get('IdNodeType')),
			$dbObj->sqlEscapeString($idNodeType));

		$dbObj->query($query);
		if (!($dbObj->numRows > 0)) {
			$this->messages->add(_('The node is not allowed inside this parent'), MSG_TYPE_WARNING);
			return false;
		}
		if (!$checkAmount) {
			return true;
		}

		$amount = $dbObj->getValue('Amount');

		if ($amount == 0) {
			return true;
		}

		$nodeTypesInParent = count($parentNode->GetChildren($idNodeType));

		if ($amount > $nodeTypesInParent) {
			return true;
		}

		$this->messages->add(_('No more nodes can be created in this folder type'), MSG_TYPE_ERROR);
		return false;

	}

	/**
	 *
	 * @param $detailLevel
	 * @return unknown_type
	 */
	function toStr($detailLevel = DETAIL_LEVEL_LOW) {
		$details = sprintf("Nombre: %s\n", $this->get('Name'));
		$details .= sprintf("IdNodeType: %s\n", $this->get('IdNodeType'));

		if ($detailLevel <= DETAIL_LEVEL_LOW) {
			return $details;
		}

		$details .= sprintf("Description: %s\n", $this->get('Description'));
		if (is_object($this->class)) {
			$details .= sprintf("Type: %s\n", $this->nodeType->get('Name'));
		}
		$details .= sprintf("Path: %s\n", $this->GetPath());
		$details .= sprintf("Parent node: %s\n", $this->get('IdParent'));

		if ($detailLevel <= DETAIL_LEVEL_MEDIUM) {
			return $details;
		}

		return $details;
	}

	/**
	 *
	 * @param $property
	 * @param $withInheritance
	 * @return unknown_type
	 */
	function getProperty($property, $withInheritance = true) {
		$propertyValues = array();
		if ($withInheritance) {
			$sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";

			$db = new DB();
			$db->Query($sql);

			while (!$db->EOF) {

				// Getting property
				if ($db->getValue('IdNode') < 1) {
					break;
				}
				$nodeProperty = new NodeProperty();
				$propertyValue = $nodeProperty->getProperty($db->getValue('IdNode'), $property);

				if (!is_null($propertyValue)) {
					return $propertyValue;
				}

				$db->Next();
			}
		} else {
			$nodeProperty = new NodeProperty();
			return $nodeProperty->getProperty($this->get('IdNode'), $property);
		}

		XMD_Log::info(sprintf(_("Property %s not found"),$property));

		return NULL;
	}

	function getAllProperties($withInheritance = false) {

		$returnValue = array();
		$nodeProperty = new NodeProperty();

		if ($withInheritance) {
			$sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";

			$db = new DB();
			$db->Query($sql);

			while (!$db->EOF) {

				// Getting property
				if ($db->getValue('IdNode') < 1) {
					break;
				}
				$properties = $nodeProperty->find('Property, Value', 'IdNode = %s', array($db->getValue('IdNode')));

				if (empty($properties)) {
					$db->Next();
					continue;
				}

				if(is_array($properties) && count($properties) > 0) {
					foreach($properties as $propertyInfo) {
						if(array_key_exists($propertyInfo['Property'], $returnValue))
							continue;
						$returnValue[$propertyInfo['Property']][] = $propertyInfo['Value'];
					}
				}

				$db->Next();
			}
		} else {

			$properties = $nodeProperty->find('Property, Value', 'IdNode = %s', array($this->get('IdNode')));
			if (empty($properties)) {
				return NULL;
			}

			foreach($properties as $propertyInfo) {
				$returnValue[$propertyInfo['Property']][] = $propertyInfo['Value'];
			}
		}

		// Compactamos un poco el array
		foreach($returnValue as $key => $propertyInfo) {
			if (count($propertyInfo) == 1) {
				$propertyInfo[$key] = $propertyInfo[0];
			}
		}

		return $returnValue;
	}

	/**
	 * Returna boolean value for a property with 'true' or 'false'
	 * @param $property
	 * @param $withInheritance
	 * @return unknown_type
	 */
	function getSimpleBooleanProperty($property, $withInheritance = true){
		$property = $this->getProperty($property,$withInheritance);
		if(!((is_array($property)) && ($property[0]=="true"))){
			$value = false;
		}else{
			$value = true;
		}
		return $value;
	}


	function setSingleProperty($property, $value) {
		$nodeProperty = new NodeProperty();

		$properties = $nodeProperty->find('IdNodeProperty',
			'IdNode = %s AND Property = %s AND Value = %s',
			array($this->get('IdNode'), $property, $value));
		if (empty($properties)) {
			$propertyValue = $nodeProperty->create($this->get('IdNode'), $property, $value);
		}
	}
	/**
	 *
	 * @param $property
	 * @param $values
	 * @return unknown_type
	 */
	function setProperty($property, $values) {
		// Removing previous values
		if(!is_array($values) ) $values = array("0" => $values );
		$nodeProperty = new NodeProperty();
		$nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);

		// Adding new values

		$n = sizeof($values);

		for ($i = 0; $i < $n; $i++) {
			$this->setSingleProperty($property, $values [$i]);
		}

	}

	/**
	 *
	 * @param $property
	 * @return unknown_type
	 */
	function deleteProperty($property) {
		if (!($this->get('IdNode') > 0)) {
			$this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
			return false;
		}
		$nodeProperty = new NodeProperty();
		return $nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);
	}

	function deletePropertyValue($property, $value) {
		if (!($this->get('IdNode') > 0)) {
			$this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
			return false;
		}

		$nodeProperty = new NodeProperty();
		$properties = $nodeProperty->find('IdNodeProperty',
			'IdNode = %s AND Property = %s AND Value = %s',
			array($this->get('IdNode'), $property, $value), MONO);

		//debug::log($properties);
		foreach($properties as $idNodeProperty) {
			$nodeProperty = new NodeProperty($idNodeProperty);
			$nodeProperty->delete();
		}
	}

	/**
	 * This function overwrite the workflow_forward function. The action function is then deprecated.
	 *
	 * @param int $idUser
	 * @param int $idGroup
	 * @return int status
	 */
	function GetNextAllowedState($idUser, $idGroup) {
		if (!($this->get('IdNode') > 0)) {
			return NULL;
		}

		if (!($this->get('IdState') > 0)) {
			return NULL;
		}

		$user = new User($idUser);
		$idRole = $user->GetRoleOnNode($this->get('IdNode'), $idGroup);

		$role = new Role($idRole);
		$allowedStates = $role->GetAllowedStates($idRole);

		$idNextState = $this->get('IdState');
		if (is_array($allowedStates) && !empty($allowedStates)) {

			$workflow = new WorkFlow($this->get('IdNode'), $idNextState);
			$idNextState=null;
			do {

				$idNextState = $workflow->GetNextState();

				if (empty($idNextState)) {
					return NULL;
				}else if ( in_array($idNextState, $allowedStates) ) {
					return $idNextState;
				}
				$workflow = new WorkFlow($this->get('IdNode'), $idNextState);
			} while (!$workflow->IsFinalState() );
		}

		return NULL;
	}

	/**
	 *  Update node childs FastTraverse info
	 * @return unknown_type
	 */
	function UpdateChildren() {
		$arr_children = $this->GetChildren();
		if(!empty($arr_children) ) {
			foreach($arr_children as $child) {
				$node_child = new Node($child);
				$node_child->UpdateFastTraverse();
				$node_child->RenderizeNode();
				$node_child->UpdateChildren();
			}
		}
	}

	/**
	 *
	 * @param $idPipeline
	 * @return unknown_type
	 */
	function updateToNewPipeline($idPipeline) {
		$db = new DB();
		$query = sprintf("SELECT IdChild FROM FastTraverse WHERE IdNode = %d", $this->get('IdNode'));
		$db->Query($query);

		while (!$db->EOF) {
			$idNode = $db->GetValue('IdChild');
			$node = new Node($idNode);
			$idStatus = $node->getFirstStatus();
			if ($idStatus > 0) {
				$node->set('IdState', $idStatus);
				$node->update();
			}
			$db->Next();
		}
	}


	function GetLastVersion() {
		$sql = "SELECT V.Version, V.SubVersion, V.IdUser, V.Date, U.Name as UserName  ";
		$sql .= " FROM Versions V INNER JOIN Users U on V.IdUser = U.IdUser ";
		$sql .= " WHERE V.IdNode = '".$this->get('IdNode')."' ";
		$sql .= " ORDER BY V.IdVersion DESC LIMIT 1 ";
		$dbObj = new DB();
		$dbObj->Query($sql);
		if($dbObj->numRows > 0) {
			if($dbObj->GetValue("Version")==0)
				$state=0;
			elseif ($dbObj->GetValue("Version")!=0 && $dbObj->GetValue("SubVersion")==0)			
				$state=1;
			else
				$state=2;

			return array(
					"Version"   =>   $dbObj->GetValue("Version"),
					"SubVersion"=>   $dbObj->GetValue("SubVersion"),
					"Published"=>    $state,
					"IdUser"    =>   $dbObj->GetValue("IdUser"),
					"Date"      =>   $dbObj->GetValue("Date"),
					"UserName"  =>   $dbObj->GetValue("UserName")
			);

		} else {
			$this->SetError(5);
		}
	}

	/**
	 *
	 * @param $type
	 * @return unknown_type
	 */
	function getTemplates($type = NULL) {

		$idProject = $this->GetProject();

		if (!($idProject > 0)) {
			XMD_Log::debug(_('It was not possible to obtain the node project folder'));
			return NULL;
		}

		$project = new Node($idProject);
		if (!($project->get('IdNode') > 0)) {
			XMD_Log::debug('An unexistent project has be obtained');
			return NULL;
		}

		$folder = new Node($project->GetChildByName(Config::GetValue("VisualTemplateDir")));
		if (!($folder->get('IdNode') > 0)) {
			XMD_Log::debug('Pvd folder could not be obtained');
			return NULL;
		}

		$templates = $this->getProperty('DefaultSchema');

		if (empty($templates)) {
			$templates = '5045,5078';
		} else {
			$templates = implode(',', $templates);
		}


		$templates = $folder->find('IdNode', 'IdParent = %s AND IdNodeType in (%s) ORDER BY Name',
			array($folder->get('IdNode'), $templates),
			MONO, false);
		if (!empty($type)) {
			foreach ($templates as $key => $idTemplate) {
				$template = new Node($idTemplate);
				$templateType = $template->getProperty('TemplateType');

				if (is_array($templateType) && count($templateType) == 1) {
					$templateType = $templateType[0];
				}

				if ($templateType != $type) {
					unset($templates[$key]);
				}
			}
		}

		if (!is_array($templates)) {
			XMD_Log::debug(sprintf('The specified folder (%s) is not containing pvds',
			$folder->get('IdNode')));
			return NULL;
		}

		return $templates;
	}

  /**
		@param $destNodeId: Destination node

  */
  function checkTarget($destNodeId) {
		$changeName=0; //assuming by default they're not the same
		$existing=0;
		$amount=0;
		$insert=0; //by default, dont insert.

		$actionNodeId = $this->GetID();
		$actionNodeType= $this->Get('IdNodeType');

		$destNode = new Node($destNodeId);
		$destNodeType=$destNode->Get('IdNodeType');

		//parents data.
		$parent = new Node($this->GetParent()); //parent node
		$parentname=$parent->Get('Name');


//query to NodeAllowedContents
		$sql1="SELECT Amount FROM NodeAllowedContents WHERE IdNodeType=$destNodeType and NodeType=$actionNodeType";
                $db=new DB();
                $db->Query($sql1);
                while(!$db->EOF){
                        $amount=$db->getValue('Amount');
                        $db->Next();
                }
		if($amount==NULL){$amount=-1;}//If there is not a relation allowed, abort the copy.

//query to FastTraverse
		$sql2="SELECT count(Depth) FROM FastTraverse WHERE FastTraverse.IdNode=$destNodeId and IdChild in (SELECT IdNode FROM Nodes WHERE IdNodeType=$actionNodeType) and Depth=1";
        	$db->Query($sql2);
        	while(!$db->EOF){
        	        $existing=$db->getValue('count(Depth)');
                	$db->Next();
        	}
		if($existing==NULL){$existing=0;} //dont exist a relation yet

		//first check, insert allowed?
		if($amount==0){$insert=1;}		//destination node allows an infinite number of copies.
		else if($amount==-1){$insert=0;}	//destination node does not allow this kind of content.
		else{ 					//limited capacity.
			if($amount>$existing){$insert=1;} //there is place for another copy.
		}

		//only if we can insert, we must check if the copy is going to be at the same level
		if($insert==1){
			if($destNodeId==$parent->Get('IdNode')){$changeName=1;}//coinciden. Copiamos el nodo al mismo nivel y debemos renombrarlo.
		}
		//$data=array('NodoCopia Id'=>$actionNodeId,'NodoCopia Tipo'=>$actionNodeType,'NodoDest Id'=>$destNodeId,'NodoDest Tipo'=>$destNodeType,'changeName'=>$changeName,'existing'=>$existing,'amount'=>$amount,'insert'=>$insert,'sentencia'=>$sql2);
		return array('NodoDest Id'=>$destNodeId,'NodoDest Tipo'=>$destNodeType,'changeName'=>$changeName,'insert'=>$insert);
  }
}

?>
