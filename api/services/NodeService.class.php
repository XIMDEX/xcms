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

include_once(XIMDEX_ROOT_PATH."/inc/modules/ModulesManager.class.php");
ModulesManager::file("/inc/model/node.php");
ModulesManager::file("/inc/model/user.php");

/**
 * <p>Service responsible of deal with nodes</p>
 *
 */
class NodeService {
    
    /**
     * <p>Default constructor</p>
     */
    public function __construct() {
        
    }
    
    /**
     * <p>Checks if the given user has the permission over the node</p>
     * @param int $userId the user name
     * @param int $nodeId the node id
     * @param string $permission the permission
     * 
     * @return a boolean indicating if the user has permission over the node
     */
    public function hasPermissionOnNode($username, $nodeid, $permission = "View all nodes") {
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
}

?>