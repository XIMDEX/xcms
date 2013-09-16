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

/**
 * <p>API Search action</p>
 * <p>Handles requests to obtain and deal with node searching</p>
 */
class Action_search extends AbstractAPIAction implements SecuredAction {

    /**
     * <p>Default method for this action</p>
     * <p>search info of the node passed as parameter</p>
     * @param $request The current request
     * @param $response The response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $nodename = $request->getParam('name');

        if (!$this->checkParameters($request, $response)) {
            return;
        }

	$nodeInfo=array();
        $nodeInfo = $this->getNodeInfo($nodename);

        $this->responseBuilder->ok()->content($nodeInfo)->build();
    }

    private function checkParameters($request, $response) {
        $nodename = $request->getParam('name');
        $username = $request->getParam(self::USER_PARAM);

        // Is a valid user !
        $user = new User();
        $user->setByLogin($username);
        $user_id = $user->GetID();
        if ($user_id == null) {
            $this->createErrorResponse('Unknown user');
            return false;
        }

        if ($nodename == null || $nodename == false) {
            $this->createErrorResponse('The name parameter is missing');
            return false;
        }

        return true;
    }

    /**
     * <p>Gets the node info</p>
     * <p>It will return the following properties of the node:
     *  <ul>
     *      <li>Nodeid</li>
     *      <li>Name</li>
     *      <li>Icon</li>
     *      <li>Children</li>
     *  </ul>
     * </p>
     *
     * @param string $nodename the node name to get the information
     * @return array containing the node information
     */
    private function getNodeInfo($nodename) {
        $node = new Node($nodename);
	$nodeInfo = $node->GetByName($nodename);
	foreach($nodeInfo as $info){
		if(strcmp($info["Icon"],'action.png')!== 0){
			$res[]=$info;
		}

	}
error_log(print_r($res,true));
	return $res;
    }

}
