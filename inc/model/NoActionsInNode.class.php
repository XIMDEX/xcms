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

if (!defined('XIMDEX_ROOT_PATH')) {
    define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../');
}
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/NoActionsInNode_ORM.class.php';

class NoActionsInNode extends NoActionsInNode_ORM {

    /**
     * Check if an action is disabled for a node
     * @param int $idNode 
     * @param int $idAction 
     * @return boolean true if exists row in NoActionsInNode table for the params values.
     * @since Ximdex 3.6
     */
    public function isActionForbiddenForNode($idNode, $idAction){
        $noAllowedActions = $this->getForbiddenActions($idNode);
        
        if (!$noAllowedActions || !is_array($noAllowedActions)){
            return false;
        }
        
        if(!in_array($idAction, $noAllowedActions)){
            return false;
        }
        return true;
    }
    
    /**
     * Get an array of the actions in NoActionsInNode table for a idnode
     * @param int $idNode
     * @return array idActions Array
     * @since Ximdex 3.6
     */
    public function getForbiddenActions($idNode){
        $arrayForbiddenActions = $this->find("IdAction","IdNode=%s",array($idNode),MONO);
        return $arrayForbiddenActions? $arrayForbiddenActions: array();
    }
}
?>