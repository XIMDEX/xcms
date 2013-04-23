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




ModulesManager::file('/actions/movenode/baseIO.php');

class Action_movenode extends ActionAbstract {

   // Main method: shows initial form
    function index () {
      	$idNode		= (int) $this->request->getParam("nodeid");
      	$actionID	= (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");
		

		$node = new Node($idNode);
		$idNodeType = $node->get('IdNodeType');

		$nac = new NodeAllowedContent();
		$allowedNodeTypes = $nac->find('IdNodeType', 'NodeType = %s', array($idNodeType), MONO);

		$parent = new Node($node->GetParent());
		$depList=array();
		$sync = new SynchroFacade();
		$isPublished = $sync->isNodePublished($idNode);
		$childList = $node->GetChildren();

		if ($childList) {
			foreach($childList as $child) {
				$childNode = new Node($child);
				$childList = array_merge($childList, $childNode->TraverseTree());
			}

			$pendingTasks = array();
			foreach($childList as $nodeID) {
				$pendingTasks =  array_merge($pendingTasks, $sync->getPendingTasksByNode($nodeID));
				$numPendingTasks = count($pendingTasks);
				$isPublished = $sync->isNodePublished($nodeID);

				if($isPublished && $numPendingTasks > 0) {
					break;
				}
			}
		}

//		$this->addJs('/actions/movenode/resources/js/movenode.js');
		$this->addJs('/actions/copy/resources/js/treeSelector.js');

				$values = array(
					'id_node' => $node->get('IdNode'),
					'name' => $node->GetNodeName(),
					'nodetypeid' => $node->nodeType->get('IdNodeType'),
					'nameNodeType' => $node->nodeType->get('Name'),
					'allowed_nodeTypes' => implode(',', $allowedNodeTypes),
					'filtertype' => $node->nodeType->get('Name'),
					'target_file' => null,
					'node_path' => $node->GetPath(),
					'isPublished' => $isPublished,
					"go_method" => "move_node"
				);

		$this->render($values, NULL, 'default-3.0.tpl');
    }

	function move_node() {
      $idNode		= (int) $this->request->getParam("nodeid");

		$targetParentID =  $this->request->getParam("targetid");
		$unpublishDoc = ($this->request->getParam("unpublishdoc") == 1) ? true : false;

		$node = new Node($idNode);
		$checks = $node->checkTarget($targetParentID);
		if(null == $checks || !$checks["insert"] ) {
			$this->messages->add(_("Moving node to selected destination is not allowed"), MSG_TYPE_ERROR);
		}else {
		  $this->_move($idNode, $targetParentID,  $unpublishDoc);
		}

		$values = array(
			'messages' => $this->messages->messages,
			'id_node' => $idNode,
			'params' => '',
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?action=movenode&nodeid={$idNode}",
			'action_with_no_return' => true
		);

		$this->render($values);
	}

	function confirm_move(){
		$idNode = (int) $this->request->getParam("nodeid");

		$targetParentID =  $this->request->getParam("targetid");
		$unpublishDoc = ($this->request->getParam("unpublishdoc") == 1) ? true : false;

		$node = new Node($idNode);
		$checks = $node->checkTarget($targetParentID);
		$smarty = null;
		$genericTemplate = null;
		if(null == $checks || !$checks["insert"] ) {
			$this->messages->add(_("Moving node to selected destination is not allowed"), MSG_TYPE_ERROR);
		}else {
		  $smarty = "confirm";
		  $genericTemplate = "default-3.0.tpl";
		}

		$targetNode = new Node($targetParentID);

		$values = array(
			'messages' => $this->messages->messages,
			'nodeid' => $idNode,
			"nodeName" => $node->GetNodeName(),
			"nodePath" => $node->GetPath(),
			"targetPath" => $targetNode->GetPath(),
			"targetid" => $targetParentID,
			'params' => '',
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?action=movenode&nodeid={$idNode}",
			"go_method" => "move_node"
		);

		$this->render($values,$smarty, $genericTemplate);
	}

  function _move($idNode, $targetParentID,  $unpublishDoc) {

		$node = new Node($idNode);
		$oldParentId = $node->GetParent();
		$parent = null;

		$err = baseIO_MoveNode($idNode, $targetParentID);

		if(!$err) {
			$this->messages->add(sprintf(_("Node %s has been successfully moved"), $node->GetNodeName()), MSG_TYPE_NOTICE);
			$sync = new SynchroFacade();
			$sync->deleteAllTasksByNode($idNode, $unpublishDoc);
		}else {
			$this->messages->add(_($err), MSG_TYPE_ERROR);
		}


		$this->reloadNode($oldParentId);
		$this->reloadNode($targetParentID);

		$targetParent = new Node($targetParentID);
		$targetParent->class->updatePath();

  }
}
?>
