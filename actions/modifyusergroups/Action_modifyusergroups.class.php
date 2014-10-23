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

class Action_modifyusergroups extends ActionAbstract {

    // Main method: shows initial form
    function index () {
        $idNode = $this->request->getParam('nodeid');

        $this->addJs('/actions/modifyusergroups/resources/js/helper.js');

        $values = array('id_node' => $idNode);

        $this->render($values, null, 'default-3.0.tpl');
    }

    function getGroups(){
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
                    $userGroupsWithRole[$index]['dirty'] = false;
                    $index ++;
                }
            }
        }
        $values = array('id_node' => $idNode,
            'user_name' => $user->get('Name'),
            'general_role' => $generalRole,
            'all_roles' => $roles,
            'filtered_groups' => $filteredGroups,
            'user_groups_with_role' => $userGroupsWithRole);
        $this->sendJSON($values);
    }

    function suscribegroupuser() {
        $newrole = $this->request->getParam("newrole");
        $newgroup = $this->request->getParam("newgroup");
        $idUser = $this->request->getParam('nodeid');

        $group = new Group($newgroup);
        $group->AddUserWithRole($idUser, $newrole);

        $values = array(
            'result' => "OK",
            'message' => "The association was created successfully"
        );

        $this->sendJSON($values);
    }

    function updategroupuser() {
        $iduser = $this->request->getParam('nodeid');
        $idGroup = $this->request->getParam("group");
        //$idRoleOld = $this->request->getParam("roleOld");
        $idRole = $this->request->getParam("role");
        /*$globalRole = $this->request->getParam("globalRole");
        $oldglobalRole = $this->request->getParam("oldglobalRole");*/

        $group = new Group();
        $group->SetID($idGroup);
        $userRoles=$group->getUserRoleInfo();
        $exist=false;
        foreach($userRoles as $u){
            if($u["IdUser"]==$iduser && $u["IdRole"]==$idRole){
                $exist=true;
                break;
            }
        }
        if(!$exist) {
            $group->ChangeUserRole($iduser,$idRole);
            $values = array(
                'result' => "OK",
                'message' => "The association has been successfully updated"
            );
        }else{
            $values = array(
                'result' => "FAIL"
            );
        }
        $this->sendJSON($values);
    }

    function deletegroupuser() {
        $group = $this->request->getParam("group");
        $iduser = $this->request->getParam('nodeid');

        if($group){
            $group = new Group($group);
            $group->DeleteUser($iduser);
            $values = array(
                'result' => "OK",
                'message' => "The association has been successfully deleted"
            );
        }
        $this->sendJSON($values);
    }
}

?>