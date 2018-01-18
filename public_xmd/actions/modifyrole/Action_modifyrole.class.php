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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */
use Ximdex\Models\Action;
use Ximdex\Models\NodeType;
use Ximdex\Models\Permission;
use Ximdex\Models\Pipeline;
use Ximdex\Models\PipeStatus;
use Ximdex\Models\Role;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Workflow\WorkFlow;


class Action_modifyrole extends ActionAbstract
{

    /**
     * Main method. Shows initial form.
     */
    public function index()
    {

        $idNode = $this->request->getParam('nodeid');
        $role = new Role($idNode);

        //Getting permisions for current role.
        $permission = new Permission();
        $allPermissionData = $permission->find();
        foreach ($allPermissionData as $key => $permissionData) {
            $allPermissionData[$key]['HasPermission'] = $role->HasPermission($permissionData['IdPermission']);
        }

        //Gets all the states for the default workflow.
        //The selected pipeline to show the states will be the default workflow.
        //Usually it is the Workflow master.
        $selectedPipeline = $this->request->getParam('id_pipeline');
        if (!($selectedPipeline > 0)) {
            $selectedPipeline = App::getValue('IdDefaultWorkflow');
            $pipeline = new Pipeline();
            $pipeline->loadByIdNode($selectedPipeline);
            $selectedPipeline = $pipeline->get('id');
        }

        $workflow = new WorkFlow(NULL, NULL, $selectedPipeline);
        $pipeProcess = $workflow->pipeProcess;
        $allIdNodeStates = $pipeProcess->getAllStatus();

        $allStates = array();
        foreach ($allIdNodeStates as $idPipeStatus) {
            $pipeStatus = new PipeStatus($idPipeStatus);
            $allStates[] = array('IdState' => $idPipeStatus, 'Name' => $pipeStatus->get('Name'));
        }
        
        $sql = 'select id, Pipeline from Pipelines where IdNode > 0 order by id asc limit 1';
        $db = new \Ximdex\Runtime\Db();
        $db->query($sql);

        $pipelines = array($db->getValue('id') => $db->getValue('Pipeline'));

        $this->addJs('/actions/modifyrole/js/modifyrole.js');
        $this->addCss('/actions/modifyrole/css/modifyrole.css');

        $values = array('name' => $role->get('Name'),
            'description' => $role->get('Description'),
            'permissions' => $allPermissionData,
            'nodetypes' => $this->getAllNodeTypes($allStates, $role, $selectedPipeline),
            'workflow_states' => $allStates,
            'pipelines' => $pipelines,
            'selected_pipeline' => $selectedPipeline,
            'go_method' => 'modifyrole'
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    protected function getAllNodeTypes($allStates, $role, $selectedPipeline)
    {

        for ($i = \Ximdex\NodeTypes\NodeTypeConstants::USER_MANAGER; $i < \Ximdex\NodeTypes\NodeTypeConstants::PROJECTS; $i++) {
            $groupeds[$i] = _("Control center permissions");
        }

        $nodeType = new NodeType();
        $allNodeTypes = $nodeType->find('IdNodeType, Description, IsPublishable, Module');
        reset($allNodeTypes);
        $respAllNodeTypes = [];
        foreach($allNodeTypes as $i => $nodeType){

            //Skipping permissions for actions in disabled modules.
            if (!empty($nodeType['Module']) &&
                !\Ximdex\Modules\Manager::isEnabled($allNodeTypes[$i]['Module'])
            ) {
                continue;
            }

            $action = new Action();
            $nodeType['actions'] = $action->find('IdAction, Name, Module, Command',
                'IdNodeType = %s', array($nodeType['IdNodeType']));
            if (is_array($nodeType['actions'])) {
                foreach ($nodeType['actions'] as $actionKey => $actionInfo) {

                    if (!empty($actionInfo['Module']) &&
                        !\Ximdex\Modules\Manager::isEnabled($actionInfo['Module'])
                    ) {
                        unset($nodeType['actions'][$actionKey]);
                        continue;
                    }

                    if ($nodeType['IsPublishable'] > 0) {
                        foreach ($allStates as $stateInfo) {
                            $nodeType['actions'][$actionKey]['states'][$stateInfo['IdState']] = $role->HasAction(
                                $actionInfo['IdAction'], $stateInfo['IdState'], $selectedPipeline
                            );
                        }
                    } else {
                        $nodeType['actions'][$actionKey]['state'] = $role->HasAction($actionInfo['IdAction'], NULL, $selectedPipeline);
                    }

                }
            }
            $respAllNodeTypes[] = $nodeType;
        }
        return $respAllNodeTypes;
    }

    /*
     * Gets the permissions of each nodetype
     */

    function modifyrole()
    {
        $idNode = $this->request->getParam('nodeid');
        $idPipeline = $this->request->getParam('id_pipeline');
        $role = new Role($idNode);
        $role->set('Description', $this->request->getParam('description'));
        $role->update();
        $role->DeleteAllPermissions();
        $role->deleteAllRolesActions($idPipeline);

        $permissions = $this->request->getParam('permissions');
        if ($permissions) {
            foreach ($permissions as $idPermission => $value) {
                $role->AddPermission($idPermission);
            }
        }

        $rolesActions = $this->request->getParam('action_workflow');
        if (count($rolesActions) >= 1) {
            foreach ($rolesActions as $idAction => $workFlowStatus) {
                foreach ($workFlowStatus as $idWorkflowStatus => $value) {
                    $role->AddAction($idAction, (int)$idWorkflowStatus, $idPipeline);
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
}