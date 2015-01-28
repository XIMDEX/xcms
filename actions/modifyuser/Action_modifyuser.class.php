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

ModulesManager::file('/inc/model/orm/RelUsersGroups_ORM.class.php');
ModulesManager::file('/inc/model/locale.php');

class Action_modifyuser extends ActionAbstract {
    // Main method: it shows init form
    function index () {
	    $idNode = $this->request->getParam('nodeid');
		$user = new User($idNode);

        $idRegisteredUser = \Ximdex\Utils\Session::get('userID');
        $registeredUser = new User($idRegisteredUser);

        $canModifyUserGroup = $registeredUser->isAllowedAction($idNode, 6004);

		$locale = new XimLocale();
		$locales = $locale->GetEnabledLocales();

        $allRole = new Role();
        $roles = $allRole->find('IdRole,Name');

        $role = new RelUsersGroups_ORM();
        $roleGeneral = $role->find('IdRole','IdUser=%s',array($idNode),MONO);
		$values = array(
			'go_method' => 'modifyuser',
			'login' => $user->get('Login'),
			'name' => $user->get('Name'),
			'email' => $user->get('Email'),
			'general_role' => $roleGeneral[0],
			'roles' => $roles,
			'user_locale' => $user->get('Locale'),
			'locales' => $locales,
			'messages' => $this->messages->messages,
            'canModifyUserGroup' => $canModifyUserGroup
            );

		$this->render($values, null, 'default-3.0.tpl');
    }

    function modifyuser() {
    	$idNode = $this->request->getParam('nodeid');
    	$name = trim($this->request->getParam('name'));
    	$email = trim($this->request->getParam('email'));
    	$password = trim($this->request->getParam('password_'));
    	$locale = trim($this->request->getParam('locale'));
        $general_role = $this->request->getParam('generalrole');

		$node = new Node($idNode);
	    $idUser = \Ximdex\Utils\Session::get('userID');
	    if(ModulesManager::isEnabled('ximDEMOS') && $idUser != $idNode && $idUser != 301){
	        $this->render($values, NULL, 'messages.tpl');
	    }

        $idRegisteredUser = \Ximdex\Utils\Session::get('userID');
        $registeredUser = new User($idRegisteredUser);

        $canModifyUserGroup = $registeredUser->isAllowedAction($idNode, 6004);

        $group = new Group();
        $group->SetID($group->GetGeneralGroup());
        $group->GetUserList();
        $roleOnNode = $group->GetRoleOnNode($idNode);
        if($canModifyUserGroup){
            $group->ChangeUserRole($idNode,$general_role);
        }elseif($roleOnNode != $general_role){
            $this->messages->add(_("You don't have enough permissions to modify the user role"), MSG_TYPE_WARNING);
        }

    	$user = new User($idNode);
    	$user->set('Name', $name);
    	$user->set('Email', $email);
		$user->set('Locale', $locale);
    	if (!empty($password)) {
    		$user->set('Pass', $password);
    	}

    	if ($res = $user->update() !== false) {
    		$this->messages->add(_('User has been successfully modified'), MSG_TYPE_NOTICE);
    	}

    	$this->messages->mergeMessages($user->messages);
		//$this->reloadNode( $node->get('IdParent') );
		$values = array('messages' => $this->messages->messages);
		$this->sendJSON($values);
    }
}
?>