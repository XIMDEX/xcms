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
ModulesManager::file('/inc/mail/Mail.class.php');
ModulesManager::file('/inc/model/role.inc');

class Action_workflow_backward extends ActionAbstract {

	// Main method: shows the initial form
    	function index () {
 		$idNode = (int) $this->request->getParam("nodeid");

		$idUser = XSession::get('userID');
                $user = new User($idUser);

		//Getting list of groups where user is added: to show it in select input
                $group = new Group();
                $groupList=$group->find('IdGroup', NULL, NULL, MONO);
                $groupState=$this->_getStateForGroups($idNode, $groupList);

                //Getting user roles on current node
                $userRoles=$user->GetRolesOnNode($idNode);

		$node= new Node($idNode);
		$workflow=new WorkFlow($idNode, $node->GetState());

                //Getting previous state
                $prevState = $workflow->GetPreviousState();
		$workflowPrev = new WorkFlow($idNode,$prevState);
		$prevStateName=$workflowPrev->GetName(); 

		//Checking if the user has some role with permission to change to next State
                $allowed=FALSE;
                foreach($userRoles as $userRole => $myIdRole) {
                        $role = new Role($myIdRole);
                        if($role->HasState($prevState)) {
                                $allowed=TRUE;
                                break;
                        }
                }

                if(!$allowed) {
                        $this->messages->add(_('You have not privileges to move forward the node to next status.'), MSG_TYPE_WARNING);
                        $this->messages->add(_('You have not assigned a role with privileges to modify workflow status on any of groups associated with the node or the section which contains it.'), MSG_TYPE_WARNING);

                        $values = array(
                                'messages' => $this->messages->messages
                        );

                        $this->render($values, 'show_results', 'default-3.0.tpl');

                        return ;
                }

		$values = array(
                                'idnode' => $idNode,
                                'go_method' => 'workflow_backward',
				'prevStateName' => $prevStateName,
				'currentStateName' => $workflow->GetName()
                        );

		if($workflow->IsInitialState()) {
			$this->messages->add(_('The document is already in its initial state. A previous state cannot be stablished.'), MSG_TYPE_ERROR);
			$values['messages'] = $this->messages->messages;
			$this->render($values, null);
		}else {
			$this->render($values, null, 'default-3.0.tpl');
		}
	}

	function workflow_backward() {

		$idNode = $this->request->getParam('nodeid');
                $node = new Node($idNode);
                $workflow = new WorkFlow($idNode, $node->GetState());
                $prevState = $workflow->GetPreviousState();
                $node->setState($prevState);

		$this->render(NULL, 'success.tpl', 'default-3.0.tpl');

	}
	
	private function _getStateForGroups($idNode, $groupList) {
                $node = new Node($idNode);
                $groupState = array();
                if (is_array($groupList) && !empty($groupList)) {
                        foreach ($groupList as $idGroup) {
                                $group = new Group($idGroup);
                                $users = $group->GetUserList();
                                if (is_array($users) && !empty($users)) {
                                        foreach ($users as $idUser) {
                                                $nextState = $node->GetNextAllowedState($idUser, $idGroup);
                                                if ($nextState > 0) {
                                                        $groupState[$idGroup] = $nextState;
                                                }
                                        }
                                }
                        }
                }
                return $groupState;

        }
}
?>
