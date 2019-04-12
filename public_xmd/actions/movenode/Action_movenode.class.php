<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\Node;
use Ximdex\Models\NodeAllowedContent;
use Ximdex\Modules\Manager;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\XsltNode;
use Ximdex\Runtime\App;
use Ximdex\Sync\SynchroFacade;

Manager::file('/actions/copy/Action_copy.class.php');

class Action_movenode extends Action_copy
{
   /**
    * Main method: shows initial form
    * 
    * {@inheritDoc}
    * @see Action_copy::index()
    */
   public function index()
   {
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $idNodeType = $node->get('IdNodeType');
        $nac = new NodeAllowedContent();
        $allowedNodeTypes = $nac->find('IdNodeType', 'NodeType = %s', array($idNodeType), MONO);
        $sync = new SynchroFacade();
        $isPublished = $sync->isNodePublished($idNode);
        $childList = $node->getChildren();
        if ($childList) {
        	foreach($childList as $child) {
        		$childNode = new Node($child);
        		$childList = array_merge($childList, $childNode->TraverseTree());
        	}
        	$pendingTasks = [];
        	foreach ($childList as $nodeID) {
        		$pendingTasks =  array_merge($pendingTasks, $sync->getPendingTasksByNode($nodeID));
        		$numPendingTasks = count($pendingTasks);
        		$isPublished = $sync->isNodePublished($nodeID);
        		if ($isPublished && $numPendingTasks > 0) {
        			break;
        		}
        	}
        }
        $targetNodes = $this->getTargetNodes($node->getID(), $node->getNodeType());
		$this->addCss('/actions/copy/resources/css/style.css');
		$values = [
			'id_node' => $node->get('IdNode'),
			'name' => $node->getNodeName(),
			'nodetypeid' => $node->nodeType->get('IdNodeType'),
			'nameNodeType' => $node->nodeType->get('Name'),
			'allowed_nodeTypes' => implode(',', $allowedNodeTypes),
			'filtertype' => $node->nodeType->get('Name'),
            'targetNodes' => $targetNodes,
			'target_file' => null,
			'node_path' => $node->getPath(),
			'isPublished' => $isPublished,
		    'nodeTypeID' => $node->nodeType->getID(),
		    'node_Type' => $node->nodeType->getName(),
			'go_method' => 'move_node'
		];
		$this->render($values, NULL, 'default-3.0.tpl');
    }

	public function move_node()
	{
      	$idNode = (int) $this->request->getParam('nodeid');
		$targetParentID = (int) $this->request->getParam('targetid');
		$unpublishDoc = ($this->request->getParam('unpublishdoc') == 1) ? true : false;
		$node = new Node($idNode);
		$checks = $node->checkTarget($targetParentID);
		if (null == $checks || ! $checks['insert'] ) {
			$this->messages->add(_('Moving node to selected destination is not allowed'), MSG_TYPE_ERROR);
			$values = [
			    'messages' => $this->messages->messages
			];
		} else {
		    $this->move($idNode, $targetParentID, $unpublishDoc);
		    $values = [
    			'messages' => $this->messages->messages,
    			'id_node' => $idNode,
    			'params' => '',
    			'nodeURL' => App::getUrl('/?action=movenode&nodeid=' . $idNode),
    			'action_with_no_return' => true,
    			'parentID' => $targetParentID,
    			'oldParentID' => $node->getParent()
		    ];
		}
		$this->sendJSON($values);
	}

	public function confirm_move()
	{
		$idNode = (int) $this->request->getParam('nodeid');
		$targetParentID = (int) $this->request->getParam('targetid');
		$node = new Node($idNode);
		$checks = $node->checkTarget($targetParentID);
		$smarty = null;
		$genericTemplate = null;
		if (null == $checks || ! $checks['insert']) {
			$this->messages->add(_('Moving node to selected destination is not allowed'), MSG_TYPE_ERROR);
		} else {
		  $smarty = 'confirm';
		  $genericTemplate = 'default-3.0.tpl';
		}
		$targetNode = new Node($targetParentID);
		$values = [
			'messages' => $this->messages->messages,
			'nodeid' => $idNode,
			'nodeName' => $node->getNodeName(),
			'nodePath' => $node->getPath(),
			'targetPath' => $targetNode->getPath(),
			'targetid' => $targetParentID,
			'params' => '',
			'nodeURL' => App::getUrl('/?action=movenode&nodeid=' . $idNode),
			'go_method' => 'move_node'
		];
		$this->render($values, $smarty, $genericTemplate);
	}
  
    /**
     * Check if the propousal node can be target for the current one.
     * Must be in the same project
     * 
     * {@inheritDoc}
     * @see Action_copy::checkTargetConditions()
     */
    protected function checkTargetConditions(int $idCurrentNode, int $idCandidateNode) : bool
    {
        $node = new Node($idCurrentNode);
        $candidateNode = new Node($idCandidateNode);
        
        // If a different project
        if ($node->getProject() != $candidateNode->getProject()) {
            return false;
        }
        
        // Has a child with same name
        if ($candidateNode->getChildByName($node->getNodeName())) {
            return false;
        }
        
        // Candidate node is inside the current node to move
        foreach (FastTraverse::getParents($idCandidateNode) as $parentId) {
            if ($parentId == $idCurrentNode) {
                return false;
            }
        }
        return true;
    }
    
    private function move(int $idNode, int $targetParentID, bool $unpublishDoc)
    {
        $node = new Node($idNode);
        $err = $this->baseIO_MoveNode($idNode, $targetParentID);
        if (! $err) {
            $this->messages->add(sprintf(_('Node %s has been successfully moved'), $node->getNodeName()), MSG_TYPE_NOTICE);
        } else {
            $this->messages->add(_($err), MSG_TYPE_ERROR);
            return false;
        }
        
        // Update templates_includes files if node type is a XSL template
        if ($node->getNodeType() == NodeTypeConstants::XSL_TEMPLATE) {
            $xsltNode = new XsltNode($node);
            if ($xsltNode->move_node($targetParentID) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        }
        $targetParent = new Node($targetParentID);
        return $targetParent->class->updatePath();
    }
    
    private function baseIO_MoveNode(int $nodeID, int $targetParentNodeID)
    {
        Logger::info("IO-movenode -> nodeID = $nodeID, nodeID_destino = $targetParentNodeID");
        $node = new Node($nodeID);
        if (! $node->get('IdNode')) {
            return _('Source node does not exist') . $node->msgErr; // Operation error
        }
        $target = new Node($targetParentNodeID);
        if (! $target->get('IdNode')) {
            return _('Source node does not exist') . $node->msgErr; // Operation error
        }
        $parent = $node->getParent();
        if ($parent == $targetParentNodeID) {
            return _('This node is already associated with that parent ') . $node->msgErr; // Operation error
        }
        $res = $node->moveNode($target->get('IdNode'));
        if ($node->hasError() or $res === false) {
            return _('The operation has failed') . ' ' . $node->getError(); // Operation error
        }
        return null;
    }
}
