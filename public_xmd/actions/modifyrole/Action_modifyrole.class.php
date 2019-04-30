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

use Ximdex\Models\Action;
use Ximdex\Models\NodeType;
use Ximdex\Models\Permission;
use Ximdex\Models\Role;
use Ximdex\Models\Node;
use Ximdex\Models\Workflow;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Modules\Manager;
use Ximdex\Models\WorkflowStatus;

class Action_modifyrole extends ActionAbstract
{
    /**
     * Main method. Shows initial form
     */
    public function index()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $role = new Role($idNode);
        if (! $role->getID()) {
            $this->messages->add(_('The role does not exists'), MSG_TYPE_WARNING);
            $this->sendJSON(['messages' => $this->messages->messages]);
            return;
        }
        
        // Getting permisions for current role
        $permission = new Permission();
        $allPermissionData = $permission->find();
        foreach ($allPermissionData as $key => $permissionData) {
            $allPermissionData[$key]['HasPermission'] = $role->hasPermission($permissionData['IdPermission']);
        }
        $node = new Node($idNode);
        $allStates = [];
        $this->addJs('/actions/modifyrole/js/modifyrole.js');
        $this->addCss('/actions/modifyrole/css/modifyrole.css');
        $values = [
            'name' => $role->get('Name'),
            'description' => $role->get('Description'),
            'permissions' => $allPermissionData,
            'nodetypes' => $this->getAllNodeTypes($role, $allStates),
            'workflow_states' => $allStates,
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
            'go_method' => 'modifyrole'
        ];
        $this->render($values, null, 'default-3.0.tpl');
    }

    /**
     * Gets the permissions of each nodetype
     */
    public function modifyrole()
    {
        // ini_set('max_input_vars', 2000);
        $idNode = (int) $this->request->getParam('nodeid');
        $role = new Role($idNode);
        if (! $role->getID()) {
            $this->messages->add(_('The role does not exists'), MSG_TYPE_WARNING);
            $this->sendJSON(['messages' => $this->messages->messages]);
        }
        $role->set('Description', $this->request->getParam('description'));
        $role->update();
        if (! $role->deleteAllPermissions()) {
            $this->messages->add(_('Cannot delete old role permissions'), MSG_TYPE_ERROR);
            $values = array(
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        $permissions = $this->request->getParam('permissions');
        if ($permissions) {
            foreach (array_keys($permissions) as $idPermission) {
                if ($role->addPermission($idPermission) === false) {
                    $this->messages->add(_('Cannot create a role permission'), MSG_TYPE_ERROR);
                    $values = array(
                        'messages' => $this->messages->messages
                    );
                    $this->sendJSON($values);
                }
            }
        }
        if (! $role->deleteAllRolesActions()) {
            $this->messages->add(_('Cannot delete old role actions'), MSG_TYPE_ERROR);
            $values = array(
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        $rolesActions = $this->request->getParam('action_workflow');
        if ($rolesActions) {
            foreach ($rolesActions as $idAction => $workFlowStatus) {
                foreach ($workFlowStatus as $workflowStatusId => $actived) {
                    if (! $actived) {
                        continue;
                    }
                    if ($role->addAction($idAction, ($workflowStatusId == 'NO_STATE') ? null : $workflowStatusId) === false) {
                        $this->messages->add(_('Cannot create a role action'), MSG_TYPE_ERROR);
                        $values = array(
                            'messages' => $this->messages->messages
                        );
                        $this->sendJSON($values);
                    }
                }
            }
        }
        $this->messages->add(_('Changes have been successfully performed.'), MSG_TYPE_NOTICE);
        $values = array(
            'goback' => true,
            'messages' => $this->messages->messages
        );
        $this->sendJSON($values);
    }
    
    private function getAllNodeTypes(Role $role, array & $allStates) : array
    {
        $nodeType = new NodeType();
        $allNodeTypes = $nodeType->find('IdNodeType, Description, IsPublishable, Module, workflowId', null, null, MULTI, true, null
            , 'Description');
        $respAllNodeTypes = [];
        $action = new Action();
        foreach ($allNodeTypes as $nodeType) {
            
            // Skipping permissions for actions in disabled modules
            if (! empty($nodeType['Module']) && ! Manager::isEnabled($nodeType['Module'])) {
                continue;
            }
            
            // Getting actions for current node type
            $nodeType['actions'] = $action->find('IdAction, Name, Module, Command, Sort', 'IdNodeType = %s', [$nodeType['IdNodeType']], MULTI
                , true, null, 'Sort');
            if ($nodeType['actions'] === false) {
                throw new Exception('Error loading node type actions');
            }
            foreach ($nodeType['actions'] as & $actionInfo) {
                
                // Skipping states for a disabled module action
                if (! empty($actionInfo['Module']) && ! Manager::isEnabled($actionInfo['Module'])) {
                    continue;
                }
                if ($nodeType['workflowId']) {
                    
                    // This node type works with a workflow
                    if (! isset($allStates[$nodeType['workflowId']])) {
                        $workflow = new Workflow($nodeType['workflowId']);
                        foreach ($workflow->getAllStates() as $id) {
                            $state = new WorkflowStatus($id);
                            $allStates[$nodeType['workflowId']][] = ['id' => $id, 'name' => $state->get('name')];
                        }
                    }
                    foreach ($allStates[$nodeType['workflowId']] as $state) {
                        $actionInfo['states'][$state['id']] = $role->hasAction($actionInfo['IdAction'], $state['id'], $workflow->get('id'));
                    }
                } else {
                    $actionInfo['state'] = $role->hasAction($actionInfo['IdAction']);
                }
            }
            $respAllNodeTypes[] = $nodeType;
        }
        return $respAllNodeTypes;
    }
}
