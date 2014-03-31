<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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
include_once(XIMDEX_ROOT_PATH . "/inc/modules/ModulesManager.class.php");
ModulesManager::file("/inc/model/node.inc");
ModulesManager::file("/inc/model/user.inc");
ModulesManager::file("/services/NodetypeService.class.php");

/**
 * <p>Service responsible of deal with nodes</p>
 *
 */
class NodeService
{
    private static $PROJECTS_ROOT_NODE_ID = 10000;
    public $node = null;
    private $lazyMode = true;
    /**
     * <p>Default constructor</p>
     */
    public function __construct($idNode = null, $lazyMode = true)
    {
        if ($idNode)
            $this->node = new Node($idNode);
        $this->lazyMode = $lazyMode;
    }

    /**
     * <p>Checks if the given user has the permission over the node</p>
     * @param int $userId the user name
     * @param int $nodeId the node id
     * @param string $permission the permission
     * 
     * @return a boolean indicating if the user has permission over the node
     */
    public function hasPermissionOnNode($username, $nodeid, $permission = "View all nodes")
    {
        $user = new User();
        $user->setByLogin($username);
        $user_id = $user->GetID();
        if ($user_id == null) {
            return false;
        }

        if ($nodeid == null) {
            return false;
        }

        $node = new Node($nodeid);

        if ($node->GetID() == null) {
            return false;
        }

        $hasPermissionOnNode = $user->HasPermissionOnNode($nodeid, $permission);

        return $hasPermissionOnNode;
    }

    /**
     * <p>Checks if the given node id exists</p>
     * @param int $nodeId The node id to be checked
     */
    public function existsNode($nodeId)
    {
        $id = (int) $nodeId;
        $node = new Node($id);

        return $node->get("IdNode") != null;
    }

    /**
     * <p>Gets the node with the given node id or null if the node does not exist</p>
     * 
     * @param int $nodeId The node id to be get
     * @return Node The requested node or null if the node does not exist
     */
    public function getNode($idNode=null)
    {
        if ($idNode)
            return $this->existsNode($idNode) ? new Node($idNode) : null;
        else
            return $this->node;
    }

    /**
     * <p>Gets the node info</p>
     * <p>It will return the following properties of the node:
     *  <ul>
     *      <li>nodeid</li>
     *      <li>nodeType</li>
     *      <li>name</li>
     *      <li>version (for nodes having version or 0 otherwise)</li>
     *      <li>creationDate (timestamp)</li>
     *      <li>modificationDate (timestamp)</li>
     *      <li>path</li>
     *      <li>parent</li>
     *      <li>children</li>
     *  </ul>
     * </p>
     *
     * @param string $nodeid the node id to get the information
     * @return array containing the node information
     */
    public function getNodeInfo($nodeid)
    {
        $node = $this->getNode($nodeid);

        if ($node == null)
            return array();
        else
            return array(
                'nodeid' => $node->GetID(),
                'nodeType' => $node->GetNodeType(),
                'name' => $node->GetNodeName(),
                'version' => $node->GetLastVersion() ? $node->GetLastVersion() : 0,
                'creationDate' => $node->get('CreationDate'),
                'modificationDate' => $node->get('ModificationDate'),
                'path' => $node->GetPath(),
                'parent' => $node->GetParent(),
                'children' => $node->GetChildren(),
            );
    }
    
    /**
     * <p>Checks if the given node (object or id) is of the specified node type</p>
     * 
     * @param mixed $node The <code>Node</code> object or the node id to be checked
     * @param string $nodetypeId The Node Type
     * 
     * @return boolean indicating if the node is of the specified node type or not
     */
    public function isOfNodeType($node, $nodetypeId) {
      $n = is_object($node) && $node instanceof Node ? $node : new Node($node);
      
      return $n->GetNodeType() == $nodetypeId;
    }
    
    /**
     * <p>Returns the Ximdex root node (projects)</p>
     * 
     * @return Node The root node called projects
     */
    public function getRootNode() {
        return $this->getNode(self::$PROJECTS_ROOT_NODE_ID);
    }

    /**
     * <p>Deletes the given node</p>
     * 
     * @param mixed $node The node id or the Node to be deleted
     * 
     * @return boolean indicates whether the node has been deleted successfully or not
     */
    public function deleteNode($node) {
        $nid = $node instanceof Node ? $node->GetID() : $node;
        
        $n = new Node($nid);
        $res = $n->DeleteNode(true);
        
        return $res > 0;
        
    }

    /**
     * Check if the nodetype allow metadata.
     * @return boolean
     */
    public function isEnabledMetadata() {

        $nodeTypeService = new NodeTypeService($this->node->get("IdNodeType"));        
        return $nodeTypeService->isEnabledMetadata();
    }

    /**
     * Check if the nodetype must have metadata.
     * @return boolean
     */
    public function isMetadataForced(){

        $nodeTypeService = new NodeTypeService($this->node->get("IdNodeType"));
        return $nodeTypeService->isMetadataForced();
    }

    public function getSiblings(){        
        $result = $this->node->find("IdNode","idparent=%s",array($this->node->get("IdParent")),MONO);
        for($i = 0; count($result); $i++){
            if ($result[$i] == $this->node->nodeID){
                unset($result[$i]);
                break;
            }
        }
        
        return $this->returnNode(array_values($result));
    }

    public function getParent(){    
        $idParent = $this->node->get("IdParent");
        return $this->returnNode($idParent);
    }

    public function setLazyMode($lazyMode){
        $this->lazyMode = $lazyMode;
    }

    private function returnNode($valueToReturn){

        if ($this->lazyMode)
            return $valueToReturn;

        $result = array();
        //If $valueToReturn is array of Ids
        if (is_array($valueToReturn)){            
            foreach ($valueToReturn as $idNode) {
                $tmpNode = new Node($idNode);
                if ($tmpNode->get("IdNode"))
                    $result[] = $tmpNode;
            }            
        }else if (is_int($valueToReturn)){ //If $valueToReturn is a node id.
                    $result = new Node($valueToReturn);
        }else if (is_object($valueToReturn) 
            && strtolower(get_class($valueToReturn)) == "node" ){ //If $valueToReturn is a Node object
            return $valueToReturn;
        }

        return $result;
    }
}

?>
