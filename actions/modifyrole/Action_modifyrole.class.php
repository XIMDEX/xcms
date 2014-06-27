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

ModulesManager::file('/inc/workflow/Workflow.class.php');

class Action_modifyrole extends ActionAbstract {
   
	/** 
	* Main method. Shows initial form.
	*/
	public function index () {

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
			$selectedPipeline = Config::getValue('IdDefaultWorkflow');
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

		$action = new Node();
		$allActions = $action->find("IdAction, Command, IdNodeType, Module, Name");
		$permisionsToEdit = array();
		foreach ($allActions as $key => $actionInfo){

			$idAction = $actionInfo["IdAction"];
			$idNodeType = $actionInfo["IdNodeType"];
			$command = $actionInfo["Command"];
			$nodeType = new NodeType($idNodeType);
			if (!empty($actionInfo["Module"]) && !ModulesManager::isEnabled($actionInfo["Module"]))
				continue;
			if (!$nodeType->get("Module") && !ModulesManager::isEnabled($nodeType->get("Module"))){
				unset($allActions[$key]);
				continue;
			}
			$hasAction = array();
			if ($nodeType->get("IsPublicable")>0){
				foreach ($allStates as $stateInfo){
					$idState = $stateInfo['IdState'];
					$hasAction[$idAction] = $role->HasAction($idAction, $stateInfo['IdState'], $selectedPipeline);
					$permisionsToEdit[$command][$idState][$idNodeType] = $hasAction;
				}				
			}else{
				$hasAction[$idAction] = $role->HasAction($idAction, NULL, $selectedPipeline);
				$permisionsToEdit[$command]["none"][$idNodeType] = $hasAction;
			}
			

		}
		$nodeType = new NodeType();
		$allNodeTypes = $nodeType->find('IdNodeType, Description, IsPublicable, Module');
		reset($allNodeTypes);
		foreach ($allNodeTypes as $key => $nodeTypeInfo){

			//Skipping permissions for actions in disabled modules.
			if (!empty($nodeTypeInfo['Module']) &&
				!ModulesManager::isEnabled($nodeTypeInfo['Module'])) {
				unset($allNodeTypes[$key]);
				continue;
			}

			$action = new Action();
			$allNodeTypes[$key]['actions'] = $action->find('IdAction, Name, Module, Command',
				'IdNodeType = %s', array($nodeTypeInfo['IdNodeType']));
			if (is_array($allNodeTypes[$key]['actions'])) {
				foreach ($allNodeTypes[$key]['actions'] as $actionKey => $actionInfo) {

					if (!empty($actionInfo['Module']) &&
						!ModulesManager::isEnabled($actionInfo['Module'])) {
						unset($allNodeTypes[$key]['actions'][$actionKey]);
						continue;
					}

					if ($nodeTypeInfo['IsPublicable'] > 0) {
						foreach ($allStates as $stateInfo) {
							$allNodeTypes[$key]['actions'][$actionKey]['states'][$stateInfo['IdState']] = $role->HasAction(
									                            $actionInfo['IdAction'], $stateInfo['IdState'], $selectedPipeline
									                            );
						}
					} else {
						$allNodeTypes[$key]['actions'][$actionKey]['state'] = $role->HasAction($actionInfo['IdAction'], NULL, $selectedPipeline);
					}

				}
			}
		}
		$sql = 'select id, Pipeline from Pipelines where IdNode > 0 order by id asc limit 1';
		$db = new DB();
		$db->query($sql);

		$pipelines = array($db->getValue('id') => $db->getValue('Pipeline'));

		$this->addJs('/actions/modifyrole/js/modifyrole.js');

		$values = array('name' => $role->get('Name'),
						'description' => $role->get('Description'),
						'permissions' => $allPermissionData,
						'actions' => $permisionsToEdit,
                        'nodetypes' => $allNodeTypes,
						'workflow_states' => $allStates,
						'pipelines' => $pipelines,
						'selected_pipeline' => $selectedPipeline,
						'go_method' => 'modifyrole'
		);
		error_log(print_r($allNodeTypes, true));
		$this->render($values, null, 'default-3.0.tpl');
    }

	function modifyrole() {
		$idNode = $this->request->getParam('nodeid');

		//If ximDEMOS is actived and nodeis is rol "Demo" then no modify is allowed
		if(ModulesManager::isEnabled("ximDEMOS") && XSession::get('user_demo')) {
			$node = new Node($idNode);
			$name = $node->get("Name");
			if("Demo" == $name ) {
				$this->messages->add(_('Changes in Demo role are not allowed'), MSG_TYPE_NOTICE);

				$values = array(
					'goback' => true,
					'messages' => $this->messages->messages
				);
				$this->render($values);

				return ;
			}
		}

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
		if(count($rolesActions)>=1) {
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
		//$this->render($values);
		$this->sendJSON($values);
	}
}
?>
