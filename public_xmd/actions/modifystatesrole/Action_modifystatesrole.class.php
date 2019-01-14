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
        $workflows = $workflow->findAll();
        $workflows[] = ['id' => null, 'description' => 'Common states'];
        foreach ($workflows as & $wfData) {
            $workflow = new Workflow($wfData['id']);
            $idAllStates = $workflow->GetAllStates(! $wfData['id']);
            $states = [];
            foreach ($idAllStates as $idStatus) {
                $wStatus = new WorkflowStatus($idStatus);
                if (is_array($idRoleStates) && in_array($idStatus, $idRoleStates)) {
                    $asociated = true;
                } else {
                    $asociated = false;
                }
                $states[] = array('id' => $idStatus, 'name' => $wStatus->get('name'), 'asociated' => $asociated);
            }
            $wfData['states'] = $states;
        }
        $node = new Node($idNode);
        $values = array(
            'workflows' => json_encode($workflows),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'idRole' => $idNode);
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update_states()
    {   
        $post = file_get_contents('php://input');
        $request = json_decode($post, true);
        $workflows = $request['workflows'];
        $idRole = $request['idRole'];
        $role = new Role($idRole);
        foreach ($workflows as $workflow) {
            foreach ($workflow['states'] as $state) {
                if ($state['asociated'] && ! $role->hasState($state['id'])) {
                    $role->addState($state['id']);
                } elseif (! $state['asociated'] && $role->hasState($state['id'])) {
                    $role->deleteState($state['id']);
                }
            }
        }
        $this->sendJSON(array('result' => 'ok', 'message' => _('The rol has been successfully updated')));
    }
}
