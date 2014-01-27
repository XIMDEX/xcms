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

if(ModulesManager::isEnabled('ximDEMOS')){
	require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/UnverifiedUsers_ORM.class.php');
}

class Action_deleteuser extends ActionAbstract {
   // Main method: it shows init form
    function index() {
		$idNode = $this->request->getParam('nodeid');

		$user = new User($idNode);
		if (!($user->get('IdUser') > 0)) {
			$this->messages->add(_('User could not be found'), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages));
		}


		$values = array(
			'id_node' => $idNode,
			'go_method' => 'deleteuser',
			'login' => $user->get('Login'),
			'realname' => $user->get('Name'),
			'email' => $user->get('Email'),
			'messages' => $this->messages->messages);

		$this->render($values, null, 'default-3.0.tpl');
    }

    function deleteuser() {
		$idParent = $idNode = $this->request->getParam('id_node');

		$user = new Node($idNode);

		if (!($user->get('IdNode') > 0)) {
			$user->messages->add(_('User could not be found'), MSG_TYPE_ERROR);
		} else {
			$idParent = $user->get('IdParent');
/************************ ximDEMOS *******************/
			if (ModulesManager::isEnabled('ximDEMOS')){
				//Now we have to delete the project's user
				$userNode = new User($idNode);
				$groupList = $userNode->GetGroupList();
				$idNode=false;
				foreach ($groupList as $idGroup) {
					if ($idGroup != 101){
						$group = new Node($idGroup);
						$node = new Node(10000);

						$idProject = $node->GetChildByName("Picasso_".$group->GetNodeName());
						if ($idProject){
				    		error_log("[ximDEMOS] The project with Id {$idProject} is going to be deleted.");
				    			$nodeToDelete = new Node($idProject);
				    			$nodeToDelete->DeleteNode();				
						}
			    
						error_log("[ximDEMOS] The group with Id $idGroup is going to be deleted.");
						$group->delete();
			    		}
				}	
			}
/************************ ximDEMOS *******************/
		   	$result = $user->delete();	
/************************ ximDEMOS *******************/
			if (ModulesManager::isEnabled('ximDEMOS')){
				$uUser = new UnverifiedUsers_ORM();
				$dbObj = new DB();
	                        $query = sprintf("DELETE FROM UnverifiedUsers where email='%s'",$userNode->get('Email'));
	                        $dbObj->Execute($query);
			}
/************************ ximDEMOS *******************/
			if($result) {
				$user->messages->add(_('User has been successfully deleted'), MSG_TYPE_NOTICE);
			}
		}

		$this->reloadNode($idParent);

		$values = array('messages' => $user->messages->messages );

		$this->render($values);
    }
}
?>
