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
ModulesManager::file('/inc/model/orm/NodeAllowedContents_ORM.class.php');
ModulesManager::file('/actions/copy/baseIO.php');

class Action_copy extends ActionAbstract {

    /**
     * Main method
     */
    function index() {
        
        $node = new Node($this->request->getParam('nodeid'));

        if (!($node->get('IdNode') > 0)) {
            $this->messages->add(_('Error with parameters'), MSG_TYPE_ERROR);
            $values = array('messages' => $this->messages->messages);
            $this->renderMessages();
        } else {
           
            $targetNodes = $this->getTargetNodes($node->GetID(), $node->GetNodeType());
            $values = array(
                'id_node' => $node->get('IdNode'),
                'nodetypeid' => $node->nodeType->get('IdNodeType'),
                'filtertype' => $node->nodeType->get('Name'),
                'targetNodes' => $targetNodes,
                'node_path' => $node->GetPath(),
                'go_method' => 'copyNodes'
            );
            
            $this->addCss('/actions/copy/resources/css/style.css');
            $this->render($values, NULL, 'default-3.0.tpl');
        }
    }

    function copyNodes() {
        //Extracts info of actual node which the action is executed
        $nodeID = $this->request->getParam("nodeid");
        $node = new Node($nodeID);
        $destIdNode = $this->request->getParam('targetid');
        $target = new Node($destIdNode);

        $nodename = $node->Get('Name');
        $idnode = $node->Get('IdNode');
        $idnodetype = $node->nodeType->get('IdNodeType');

        $nodeID = $this->request->getParam("nodeid");
        $destIdNode = $this->request->getParam('targetid');

        $recursive = $this->request->getParam('recursive');
        $recursive = $recursive == 'on' ? true : false;

        if ($nodeID == $destIdNode) {
            $this->messages->add(_('Source node cannot be the same as destination node'), MSG_TYPE_ERROR);
            $this->render(array('messages' => $this->messages->messages));
            return;
        }

        $this->messages = copyNode($nodeID, $destIdNode, $recursive);
        $this->reloadNode($destIdNode);

        $values = array('messages' => $this->messages->messages);
        $this->render($values, 'index');
    }
    
    /**
     * Get an array with the available target info
     * @param int $idNode of the node to move.
     * @param int $idNodeType of the node to move.
     * @return array With path and idnode for every target folder
     */
    protected function getTargetNodes($idNode, $idNodeType){
        
        $nodeAllowedContent = new NodeAllowedContent();
        $arrayNodeTypesAllowed = $nodeAllowedContent->getAllowedParents($idNodeType);
        
        $node = new Node($idNode);
        $arrayIdnodes = $node->find("IdNode", "idnodetype in (".implode(",", $arrayNodeTypesAllowed).") and idnode <> %s order by path",array( $node->GetParent()),MONO);        
      
        $idTargetNodes = array();
        foreach ($arrayIdnodes as $idCandidateNode) {            
            if ($this->checkTargetConditions($idNode, $idCandidateNode))
                $idTargetNodes[] = $idCandidateNode;
        }
        $targetNodes = array();
        foreach ($idTargetNodes as $idTargetNode) {
            $targetNode =  new Node($idTargetNode);                
            $arrayAux["path"] = str_replace("/Ximdex/Projects/","", $targetNode->GetPath());
            $arrayAux["idnode"] = $targetNode->GetID();
            $targetNodes[] = $arrayAux;
        }
        return $targetNodes;
    }
    
    /**
     * Check if the propousal node can be target for the current one.
     * Must be in the same project
     * @param int $idCurrentNode
     * @param int $idCandidateNode
     * @result boolean True if everything is ok.
     */
    protected function checkTargetConditions($idCurrentNode, $idCandidateNode){
        $node = new Node($idCurrentNode);
        $candidateNode = new Node($idCandidateNode);
        return $node->getProject() == $candidateNode->getProject();
    }

    function checkNodeName() {
        $actionNodeId = $this->request->getParam("nodeid"); //node to copy
        $destNodeId = $this->request->getParam('targetid'); //destination node
        $actionNode = new Node($actionNodeId);
        $data = $actionNode->checkTarget($destNodeId);
        $this->sendJSON($data);
    }
}

?>
