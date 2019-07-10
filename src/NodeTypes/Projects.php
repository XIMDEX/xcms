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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

/**
 * @brief Handles the Projects Node
 *
 * The Projects Node is the container of all ximDEX projects
 */
class Projects extends FolderNode
{
    private static $ROOTNODEID = 10000;

    /**
     * Gets the node with the given node id or null if the node does not exist
     *
     * @param int $nodeId : The node id to be get
     * @return \Ximdex\Models\Node : The requested node or null if the node does not exist
     */
    public function getProject(int $idProject = null)
    {
        if ($idProject) {
            return $this->existsNode($idProject) ? new \Ximdex\Models\Node($idProject) : null;
        } else {
            return $this->parent;
        }
    }

    /**
     * Gets the project info
     * It will return the following properties of the project:
     *  - nodeid
     *  - name
     *  - creationDate (timestamp)
     *  - path
     *
     * @return array : containing the project information
     */
    public function getProjectInfo()
    {
        if ($this->parent == null) {
            return array();
        } else {
            return array(
                'nodeid' => $this->parent->GetID(),
                'name' => $this->parent->GetNodeName(),
                'creationDate' => $this->parent->get('CreationDate'),
                'path' => $this->parent->GetPath(),
            );
        }
    }

    public static function getProjectsInfo()
    {
        $projectsInfo = array();
        $projects = self::getAllProjects();
        foreach ($projects as $project) {
            $projectsInfo[] = $project->getProjectInfo();
        }
        return $projectsInfo;
    }

    private static function getAllProjects()
    {
        $res = array();
        $root = new \Ximdex\Models\Node(self::$ROOTNODEID);
        $projectIds = $root->GetChildren();
        if (count($projectIds) > 0) {
            foreach ($projectIds as $pid) {
                if ($pid != 8122) {
                    $res[] = new Projects($pid);
                }
            }
        }
        return $res;
    }
    
    
}
