<?php
use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\Role;
use Ximdex\MVC\ActionAbstract;

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
class Action_modifygroupsnode extends ActionAbstract
{
    // Main method: shows initial form
    function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if (!($node->get('IdNode') > 0)) {
            $this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
            $this->render(array($this->messages), NULL, 'messages.tpl');
            die();
        }

        $group = new Group();
        $allGroups = $group->GetAllGroups();
        $nodeGroups = $node->GetGroupList();
        $groupsToShow = array_diff($allGroups, $nodeGroups);
        $newGroups = array();
        if (is_array($groupsToShow)) {
            foreach ($groupsToShow as $idGroup) {
                $group = new Group($idGroup);
                $newGroups[$group->get('IdGroup')] = $group->get('Name');
            }
        }

        $rol = new Role();
        $roles = $rol->find('IdRole, Name');

        $groupList = $node->GetGroupList();
        $strGroupList = implode(', ', $groupList);
        $group = new Group();
        $allGroups = $group->find('IdGroup, Name',
            'IdGroup in (%s) AND IdGroup <> %s',
            array($strGroupList, \App::getValue('GeneralGroup')),
            MULTI, false);

        if (is_array($allGroups)) {
            foreach ($allGroups as $key => $groupInfo) {
                $grupo = new Group($groupInfo['IdGroup']);
                $allGroups[$key]['IdRoleOnNode'] = $grupo->GetRoleOnNode($idNode);
            }
        }

        $this->addJs('/actions/modifygroupsnode/resources/js/helper.js');
        $values = array(
            'id_node' => $idNode,
            'node_name' => $node->get('Name'),
            'new_groups' => $newGroups,
            'all_groups' => $allGroups,
            'roles' => $roles,
            'go_method' => 'modifygroup',
            'name' => $node->GetNodeName()
        );

        $this->render($values, null, 'default-3.0.tpl');
    }

    function addgroupnode()
    {
        $idNode = $this->request->getParam('id_node');
        $idGroup = $this->request->getParam('id_group');
        $idRole = $this->request->getParam('id_role');
        $isRecursive = $this->request->getParam('is_recursive');

        $node = new Node($idNode);
        $node->AddGroupWithRole($idGroup, $idRole);

        if ($isRecursive) {
            $sectionList = $node->TraverseTree(3);
            foreach ($sectionList as $idSection) {
                $section = new Node($idSection);
                if ($section->HasGroup($idGroup)) {
                    $section->ChangeGroupRole($idGroup, $idRole);
                } else {
                    $section->AddGroupWithRole($idGroup, $idRole);
                }
            }
        }
        $this->redirectTo('index');
    }

    function updategroupnode()
    {
        $idNode = $this->request->getParam('id_node');
        $idGroups = $this->request->getParam('idGroups');
        $recursive = $this->request->getParam('recursive');
        $idRoleOld = $this->request->getParam('IdRoleOld');
        $idRole = $this->request->getParam('idRole');

        $node = new Node($idNode);
        $recursiveGroups = array();
        $recursiveRoles = array();

        if (is_array($idGroups)) {
            foreach ($idGroups as $id => $idGroup) {
                if (is_array($recursive) && in_array($idGroup, $recursive)) {
                    $recursiveGroups[] = $idGroup;
                    $recursiveRoles[] = $idRole[$id];
                }
                if ($idRole[$id] != $idRoleOld[$id]) {
                    if ($node->HasGroup($idGroup)) {
                        $node->ChangeGroupRole($idGroup, $idRole[$id]);
                    } else {
                        $node->AddGroupWithRole($idGroup, $idRole[$id]);
                    }
                }
            }
        }
        if (count($recursiveGroups) > 0) {
            $sectionList = $node->TraverseTree(3);
            foreach ($sectionList as $idSection) {
                $node = new Node($idSection);
                foreach ($recursiveGroups as $id => $idGroup) {
                    if ($node->HasGroup($idGroup)) {
                        $node->ChangeGroupRole($idGroup, $recursiveRoles[$id]);
                    } else {
                        $node->AddGroupWithRole($idGroup, $recursiveRoles[$id]);
                    }
                }
            }
        }
        $this->redirectTo('index');
    }

    function deletegroupnode()
    {
        $idNode = $this->request->getParam('id_node');
        $idGroups = $this->request->getParam('idGroups');
        $idGroupChecked = $this->request->getParam('id_group_checked');
        $recursive = $this->request->getParam('recursive');

        $node = new Node($idNode);
        $recursiveGroups = array();
        foreach ($idGroups as $idGroup) {
            if (is_array($idGroupChecked) && in_array($idGroup, $idGroupChecked)) {
                if (is_array($recursive) && in_array($idGroup, $recursive)) {
                    $recursiveGroups[] = $idGroup;
                }
                $node->DeleteGroup($idGroup);
            }
        }

        if (count($recursiveGroups) > 0) {
            $sectionList = $node->TraverseTree(3);
            foreach ($sectionList as $idSection) {
                $node = new Node($idSection);
                foreach ($recursiveGroups as $idGroup) {
                    $node->DeleteGroup($idGroup);
                }
            }
        }
        $this->redirectTo('index');
    }
}

?>