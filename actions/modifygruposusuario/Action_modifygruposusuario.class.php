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




class Action_modifygruposusuario extends ActionAbstract {
   // Main method: shows initial form
    function index () {
		$idNode = $this->request->getParam('nodeid');
		$user = new User($idNode);

		$group = new Group();
		
		$generalRole = $user->GetRoleOnGroup($group->GetGeneralGroup());

		$rol = new Role();
		$roles = $rol->find('IdRole, Name');
		
		$userGroups = $user->GetGroupList();
		$excludedGroups = '';
		if (is_array($userGroups)) {
			$excludedGroups = implode(', ', $userGroups);
		}
	 
		if (empty($excludedGroups)) {
		 	$filteredGroups = $group->find('IdGroup, Name');
		} else {
		 	$filteredGroups = $group->find('IdGroup, Name', 'NOT IdGroup IN (%s)', 
		 		array($excludedGroups), MULTI, false);
		}
	 	
	 	$userGroupsWithRole = array();
	 	if (is_array($userGroups)) {
		 	$index = 0;
			foreach ($userGroups as $value) {
				if (!is_array($value) ||  !array_key_exists('IdGroup', $value) || $value['IdGroup'] != $group->GetGeneralGroup()) {
					$userGroupsWithRole[$index]['IdGroup'] = $value;
					$tmpGroup = new Group($value);
					$userGroupsWithRole[$index]['Name'] = $tmpGroup->get('Name');
					$userGroupsWithRole[$index]['IdRole'] = $user->GetRoleOnGroup($value);
					$index ++;
				}
			}
    	}		

    	$this->addJs('/actions/modifygruposusuario/javascript/helper.js');

		$values = array('id_node' => $idNode,
						'user_name' => $user->get('Name'),
						'general_role' => $generalRole,
						'all_roles' => $roles,
						'filtered_groups' => $filteredGroups,
						'user_groups_with_role' => $userGroupsWithRole);
		$this->render($values, null, 'default-3.0.tpl');
    }
    
    function suscribegroupuser() {
		$newrole = $this->request->getParam("newrole");
		$newgroup = $this->request->getParam("newgroup");
		$idUser = $this->request->getParam('nodeid');

		$grupo = new Group($newgroup);		//Create group object with appropriate ID
		$grupo->AddUserWithRole($idUser, $newrole);
		$this->redirectTo('index');
    }
    
    function updategroupuser() {
      $idusuario = $this->request->getParam('nodeid');
		$idGroups = $this->request->getParam("idGroups");
		$idRoleOld = $this->request->getParam("idRoleOld");
		$idRole = $this->request->getParam("idRole");
		$globalRole = $this->request->getParam("globalRole");
		$oldglobalRole = $this->request->getParam("oldglobalRole");
		
		$grupo = new Group();
		if($idGroups) {
			foreach ($idGroups as $idx => $gID) {
				if($idRole[$idx] != $idRoleOld[$idx]) {
					$grupo->SetID($gID);
					$grupo->ChangeUserRole($idusuario,$idRole[$idx]);
				}
			}
		}
		
		if ($globalRole != $oldglobalRole) {
			$grupo->SetID($grupo->GetGeneralGroup());
			$grupo->ChangeUserRole($idusuario,$globalRole);
		}
		$this->redirectTo('index');
    }
    
    function deletegroupuser() {
		$checked = $this->request->getParam("checked");
      $idusuario = $this->request->getParam('nodeid');
		
		foreach ($checked as $cked) {
			if($cked) {
				$grupo = new Group($cked);
				$grupo->DeleteUser($idusuario);
			}
		}
		$this->redirectTo('index');
    }
}
?>
