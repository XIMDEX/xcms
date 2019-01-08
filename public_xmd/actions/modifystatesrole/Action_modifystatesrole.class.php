<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\WorkflowStatus;
use Ximdex\Models\Role;
use Ximdex\Models\Node;
use Ximdex\Models\Workflow;
use Ximdex\MVC\ActionAbstract;

class Action_modifystatesrole extends ActionAbstract
{
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $role = new Role($idNode);
        $idRoleStates = $role->GetAllStates();
        $workflow = new Workflow();
        try {
            $workflow->loadMaster();
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        $idAllStates = $workflow->GetAllStates();
        $states = [];
        foreach ($idAllStates as $idStatus) {
            $wStatus = new WorkflowStatus($idStatus);
            $states[] = array('id' => $idStatus, 'name' => $wStatus->get('name'));
        }
        foreach ($states as $i => $state) {
            if ($state['id'] != null && is_array($idRoleStates) && in_array($state['id'], $idRoleStates)) {
                $states[$i]['asociated'] = true;
            } else {
                $states[$i]['asociated'] = false;
            }
        }
        $node = new Node($idNode);
        $values = array(
            'all_states' => json_encode($states),
            'node_Type' => $node->nodeType->GetName(),
            'idRole' => $idNode);
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update_states()
    {   
        $post = file_get_contents('php://input');
        $request = json_decode($post, true);
        $states = $request['states'];
        $idRole = $request['idRole'];
        $role = new Role($idRole);
        foreach ($states as $state) {
            if ($state['asociated'] && $role->hasState($state['id']) == 0) {
                $role->AddState($state['id']);
            } elseif (!$state['asociated'] && $role->hasState($state['id']) > 0) {
                $role->deleteState($state['id']);
            }
        }
        $this->sendJSON(array('result' => 'ok', 'message' => _('The rol has been successfully updated')));
    }
}
