<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\Group;
use Ximdex\Models\Role;
use Ximdex\Models\User;
use Ximdex\Models\XimLocale;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\Node;
use Ximdex\Models\Action;

class Action_modifyuser extends ActionAbstract
{    
    /**
     * Main method: it shows init form
     */
    public function index()
    {
	    $idNode = (int) $this->request->getParam('nodeid');
		$user = new User($idNode);
		if (! $user->getID()) {
		    $this->messages->add(_('User') . " {$idNode} " . _('does not exists'), MSG_TYPE_WARNING);
		    $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
		    return;
		}
		$folder = new Node($idNode);
        $idRegisteredUser = \Ximdex\Runtime\Session::get('userID');
        $registeredUser = new User($idRegisteredUser);
        $canModifyUserGroup = $registeredUser->isAllowedAction(Group::ID_GENERAL, Action::MODIFY_GROUP_USERS);
		$locale = new XimLocale();
		$locales = $locale->getEnabledLocales();
        $role = new Role();
        $roles = $role->find('IdRole, Name');
        $roleGeneral = $user->getRoleOnGroup(Group::getGeneralGroup());
		$values = array(
			'go_method' => 'modifyuser',
			'login' => $user->get('Login'),
			'name' => $user->get('Name'),
			'email' => $user->get('Email'),
			'general_role' => $roleGeneral,
			'roles' => $roles,
			'user_locale' => $user->get('Locale'),
			'locales' => $locales,
			'messages' => $this->messages->messages,
		    'nodeTypeID' => $folder->nodeType->getID(),
		    'node_Type' => $folder->nodeType->getName(),
            'canModifyUserGroup' => $canModifyUserGroup
		);
		$this->render($values, null, 'default-3.0.tpl');
    }

    public function modifyuser()
    {
    	$idNode = $this->request->getParam('nodeid');
    	$name = trim($this->request->getParam('name'));
    	$email = trim($this->request->getParam('email'));
    	$password = trim($this->request->getParam('password_'));
    	$password_repeated = trim($this->request->getParam('password_repeated'));
    	if ($password != $password_repeated) {
    	    $this->messages->add(_('Password values are not equals'), MSG_TYPE_ERROR);
    	    $this->sendJSON(['messages' => $this->messages->messages]);
    	}
    	$locale = trim($this->request->getParam('locale'));
        $general_role = $this->request->getParam('generalrole');
        if (! $general_role) {
            $this->messages->add(_('User role in general group is necesary'), MSG_TYPE_ERROR);
            $this->sendJSON(['messages' => $this->messages->messages]);
        }
        $idRegisteredUser = \Ximdex\Runtime\Session::get('userID');
        $registeredUser = new User($idRegisteredUser);
        $canModifyUserGroup = $registeredUser->isAllowedAction(Group::ID_GENERAL, Action::MODIFY_GROUP_USERS);
        $group = new Group();
        $group->setID(Group::getGeneralGroup());
        $group->getUserList();
        $roleOnNode = $group->getRoleOnNode($idNode);
        if ($canModifyUserGroup) {
            $group->changeUserRole($idNode, $general_role);
        } elseif ($roleOnNode != $general_role) {
            $this->messages->add(_('You don\'t have enough permissions to modify the user role'), MSG_TYPE_WARNING);
        }
    	$user = new User($idNode);
    	$user->set('Name', $name);
    	$user->set('Email', $email);
		$user->set('Locale', $locale);
    	if (! empty($password)) {
    		$user->set('Pass', $password);
    	}
    	if ($user->update() !== false) {
    		$this->messages->add(_('User has been successfully modified'), MSG_TYPE_NOTICE);
    	}
    	$this->messages->mergeMessages($user->messages);
		$values = array('messages' => $this->messages->messages);
		$this->sendJSON($values);
    }
}
