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
ModulesManager::file('/inc/pipeline/PipeStatus.class.php');

class Action_modifystatesrole extends ActionAbstract {
	
    function index () {
    	$idNode = $this->request->getParam('nodeid');
		$role = new Role($idNode);
		$idRoleStates = $role->GetAllStates();

		$asociatedStates = Array();
		$notAsociatedStates = Array();

		$workflow = new WorkFlow(NULL, NULL, Config::getValue('IdDefaultWorkflow'));
		$idAllStates = $workflow->GetAllStates();
		foreach ($idAllStates as $idStatus) {
			$pipeStatus = new PipeStatus($idStatus);
			$states[$idStatus] = $pipeStatus->get('Name');
		}

		foreach ($states as $idState => $name) {
			if ($idState != null && is_array($idRoleStates) && in_array($idState, $idRoleStates)) {
				$asociatedStates[$idState] = $name;
			} else {
				$notAsociatedStates[$idState] = $name;
			}
		}
		
        $query = App::get('QueryManager');
        $addState = $query->getPage() . $query->buildWith(array('method' => 'add_state'));
        $deleteState = $query->getPage() . $query->buildWith(array('method' => 'delete_state'));

		$values = array('all_states' => $notAsociatedStates,
						'role_states' => $asociatedStates,
						'action_add' => $addState,
						'action_delete' => $deleteState);
						
		$this->render($values, null, 'default-3.0.tpl');
    }
    
    function add_state() {
    	$idNode = $this->request->getParam('nodeid');
    	$idState = $this->request->getParam('id_state');
		
    	$role=new Role($idNode);
		$role->AddState($idState);
    	
		$this->redirectTo('index');
    }
    
    function delete_state() {
    	$idNode = $this->request->getParam('nodeid');
    	$states = $this->request->getParam('states');
		$role = new Role($idNode);
		if(is_array($states)) {
			foreach ($states as $idState) {
				$role->DeleteState($idState);
			}
		}
    	$this->redirectTo('index');
    }

}
?>
