<?php

/* * ****************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2013  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 * **************************************************************************** */

require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/persistence/Config.class.php');
require_once(XIMDEX_ROOT_PATH . '/conf/stats.conf');
ModulesManager::file('/inc/i18n/I18N.class.php');

/**
 * <p>API NodeType action</p>
 * <p>Handles requests to obtain the nodetypes</p>
 */
class Action_nodetype extends AbstractAPIAction implements SecuredAction {

    /**
     * <p>Default method for this action</p>
     * <p>Gets all registered nodetypes or a specific nodetype</p>
     * @param Request The current request
     * @param Response The Response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $nodeTypeId = $request->getParam("nodetypeid");
        if($nodeTypeId == null || $nodeTypeId == "") {
            $nodeTypes = $this->getNodeTypeInfo();
        }
        else {
            $nodeTypeIdInt = intval($nodeTypeId);
            if($nodeTypeIdInt == 0) {
                $this->createErrorResponse("Bad identifier supplied");
                return;
            }
            
            $nodeTypes = $this->getNodeTypeInfo($nodeTypeIdInt);
        }

        if(empty($nodeTypes)) {
            $this->createErrorResponse("No nodetypes found");
            return;
        }
        
        $this->responseBuilder->ok()->content($nodeTypes)->build();
    }
    
    /**
     * <p>Gets the registered nodetypes or a specific nodetype if a nodeType id is given</p>
     * @param int $nodeType The nodeType id
     * @return array containing the requested nodetypes
     */
    private function getNodeTypeInfo($nodeType = null) {
        $where = $nodeType == null || $nodeType == "" ? "" : " WHERE n.IdNodeType = ".$nodeType;
        $sql = "SELECT n.IdNodeType, n.Name, n.Description, r.mimeString from NodeTypes n join RelNodeTypeMimeType r on(n.IdNodeType=r.IdNodeType)".$where;
        $dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->createErrorResponse('An error ocurred while processing');
            return;
        }

        $nodeTypes = array();
        while (!$dbObj->EOF) {
            $nodeTypes[] = array('idnodetype' => $dbObj->getValue("IdNodeType"),
                'name' => $dbObj->getValue("Name"),
                'description' => $dbObj->getValue("Description"),
                'mimetype' => $dbObj->getValue("mimeString")
            );
            $dbObj->Next();
        }
        
        return $nodeTypes;
    }

}