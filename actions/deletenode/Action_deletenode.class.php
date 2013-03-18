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



ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/Toldox.class.php', 'tolDOX');
ModulesManager::file('/actions/browser3/inc/GenericDatasource.class.php');

class Action_deletenode extends ActionAbstract {

	function index () {

		$formType = "simple";

		$nodes = $this->request->getParam("nodes");

		$nodes = GenericDatasource::normalizeEntities($nodes);
		$params = $this->request->getParam("params");
		$userID = XSession::get('userID');
		$texto = "";

		if (count($nodes) == 1) {
			$idNode = $this->request->getParam('nodeid');
		}

		$node	= new Node($idNode);
		$children = $node->GetChildren();

		$user = new User($userID);
		$depList = array();

		if ($user->HasPermission("delete on cascade")) {

			$undeletableChildren = array();

			if ($node->nodeType->get('Name') != "XmlContainer") {
				if ($node->nodeType->get('Name') != 'Channel') {
					$depList = $node->GetDependencies();
				}
				$undeletableChildren = $node->TraverseTree(5);

			} else {

				if (sizeof($children) > 0) {

					foreach ($children as $idChild) {
						$childNode = new Node($idChild);
						$depList = array_merge($depList, $childNode->GetDependencies());
					}
				}
			}

			if (sizeof($depList) > 0) {

				foreach($depList as $idDep) {
					$depNode = new Node($idDep);
					$undeletableChildren = array_unique(array_merge($undeletableChildren, $depNode->TraverseTree(5)));
				}
			}

			if (!empty($undeletableChildren)) {

				$texto = _("Because of system restrictions, the following nodes cannot be deleted: ");

				foreach( $undeletableChildren as $_undelete) {
					$node_t = new Node($_undelete);
					$name_t = $node_t->GetNodeName();
					$texto .= " $name_t($_undelete), ";
				}
			} else {
				$formType = "dependencies";
			}

		} else {

			if (sizeof($children) && sizeof($depList)) {

				$texto = $node->nodeType->get('Name') != "XmlContainer" ?
					_("Selected document or folder is not empty. It also have external dependencies with other system documents and it cannot be deleted using your role. Please, undo dependencies or use a user with suitable permissions.") : _("Your role cannot delete the selected container because of it has idiomatic versions depending on it.");

				$formType = "no_permisos";
			}

			/// Error: if it has not permits to cascade deletion and node has children but has dependencies
			if (sizeof($children) && !sizeof($depList)) {

				$texto = _("Selected document or folder is not empty and it cannot be deleted using your role. Please, undo dependencies or use a user with suitable permissions.");

				$formType = "no_permisos";
			}

			/// Error: if it has not permits to cascade deletion and node has not children but has dependencies
			if (!sizeof($children) && sizeof($depList)) {
				$texto = _("Selected file has dependencies with other system documents and it cannot be deleted using your role. Please, undo dependencies or use a user with suitable permissions.");
				$formType = "no_permisos";
			}

			/// If it has not permits to cascade deletion and node has not children and has not dependencies
			/// Here it is allowed atomic deletion
			if (!sizeof($children) && !sizeof($depList)) {
				$formType = "simple";
			}
		}

		$values = array(
			'id_node' => $idNode,
			'params' => $params,
			'nameNode' => $node->get('Name'),
			'formType' => $formType,
			'texto' => $texto,
			"go_method" => "delete_node",
		);

		if (sizeof($depList) > 0) {

			foreach($depList as $idDep) {
				$depNode = new Node($idDep);
				$values["depList"][] = $depNode->GetPath();
			}
		}

		if ($formType == 'no_permisos') {

			$values['titulo'] = $node->nodeType->get('Name') != "XmlContainer" ?  _("List of pending documents")
				: _("To be able to delete this node, you should first delete the following idiomatic versions");

		} else if ($formType == 'dependencies') {

			// Looking for publication (pending and in) tasks for node

			$sync = new SynchroFacade();
			$pendingTasks = $sync->getPendingTasksByNode($idNode);
			$isPublished = $sync->isNodePublished($idNode);

			if (sizeof($children) > 0  && !($isPublished && count($pendingTasks) > 0)) {

				foreach ($children as $idChild) {
					$childNode = new Node($idChild);
					$children = array_merge($children, $childNode->TraverseTree());
				}

				foreach ($children as $idChild) {
					$pendingTasks =  $sync->getPendingTasksByNode($idChild);
					$isPublished = $sync->isNodePublished($idChild);

					if ($isPublished && count($pendingTasks) > 0) {
						break;
					}
				}
			}

			$values["depList"] = $depList;
			$values["pendingTasks"] = count($pendingTasks);
			$values["isPublished"] = $isPublished;
		}

	//	$this->addJs('/actions/composer/resources/js/widgets_controllers/default.js');
		$this->addJs('/actions/deletenode/resources/js/deletenode.js');

		$this->render($values, null, 'default-3.0.tpl');
	}

