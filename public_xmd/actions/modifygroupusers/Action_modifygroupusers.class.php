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

use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\Role;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;

class Action_modifygroupusers extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $user = new User();
        $userList = $user->GetAllUsers();
        $group = new Group($idNode);
        $groupUsers = $group->GetUserList();
        $role = new Role();
        $roles = $role->find('IdRole, Name', '1 ORDER BY Name', NULL);
        $rolesToSend = array();
        foreach ($roles as $r) {
            $rolesToSend[$r['IdRole']] = $r['Name'];
        }
        $userRoleInfo = $group->getUserRoleInfo();
        $userRI = array();
        if (is_array($userRoleInfo)) {
            foreach ($userRoleInfo as $key => $info) {
                $user = new User($info['IdUser']);
                $userRoleInfo[$key]['UserName'] = $user->get('Login');
                $userRoleInfo[$key]['dirty'] = false;
                $userRI[] = $userRoleInfo[$key];
                if ($k = array_search($info['IdUser'], $userList)) {
                    array_splice($userList, $k, 1);
                }
            }
        }
        $users = array();
        foreach ($userList as $idUser) {
            if (! in_array($idUser, $groupUsers)) {
                $user = new User($idUser);
                $u = [];
                $u['id'] = $idUser;
                $u['name'] = $user->get('Login');
                $users[] = $u;
            }
        }
        $this->addJs('/actions/modifygroupusers/resources/js/helper.js');
        $values = array('name' => $node->get('Name'),
            'users_not_associated' => json_encode($users),
            'idnode' => $idNode,
            'roles' => json_encode($rolesToSend),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'users_associated' => json_encode($userRI)
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function addGroupUser()
    {
        $idNode = $this->request->getParam('nodeid');
        $idUser = $this->request->getParam('id_user');
        $idRole = $this->request->getParam('id_role');
        $group = new Group($idNode);
        if (! $group->getID()) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        if (! $group->addUserWithRole($idUser, $idRole)) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        $values = array('result' => 'ok');
        $this->sendJSON($values);
    }

    public function editGroupUser()
    {
        $idNode = $this->request->getParam('nodeid');
        $user = $this->request->getParam('user');
        $role = $this->request->getParam('role');
        $group = new Group($idNode);
        if (! $group->getID()) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        if (! $group->changeUserRole($user, $role)) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        $values = array('result' => 'ok');
        $this->sendJSON($values);
    }

    public function deleteGroupUser()
    {
        $idNode = $this->request->getParam('nodeid');
        $user = $this->request->getParam('user');
        $group = new Group($idNode);
        if (! $group->getID()) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        if (! $group->deleteUser($user)) {
            $values = array('result' => 'notok');
            $this->sendJSON($values);
        }
        $values = array('result' => 'ok');
        $this->sendJSON($values);
    }
}
