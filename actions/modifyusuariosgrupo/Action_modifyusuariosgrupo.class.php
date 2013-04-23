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








class Action_modifyusuariosgrupo extends ActionAbstract {
   // Main method: shows initial form
    function index () {

		$idNode = $this->request->getParam('nodeid');
		$node = new Node($idNode);

		$user = new User();
		$userList = $user->GetAllUsers();
		$grupo = new Group($idNode);
		$groupUsers = $grupo->GetUserList();


		$users = array();
		foreach ($userList as $idUser) {
			if (!in_array($idUser, $groupUsers)) {
				$user = new User($idUser);
				$users[$idUser] = sprintf('%s (%s)',$user->get('Name'), $user->get('Login'));
			}
		}

		$role = new Role();
		$roles = $role->find('IdRole, Name', '1 ORDER BY Name', NULL);

		$group = new Group($idNode);
		$userRoleInfo = $group->getUserRoleInfo();

		if (is_array($userRoleInfo)) {
			foreach ($userRoleInfo as $key => $info) {
				$user = new User($info['IdUser']);
				$userRoleInfo[$key]['UserName'] = $user->get('Name');
			}
		}


		$query = App::get('QueryManager');
		    	$this->addJs('/actions/modifyusuariosgrupo/javascript/helper.js');
			$values = array('name' => $node->get('Name'),
						'users' => $users,
						'idnode' => $idNode,
						'roles' => $roles,
						'user_infos' => $userRoleInfo,
						'nodeid' => $idNode,
						'action_add' => $query->getPage() . $query->buildWith(array('method' => 'addgroupuser')),
						'action_edit_delete' => $query->getPage() . $query->build() );

		$this->render($values, null, 'default-3.0.tpl');
    }

    function addgroupuser() {
    	$idNode = $this->request->getParam('nodeid');
    	$idUser = $this->request->getParam('id_user');
    	$idRole = $this->request->getParam('id_role');
		$group = new Group($idNode);
		$group->AddUserWithRole($idUser, $idRole);

		$this->redirectTo('index');
    }

    function editgroupuser() {


    	$idNode = $this->request->getParam('nodeid');
    	$users = $this->request->getParam('users');
    	$userForRole = $this->request->getParam('user_for_role');
    	$userRoles = $this->request->getParam('id_user_role');

    	$group = new Group($idNode);

       if (is_array($userRoles)) {
    		foreach ($userRoles as $key => $idRole) {
    			$group->ChangeUserRole($userForRole[$key], $idRole);
    		}
    	}
    	  $this->redirectTo('index');
    }

    function deletegroupuser() {


    	$idNode = $this->request->getParam('nodeid');
    	$users = $this->request->getParam('users');
    	$userForRole = $this->request->getParam('user_for_role');
    	$userRoles = $this->request->getParam('id_user_role');

    	$group = new Group($idNode);

    	if (is_array($users)) {
    		foreach ($users as $key => $idUser) {
    			$group->DeleteUser($idUser);
    			unset($userRoles[$key]);
    		}
    	}


    	$this->redirectTo('index');

    }
}
?>