	function delete_node() {

		$idNode	= $this->request->getParam("nodeid");
		$depList = array();

		//If ximDEMOS is actived and nodeis is rol "Demo" then  remove is not allowed
		if(ModulesManager::isEnabled("ximDEMOS") &&  XSession::get('user_demo')) {
			$node = new Node($idNode);
			$name = $node->get("Name");
			if("Demo" == $name ) {
				$this->messages->add(_('Changes in Demo role are not allowed'), MSG_TYPE_NOTICE);

				$values = array(
					'action_with_no_return' => true,
					'messages' => $this->messages->messages
				);
				$this->render($values);

				return ;
			}
		}

		$userID = XSession::get('userID');
		$unpublishDoc = ($this->request->getParam("unpublishdoc") == 1) ? true : false;

		// Deleting publication tasks

		$sync = new SynchroFacade();
		$sync->deleteAllTasksByNode($idNode, $unpublishDoc);

		$node = new Node($idNode);
		$parentID = $node->get('IdParent');

		$user = new User($userID);
		$canDeleteOnCascade = $user->HasPermission("delete on cascade");

		if ($canDeleteOnCascade) {


			if ($node->nodeType->get('Name') != 'Channel') {
				$depList = $node->GetDependencies();
			}

			$undeletableChildren = $node->TraverseTree(5);

			if ($node->nodeType->get('Name') == "XmlContainer") {

				$children = $node->GetChildren();

				foreach($children as $child) {
					$childNode = new Node($child);
					$depList = array_merge($depList, $childNode->GetDependencies());
				}

			} else {
 				if (is_array($depList)) {
					foreach($depList as $idDep) {
						$depNode = new Node($idDep);
						$undeletableChildren = array_unique(array_merge($undeletableChildren, $depNode->TraverseTree(5)));
					}
				}
			}

		//	if (count($undeletableChildren) > 0) {

				//If a node cannot be deleted


		//		$texto= _("Por restricciones del sistema, no se pueden eliminar los siguientes nodos:");
		//		foreach( $undeletableChildren as $_undelete) {
		//			$node = new Node($_undelete);
		//			$name = $node->GetNodeName();
		//			$texto .= " $name($_undelete), ";
		//		}

		//		$this->messages->add($texto, MSG_TYPE_WARNING);
		//	} else {

				// Deleting recursively

				$node = new Node($idNode);
				$node->delete();

				$err = NULL;
				if($node->numErr) {

					$err = _("An error occurred while deleting:");
					$err .= '<br>' . $node->get('IdNode') . " " . $node->GetPath() . '<br>' . _("Error message: ") . $node->msgErr . "<br><br>";

				} else {

					if ($node->nodeType->get('Name') == 'Channel') {
						$sql = sprintf('delete from RelStrDocChannels where IdChannel = %s', $idNode);
						$db = new DB();
						$db->execute($sql);
					}
				}

				if (is_array($depList)) {
					foreach($depList as $depID) {
						$depNode = new Node($depID);
						$depNode->delete();

						if($depNode->numErr) {
							if(!strlen($err))
							$err = _("An error occurred while deleting dependencies: ");
							$err .= '<br>'.$depNode->get('IdNode'). " ".$depNode->GetPath().'<br>'. _("Error message: ") .
								$depNode->msgErr . "<br><br>";
						}
					}
			//	}

				if (strlen($err)) {
					$this->messages->add($err, MSG_TYPE_ERROR);
				} else {
					$this->messages->add(_("All nodes were successfully deleted"), MSG_TYPE_NOTICE);
				}
			}
		} else {

			/// Error: if it has not permit to cascade deletion and node has children and dependencies
			if(sizeof($children) && sizeof($depList))
			$this->messages->add(_("Node is not empty and it has external dependencies."), MSG_TYPE_ERROR);

			/// Error: if it has not permit to cascade deletion and node has children but dependencies
			if(sizeof($children) && !sizeof($depList))
			$this->messages->add(_("Node is not empty."), MSG_TYPE_ERROR);

			/// Error: If it has not permit to cascade deletion and node has not children but has dependencies
			if(!sizeof($children) && sizeof($depList))
			$this->messages->add(_("It has external dependencies."), MSG_TYPE_ERROR);

			/// If it has not permit to cascade deletion and node has not children and has not dependencies
			/// Here it is allowed atomic deletion.
			if(!sizeof($children) && !sizeof($depList)) {
				$node->delete();
				$this->messages->add(_("Action successfully performed."), MSG_TYPE_NOTICE);
			}
		}

		$this->reloadNode($parentID);

		$values = array(
			'messages' => $this->messages->messages,
			'action_with_no_return' => true,
			'depList' => $depList
		);

		$this->render($values, NULL, 'messages.tpl');
	}

}
?>
