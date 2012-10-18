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



ModulesManager::file('/inc/mail/Mail.class.php');
ModulesManager::file('/inc/model/role.inc');

class Action_workflow_backward extends ActionAbstract {
   // Main method: shows the initial form
    function index () {
 	     	$idNode = (int) $this->request->getParam("nodeid");
      		$actionID = (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");
		$userID = XSession::get("userID");
		$nombre_esclavo=NULL;
		$error = false;
		$form_type = 'none';

		$node= new Node($idNode);
		$workflow=new Workflow($idNode, $node->GetState());
		if ($node->IsWorkflowSlave()!="") {
			$form_type = 'form_esclavo';
			$sharedID = $node->IsWorkflowSlave();
			$nodeShared=new Node($sharedID);
			$nombre_esclavo =$nodeShared->GetNodeName();
		}else {

			if (!$workflow->IsInitialState()) {
				$form_type = 'elegir_grupo';
			} else {
				//$this->messages->add(_("A state previous to Edition cannot be stablished"), MSG_TYPE_ERROR);
				$error = 1;
			}
		}

		if($form_type == "elegir_grupo") {
			$values = $this->_elegir_grupo($idNode, $userID, false);
			$values['id_node'] = $idNode;
			if(!$values['allGroups']) {
				$error = 1;
			}
		}else {
			$values = array(
				'id_node' => $idNode,
				'id_action' => $actionID,
				'params' => $params,
				'userID' => $userID,
				'form_type' => $form_type,
				'nombre_esclavo' => $nombre_esclavo,
				"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
				"go_method" => "wordkflow_backward",
			);
		}

		$error = 1; //Due to the current publication system, this action has not much sense(ticket #2133)
		if($error) {
			$this->messages->add(_('The document is already in its initial state. A previous state cannot be stablished.'), MSG_TYPE_ERROR);
			$values['messages'] = $this->messages->messages;
			$this->render($values, null);
		}else {
			$this->render($values, null, 'default-3.0.tpl');
		}
    }

	function wordkflow_backward() {
	    $idNode		= (int) $this->request->getParam("nodeid");
		$userID 	= 	(int) $this->request->getParam("userid");
		$estado 	= 	 $this->request->getParam("estado");	
		$boton 		= 	 $this->request->getParam("boton");
		$indice 	= 	 $this->request->getParam("idx");
		$check 		= 	 $this->request->getParam("check");

	// Calling the function which will carry out the job
		if($estado=='mostrarusers' and $boton =='previous') {
			$values = $this->_elegir_grupo($idNode, $userID, false);
		}
		// If group value is empty, it is notified and the form is painted again without changing the state
		elseif($estado=='mostrargrupos' and $indice==null){
			$values = $this->_elegir_grupo($idNode, $userID, false);
		}
		elseif($estado=='mostrargrupos') {
			$groups=$_POST["groups"];
			$states=$_POST["states"];
	
			$groupID=$groups[$indice];
			$nextstateID=$states[$indice];

			$values = $this->_formMostrarUsers($idNode, $groupID, $nextstateID,$userID);
		} elseif($estado=='mostrarusers' and !$check) {
			$status = $_POST["states"];
			$nextstateID = $status[$indice];
			
			$groups = $_POST['groups'];
			$groupID = $groups[$indice];
	
			$userID=$_POST["userid"];
	
			$values = $this->_formMostrarUsers($idNode, $groupID, $nextstateID,$userID,_('At least one user to notify should be selected.'));
		} elseif($estado=='mostrarusers' and $boton=='next') {

			$stateID=$_POST["stateid"];
	
			$listausers=$_POST["check"];
			$userID=XSession::get("userID");
			$texttosend=$_POST["texttosend"];
			$values = $this->_cambiarEstado($idNode, $stateID, $listausers,$userID, $texttosend);
		}


		$this->render($values, "index", 'default-3.0.tpl');

	}
	

	private function _elegir_grupo($nodeID, $userID, $emptyGroup) {

		$user = new User($userID);
		$group = new Group();
		$node = new Node($nodeID);
		$currentWorkflowStatus = new Workflow($nodeID, $node->GetState());
		
		
		$validState2 = array();
		$validGroup2 = array();
		// Looking for next states for the user groups
		$groups = $user->GetGroupList();
		$groups = array_diff($groups, array($group->GetGeneralGroup()));

		if($groups) {
			foreach($groups as $gID) {
				$group = new Group($gID);
				$users = $group->GetUserList();

				if (is_array($users) && count($users) > 0) {
					foreach($users as $uID) {
						$nextState = $this->_getPreviousAllowedState($nodeID, $uID, $gID);

						if (!is_null($nextState)) {
							$allGroups=false;
							$validState2[]=$nextState;
							$validGroup2[]=$gID;
						}
					}
				}
			}
		}
	
		//Getting the first state for each group we belong to
		if (count($validState2) > 0) {
				$listaGrupos=array_unique($validGroup2);
				foreach($listaGrupos as $gID) {
					$listaEstados='';
					foreach($validGroup2 as $idx =>$gID2) {
						if ($gID2==$gID) {
							$listaEstados[]=$validState2[$idx];
						}
					}

					$i=0;
					do {
						$stateID = $currentWorkflowStatus->GetPreviousState();
						$workflow = new WorkFlow($nodeID, $stateID);
					} while(($i++ < 50) and !$workflow->IsInitialState() and !in_array($stateID,$listaEstados));
					$validState[]=$stateID;
					$validGroup[]=$gID;
					}
				}
		
		
		
			//If there is not any, looking for all groups
			if (!count(($validState2) > 0)) {
				$groups= $node->GetGroupList();
				if($groups) {
					$groups=array_diff($groups,array($group->GetGeneralGroup()));
				}
	
				foreach($groups as $gID) {
					$group->SetID($gID);
					$users = $group->GetUserList();
					if ($users) {
						foreach($users as $uID) {
							$nextState=$this->_getPreviousAllowedState($nodeID, $uID, $gID);
							if (!is_null($nextState)) {
								$allGroups=true;
								$validState2[]=$nextState;
								$validGroup2[]=$gID;
							}
						}
					}
				}
		
				//Now, keeping only the minor states
				//Looking for the current state for the node
				if(count($validState2) > 0) {
					do {
						$stateID = $currentWorkflowStatus->GetPreviousState();
						$workflow = new WorkFlow($nodeID, $stateID);
					} while(!$workflow->IsInitialState() and !in_array($stateID,$validState2));
		
					foreach($validState2 as $idx =>$vstateID){
						if ($vstateID == $stateID and !in_array($validGroup2[$idx], $validGroup)) {
							$validState[] = $stateID;
							$validGroup[] = $validGroup2[$idx];
						}
					}
				}
			}

		if(count($validGroup) > 0) {
			$validGroupList = array();
			foreach ($validGroup as $idx => $groupID) {
				$stateID=$validState[$idx];
				$workflow = new WorkFlow($nodeID, $stateID);
				$group->SetID($groupID);
				$nombreEstado = $workflow->GetName();
				$nombreGroup = $group->GetGroupName();

				$validGroupList[] = array(
					'userID' => $userID,
					"nombreEstado" => $nombreEstado, 
					"nombreGroup" => $nombreGroup, 
					"groupID" => $groupID, 
					"stateID" => $stateID,
					"idx" => $idx
				);
			}
		}

	

		$values = array(
			'userID' => $userID,
			'allGroups' => $allGroups,
			'validGroup' => $validGroup,
			'validGroupList' => $validGroupList,
			'emptyGroup' => $emptyGroup,
			'form_type' => 'elegir_grupo',
			"go_method" => "wordkflow_backward",
		);

		return $values;
	}


	private function _getPreviousAllowedState($nodeID, $userID, $groupID)
	{
		$user = new User($userID);
		$roleID = $user->GetRoleOnNode($nodeID, $groupID);
		
		$node = new Node($nodeID);
		$stateID = $node->GetState();
		
		$workflow = new Workflow($nodeID, $stateID);
		$allowedStates = $workflow->GetAllowedStates($roleID);
		if($allowedStates) {
			do {
				$stateID = $workflow->GetPreviousState();
				$workflow = new WorkFlow($nodeID, $stateID);
			} while(!$workflow->IsInitialState() and !in_array($stateID,$allowedStates));
		}
	
		if (!is_array($allowedStates)) {
			return NULL;
		}
		return (in_array($stateID, $allowedStates)) ? $stateID : NULL;
	}

	private function _formMostrarUsers($nodeID, $groupID, $nextstateID, $userID, $message=null) {
		$group = new Group($groupID);
		
		$role = new Role();
		$roles = $role->getAllRolesForStatus($nextstateID);
		
		$user = new User();
		$users = $user->GetAllUsers();
		$userlist = array();
		if($users) {
			foreach ($users as $userID) {
				$user->SetID($userID);
				$roleID=$user->GetRoleOnNode($nodeID, $groupID);
				if ($roleID) {
					if(in_array($roleID, $roles)) {
						$userlist[] = array("id" => $userID, "name" => $user->GetRealName() );
					}
				}
			}

		}

		$workflow = new WorkFlow($nodeID, $nextstateID);
		$values = array(
			'id_node' => $nodeID,
			'groupID' => $groupID,
			'groupName' => $group->GetGroupName(),
			'nextstateID' => $nextstateID,
			'userID' => $userID,
			'message' => $message,
			'userlist' => $userlist,
			'emptyGroup' => $emptyGroup,
			'workflow_name' => $workflow->getName(),
			'form_type' => 'mostrar_user',
			"go_method" => "wordkflow_backward",
		);

		return $values;

	}

	private function _cambiarEstado($nodeID, $stateID, $listausers=null, $uID, $texttosend) {
		$node=new Node($nodeID);
		$estadoactual=$node->GetState();
		$user = new User();
		$workflow = new Workflow($nodeID, $estadoactual);
	
		$workflow->SetID($stateID);
		$estadoprevio=$workflow->GetNextState();
	
		$workflow2 = new Workflow($nodeID, $estadoprevio);

		$idTransition = $workflow2->pipeProcess->getTransition($estadoprevio);
		
		$dataFactory = new DataFactory($nodeID);
		$idVersion = $dataFactory->GetLastVersionId();
		$transition = new PipeTransition($idTransition);
		$transformedContent = $transition->reverse($idVersion, $node->GetContent(), array());
		
		$node->SetContent($transformedContent);
		
		//Changing state
		$node->SetState($stateID);
		$errors = $node->numErr;
		$errorsMSG = $node->msgErr;

		//Notifying the selected users
		$users = array();
		if ($listausers) {
			foreach($listausers as $userID) {
				$user->SetID($userID);
				if ($to)
					$to = $to . "," . $user->GetLogin();
				else
					$to = $user->GetLogin();
			}


			$user->SetID($uID);
			$from=$user->GetLogin();
			
			$subject = _('Ximdex document new state:')." ".$node->GetNodeName();

			$content = _('Backward state notification.')."\n\n";

			$content.= _('The user')." `". $user->GetRealName(). "' "._('has changed the state of the document')." '".$node->GetNodeName()."´\n\n";
			$content.= _('Full path')." --> ". $node->GetPath() ."\n\n";

			$content.= _('Initial state')." --> ".$workflow2->GetName()."\n";
			$content.= _('Final state')." --> ". $workflow->GetName()."\n";

			$content.= "/n/n"._('Comment').":\n".$texttosend."\n";

			$msg = new MesgEvent($from, $from, $to, $subject, $content);
			$errorsMail = $msg->numErr;
			$errorsMailMSG = $msg->msgErr;


			foreach($listausers as $userID)
			{
				$user->SetID($userID);
	
				$user_email = $user->GetEmail();
				$user_name = $user->GetRealName();
	
				$mail = new Mail();
	
				$mail->addAddress($user_email, $user_name);
				$mail->Subject = $subject;
				$mail->Body = $content;
	
				if ($mail->Send()) {
					$exito = 1;
				}else{
					$exito = 0;
				}

				$users[] = array("exito" => $exito, "nombre" => $user_name, "email" => $user_email );
			}

		}


		$values = array(
			'id_node' => $nodeID,
			'node_name' => $node->GetNodeName(),
			'node_path' => $node->GetPath(),
			'estado2_name' => $workflow2->GetName(),
			'estado1_name' => $workflow->GetName(),
			'user_to' => $to,
			'users' => $users,
			'listausers' => $listausers,
			'errorsMail' => $errorsMail,
			'errorsMailMSG' => $errorsMailMSG,
			'form_type' => 'cambiar_estado',
			'errors' => $errors,
			'errorsMSG' => $errorsMSG,
		);

		return $values;


	}

	
}
?>
