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


ModulesManager::file('/inc/workflow/Workflow.class.php');
ModulesManager::file('/inc/mail/Mail.class.php');
ModulesManager::file('/inc/model/role.php');

class Action_workflow_backward extends ActionAbstract {

	// Main method: shows the initial form
	function index () {
 		$idNode = (int) $this->request->getParam("nodeid");
        $this->addJs('/actions/workflow_forward/resources/js/workflow_forward.js');

		$idUser = XSession::get('userID');
        $user = new User($idUser);

		//Getting user roles on current node
        $userRoles=$user->GetRolesOnNode($idNode);

		$node= new Node($idNode);
		$workflow=new WorkFlow($idNode, $node->GetState());

        //Getting previous state
        $prevState = $workflow->GetPreviousState();
		$workflowPrev = new WorkFlow($idNode,$prevState);
		$prevStateName=$workflowPrev->GetName(); 

		//Checking if the user has some role with permission to change to next State
        $allowed=FALSE;
        foreach($userRoles as $userRole => $myIdRole) {
            $role = new Role($myIdRole);
            if($role->HasState($prevState)) {
                $allowed=TRUE;
                break;
            }
        }

        if(!$allowed) {
            $this->messages->add(_('You have not privileges to move forward the node to next status.'), MSG_TYPE_WARNING);
            $this->messages->add(_('You have not assigned a role with privileges to modify workflow status on any of groups associated with the node or the section which contains it.'), MSG_TYPE_WARNING);

            $values = array(
                'messages' => $this->messages->messages
            );

            $this->render($values, 'show_results', 'default-3.0.tpl');

            return ;
        }

        $conf = ModulesManager::file('/conf/notifications.conf');
        $defaultMessage=$this->buildMessage($conf["defaultMessage"], $prevStateName,$node->GetNodeName());


		$values = array(
            'idnode' => $idNode,
            'go_method' => 'workflow_backward',
            "defaultMessage" => $defaultMessage,
            "group_state_info" => Group::getSelectableGroupsInfo($idNode),
    		'prevStateName' => $prevStateName,
    		'currentStateName' => $workflow->GetName()
        );

		if($workflow->IsInitialState()) {
			$this->messages->add(_('The document is already in its initial state. A previous state cannot be stablished.'), MSG_TYPE_ERROR);
			$values['messages'] = $this->messages->messages;
			$this->render($values, null);
		}else {
			$this->render($values, null, 'default-3.0.tpl');
		}
	}

	function workflow_backward() {

		$idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $workflow = new WorkFlow($idNode, $node->GetState());
        $prevState = $workflow->GetPreviousState();
        

        $notificableUsers = $this->request->getParam('users');
        $texttosend = $this->request->getParam('texttosend');
        $sendNotifications = $this->request->getParam('sendNotifications');
        //If must send notifications
        if ((boolean)$sendNotifications) {
            $sent = $this->sendNotifications($idNode, $prevState, $notificableUsers, $texttosend);
            if (!$sent) {
                $values = array(
                    'goback' => true,
                    'messages' => $this->messages->messages
                );
                $this->render($values, 'show_results', 'default-3.0.tpl');
                return;
            }
        }        

        $node->setState($prevState);

		$this->render(NULL, 'success.tpl', 'default-3.0.tpl');

	}
	
	/**
     * Sends notifications and sets node state
     * 
     * Called from publicateNode
     * 
     * @param int $idNode Node id
     * @param int $idState Target state in workflow.
     * @param array<int> $userList Array with id of users to notificate.
     * @param string $texttosend Texto to send in notification mail.
     *
     * @return boolean true if the notification is sended.
     */
    protected function sendNotifications($idNode, $idState, $userList, $texttosend) {

        $send = true;
        $idUser = XSession::get("userID");
        if (count($userList) == 0) {
            $this->messages->add(_('Users to notify has not been selected.'), MSG_TYPE_WARNING);
            $send = false;
        }

        if (empty($texttosend)) {
            $this->messages->add(_('No message specified.'), MSG_TYPE_WARNING);
            $send = false;
        }

        if (!$send) {
            return false;
        }

        $node = new Node($idNode);
        $idActualState = $node->get('IdState');
        $actualWorkflowStatus = new WorkFlow($idNode, $idActualState);
        $nextWorkflowStatus = new WorkFlow($idNode, $idState);

        if (count($userList) > 0) {
            $userNameList = array();
            foreach($userList as $id) {
                $user = new User($id);
                $userNameList[] = $user->get('Login');
            }
            $userNameString = implode(', ', $userNameList);
        }

        $user = new User($idUser);
        $from = $user->get('Login');
        $userName = $user->get('Name');
        $nodeName = $node->get('Name');
        $nodePath = $node->GetPath();

        $nextStateName = $nextWorkflowStatus->pipeStatus->get('Name');
        $actualStateName = $actualWorkflowStatus->pipeStatus->get('Name');

        $subject = _("Ximdex CMS: new state for document:")." ".$nodeName;
        $content  =
            _("State backward notification.") . "\n"
            . "\n"
            . _("The user")." ".$userName." "._("has changed the state of")." ".$nodeName."\n"
            . "\n"
            . _("Full Ximdex path")." --> ".$nodePath."\n"
            . "\n"
            . _("Initial state")." --> ".$actualStateName."\n"
            . _("Final state")." --> ".$nextStateName."\n"
            . "\n"
            . "\n"
            . _("Comment").":"."\n"
            . $texttosend."\n"
            . "\n";
        parent::sendNotifications($subject, $content, $userList);
            
        return true;
    }

    /**
    *Replace %doc and %state macros in default Message.
    *
    *The message is getted from conf/notifications.conf
    *
    *@return string with the text replaced.
    */
    private function buildMessage($message, $stateName,$nodeName){
        $mesg=preg_replace('/%doc/', $nodeName, $message);
        $mesg=preg_replace('/%state/', $stateName,$mesg);
        return $mesg;
    }
}
?>