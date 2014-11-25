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
ModulesManager::file("/inc/model/node.php");
//ModulesManager::file("/inc/model/user.php");
ModulesManager::file("/services/NodetypeService.class.php");

/**
 * <p>Service responsible of deal with nodes</p>
 *
 */
class ProjectService{
    private static $ROOTNODEID = 10000;
    public $nodeid = null;
    /**
     * <p>Default constructor</p>
     */
    public function __construct($idNode = null, $lazyMode = true){
        if ($idNode)
            $this->node = new Node($idNode);
        $this->lazyMode = $lazyMode;
    }

    /**
     * <p>Checks if the given node id exists</p>
     * @param int $nodeId The node id to be checked
     */
    public function existsProject($projectId){
        $id = (int) $projectId;
        $node = new Node($id);
        return $node->get("IdNode") != null;
    }

    /**
     * <p>Gets the node with the given node id or null if the node does not exist</p>
     * 
     * @param int $nodeId The node id to be get
     * @return Node The requested node or null if the node does not exist
     */
    public function getProject($idProject=null){
        if ($idProject)
            return $this->existsNode($idProject) ? new Node($idProject) : null;
        else
            return $this->node;
    }


    /**
     * <p>Gets the project info</p>
     * <p>It will return the following properties of the project:
     *  <ul>
     *      <li>nodeid</li>
     *      <li>name</li>
     *      <li>creationDate (timestamp)</li>
     *      <li>path</li>
     *  </ul>
     * </p>
     *
     * @param string $projectId the project id to get the information
     * @return array containing the project information
     */
    public function getProjectInfo(){

        if ($this->node == null){
            return array();
        }
        else{
            return array(
                'nodeid' => $this->node->GetID(),
                'name' => $this->node->GetNodeName(),
                'creationDate' => $this->node->get('CreationDate'),
                'path' => $this->node->GetPath(),
            );
        }
    }
    
    public static function getProjectsInfo(){
        $projectsInfo=array();
        $projects=self::getAllProject();
        foreach($projects as $project){
            $projectsInfo[]=$project->getProjectInfo();   
        }    
        return $projectsInfo;    
    }

    public static function getAllProject(){
        $res=array();
        $root=new Node(self::$ROOTNODEID);
        $projectIds=$root->GetChildren();
        if(count($projectIds)>0){
            foreach($projectIds as $pid){
                if($pid!=8122){
                    $res[] = new ProjectService($pid);
                }
            }
        }
        return $res;
    }

    /**
     * <p>Returns the Ximdex root node (projects)</p>
     * 
     * @return Node The root node called projects
     */
    public function getRootProject() {
        return $this->getNode(self::$ROOTNODEID);
    }

    /**
     * <p>Deletes the given node</p>
     * 
     * @param mixed $node The node id or the Node to be deleted
     * 
     * @return boolean indicates whether the node has been deleted successfully or not
     */
    public function deleteProject($project) {
        $nid = $node instanceof Node ? $node->GetID() : $node;
        
        $n = new Node($nid);
        $res = $n->DeleteNode(true);
        
        return $res > 0;
        
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

}

?>