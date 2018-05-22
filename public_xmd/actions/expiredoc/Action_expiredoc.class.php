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

use Ximdex\Logger;
use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\Role;
use Ximdex\Models\Server;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Utils\Serializer;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Workflow\WorkFlow;

Ximdex\Modules\Manager::file('/inc/model/NodesToPublish.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/conf/synchro_conf.php', 'ximSYNC');

/**
 * Move a node to next state.
 * If the node is not a structured document the next state will be publication.
 */
class Action_expiredoc extends ActionAbstract
{
    public function index()
    {
        // Get nodeid or first in nodes if nodeid doesn't exist.
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        if (empty($idNode)) {
            $nodes = $this->request->getParam('nodes');
            $idNode = $nodes[0];
        }
        if (! $this->validateInIndex($idNode)) {
            $this->renderMessages();
        }
        $node = new Node($idNode);
        
        // Loading Notifications default values
        $conf = \Ximdex\Modules\Manager::file('/conf/notifications.php', 'XIMDEX');
        $values = array(
            'group_state_info' => Group::getSelectableGroupsInfo($idNode),
            'required' => $conf['required'] === true ? 1 : 0,
            'idNode' => $idNode,
            'node_Type' => $node->nodeType->GetName(),
            'name' => $node->GetNodeName()
        );
            
        // Show the expiration form
        $values['go_method'] = 'expireNode';
        $values['defaultMessage'] = $this->buildMessage($conf["defaultMessage"], 'Expire', $node->get('Name'));
        $values = array_merge($values, $this->buildExtraValues($idNode));
        
        // Loading resources for the action form
        $this->addJs('/actions/expiredoc/resources/js/expiredoc.js');
        $this->addCss('/actions/expiredoc/resources/css/style.css');
        // $this->addCss('/assets/style/jquery/ximdex_theme/widgets/calendar/calendar.css');
        $this->render($values, NULL, 'default-3.0.tpl');
    }

    /**
     * Replace %doc and %state macros in default Message.
     * The message is getted from conf/notifications.php
     *
     * @return string with the text replaced.
     */
    private function buildMessage($message, $stateName, $nodeName)
    {
        $mesg = preg_replace('/%doc/', $nodeName, $message);
        $mesg = preg_replace('/%state/', $stateName, $mesg);
        return $mesg;
    }
    
    public function expireNode()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $dateDown = $this->request->getParam('dateDown_timestamp');
        $markEnd = $this->request->getParam('markend') ? true : false;
        $structure = $this->request->getParam('no_structure') ? false : true;
        $levels = $this->request->getParam('levels');
        if ($levels == 'all') {
            
            // All linked elements
            $deepLevel = -1;
        }
        elseif ($levels == 'deep') {
            
            // N levels of depth
            $deepLevel = abs($this->request->getParam('deeplevel'));
        }
        else {
            
            // Zero levels, only the given node
            $deepLevel = 0;
        }
        $node = new Node($idNode);
        $nodename = $node->get('Name');
        
        // The expiration times are in milliseconds
        $down = (! is_null($dateDown) && $dateDown != "") ? $dateDown / 1000 : time();
        
        // If send notifications
        $sendNotifications = $this->request->getParam('sendNotifications');
        if ((bool) $sendNotifications) {
            $notificableUsers = $this->request->getParam('users');
            $texttosend = $this->request->getParam('texttosend');
            $sent = $this->sendNotification($idNode, $notificableUsers, $texttosend);
            if (! $sent) {
                $values = array(
                    'goback' => true,
                    'messages' => $this->messages->messages
                );
                $this->render($values, 'show_results', 'default-3.0.tpl');
                return false;
            }
        }
        
        // Expiration flags
        $flagsExpiration = array(
            'markEnd' => $markEnd,
            'linked' => true,
            'workflow' => true,
            'expireSection' => false,
            'deeplevel' => $deepLevel,
            'structure' => $structure,
            'force' => true,
            'recurrence' => false
        );
        
        // Processing the expiration of related ones
        $sync = new SynchroFacade();
        if ($sync->expire($node, $down, $flagsExpiration) === false) {
            $this->messages->add(sprintf(_('%s could not sent to expire'), $nodename), MSG_TYPE_ERROR);
        } else {
            $this->messages->add(sprintf(_('%s has been successfully sent to expire'), $nodename), MSG_TYPE_NOTICE);
        }
        $values = array('messages' => $this->messages->messages);
        $this->sendJSON($values);
    }

    /**
     * Print a JSON object with users of the selected group.
     * Called from an ajax request.
     * 
     * @return void
     */
    public function notificableUsers()
    {
        $idGroup = $this->request->getParam('groupid');
        $idState = $this->request->getParam('stateid');
        $idNode = $this->request->getParam('nodeid');
        $group = new Group($idGroup);
        $workflow = new WorkFlow($idNode, $idState);
        $values = array(
            'messages' => array(
                _('You do not belong to any group with publication privileges')
            )
        );
        $validateInGroup = $this->validateInSelectedGroup($group, $workflow, $idState, $idGroup);
        if ($validateInGroup === false) {
            return false;
        }
        if ($group->get('IdGroup') > 0 && $validateInGroup) {
            $role = new Role();
            $roles = $role->getAllRolesForStatus($idState);
            $user = new User();
            $users = $user->GetAllUsers();
            $notificableUsers = array();
            if (! empty($users) && is_array($users)) {
                foreach ($users as $idUser) {
                    $user = new User($idUser);
                    $idRole = $user->GetRoleOnNode($idNode, $idGroup);
                    if (($idRole > 0) && (in_array($idRole, $roles))) {
                        $notificableUsers[] = array(
                            'idUser' => $idUser,
                            'userName' => $user->get('Name')
                        );
                    }
                }
            }
            $values = array(
                'group' => $idGroup,
                'groupName' => $group->get('Name'),
                'state' => $idState,
                'stateName' => $workflow->pipeStatus->get('Name'),
                'notificableUsers' => $notificableUsers
            );
        }
        header('Content-type: application/json');
        $values = Serializer::encode(SZR_JSON, $values);
        echo $values;
    }

    /**
     * Obtains the publication intervals
     * Called from index
     *
     * @param int $idNode
     * @return array With info about the available gaps
     */
    private function getPublicationIntervals($idNode)
    {
        $nodesToPublish = new NodesToPublish();
        $intervals = $nodesToPublish->getIntervals($idNode);
        return $this->formatInterval($intervals);
    }

    /**
     * Format the date intevals array.
     *
     * @param int $idNode
     * @return array With info about the available gaps
     */
    private function formatInterval($gaps)
    {
        $gapInfo = array();
        if (count($gaps) > 0) {
            foreach ($gaps as $gap) {
                $gapInfo[] = array(
                    'BEGIN_DATE' => strftime("%d/%m/%Y %H:%M:%S", $gap['start']),
                    'END_DATE' => isset($gap['end']) ? strftime("%d/%m/%Y %H:%M:%S", $gap['end']) : null
                );
            }
        }
        return $gapInfo;
    }

    /**
     * Build params for the value array.
     * 
     * @param int $idNode : the current Node
     * @return array.
     */
    private function buildExtraValues($idNode)
    {
        setlocale(LC_TIME, "es_ES");
        $idUser = \Ximdex\Runtime\Session::get('userID');
        $user = new User($idUser);
        $node = new Node($idNode);
        $nodeTypeName = $node->nodeType->GetName();
        $gapInfo = $this->getPublicationIntervals($idNode);
        return array(
            'gap_info' => $gapInfo,
            'has_unlimited_life_time' => SynchroFacade::HasUnlimitedLifeTime($idNode),
            'timestamp_from' => time(),
            'structural_publication' => $user->HasPermission('structural_publication') ? '1' : '0',
            'advanced_publication' => $user->HasPermission('advanced_publication') ? '1' : '0',
            'nodetypename' => $nodeTypeName,
            'synchronizer_to_use' => \Ximdex\Modules\Manager::isEnabled('ximSYNC') ? 'ximSYNC' : 'default',
            'ximpublish_tools_enabled' => \Ximdex\Modules\Manager::isEnabled('ximPUBLISHtools'),
            'show_rep_option' => true
        );
    }

    /**
     * Sends notifications and sets node state
     * Called from expireNode
     *
     * @param int $idNode : Node id
     * @param int $idState : Target state in workflow.
     * @param array<int> $userList : Array with id of users to notificate.
     * @param string $texttosend : Text to send in notification mail.    
     * @return boolean true if the notification is sended.
     */
    private function sendNotification($idNode, $idState, $userList, $texttosend = "")
    {
        $send = true;
        if (count($userList) == 0) {
            $this->messages->add(_('Users to notify has not been selected.'), MSG_TYPE_WARNING);
            $send = false;
        }
        if (empty($texttosend)) {
            $this->messages->add(_('No message specified.'), MSG_TYPE_WARNING);
            $send = false;
        }
        if (! $send) {
            return false;
        }
        $idUser = \Ximdex\Runtime\Session::get("userID");
        $node = new Node($idNode);
        $idActualState = $node->get('IdState');
        $actualWorkflowStatus = new WorkFlow($idNode, $idActualState);
        $nextWorkflowStatus = new WorkFlow($idNode, $idState);
        if (count($userList) > 0) {
            $userNameList = array();
            foreach ($userList as $id) {
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
        $subject = _("Ximdex CMS: new state for document:") . " " . $nodeName;
        $content = _("State forward notification.") . "\n" . "\n" . _("The user") . " " . $userName . " " . _("has changed the state of") 
            . " " . $nodeName . "\n" . "\n" . _("Full Ximdex path") . " --> " . $nodePath . "\n" . "\n" . _("Initial state") . " --> " 
            . $actualStateName . "\n" . _("Final state") . " --> " . $nextStateName . "\n" . "\n" . "\n" . _("Comment") . ":" . "\n" 
            . $texttosend . "\n" . "\n";
        parent::sendNotifications($subject, $content, $userList);
        return true;
    }

    /**
     * Publicate the node
     *
     * Request params
     *
     * * nodeid
     * * dateUp
     * * dateDown
     * * markend
     * * republish
     * * no_structure
     * * all_levels
     * * sendNotifications
     * * users
     * * stateid
     * * texttosend
     */
    public function publicateNode()
    {
        $idNode = $this->request->getParam('nodeid');
        
        // The publication times are in milliseconds
        $dateUp = $this->request->getParam('dateUp_timestamp');
        $dateDown = $this->request->getParam('dateDown_timestamp');
        $up = (! is_null($dateUp) && $dateUp != "") ? $dateUp / 1000 : time();
        $down = (! is_null($dateDown) && $dateDown != "") ? $dateDown / 1000 : null;
        $markEnd = $this->request->getParam('markend') ? true : false;
        $structure = $this->request->getParam('no_structure') ? false : true;
        $levels = $this->request->getParam('levels');
        if ($levels == 'all') {
            
            // All linked elements
            $deepLevel = -1;
        }
        elseif ($levels == 'deep') {
            
            // N levels of depth
            $deepLevel = abs($this->request->getParam('deeplevel'));
        }
        else {
            
            // Zero levels, only the given node
            $deepLevel = 0;
        }
        $force = $this->request->getParam('force') ? true : false;
        $sendNotifications = $this->request->getParam('sendNotifications');
        $notificableUsers = $this->request->getParam('users');
        $idState = $this->request->getParam('stateid');
        $texttosend = $this->request->getParam('texttosend');
        $lastPublished = $this->request->getParam('latest') ? false : true;
        Logger::info("ADDSECTION publicateNode PRE");
        $this->sendToPublish($idNode, $up, $down, $markEnd, $force, $structure, $deepLevel, $sendNotifications, $notificableUsers, $idState
            , $texttosend, $lastPublished);
    }

    private function buildFlagsPublication($markEnd, $structure = 1, $deepLevel = 1, $force = false, $lastPublished = 0)
    {
        // Creating flags to publicate
        $flagsPublication = array(
            'markEnd' => $markEnd,
            'structure' => $structure,
            'deeplevel' => $deepLevel,
            'force' => $force,
            'recurrence' => false,
            'workflow' => true,
            'lastPublished' => $lastPublished
        );
        return $flagsPublication;
    }

    /**
     * Validate if a node can be forwarded
     *
     * @param $idNode : The selected node identificator.
     * @return Boolean : true if the node exists and has a valid server and it is not a dependant node (Shared Workflow).
     */
    private function validateInIndex($idNode)
    {
        // Check if idnode exist
        $node = new Node($idNode);
        if (! ($node->get('IdNode') > 0)) {
            $this->messages->add(_('The node could not be loaded'), MSG_TYPE_ERROR);
            return false;
        }
        
        // Get Server node for selected node
        $idServer = $node->getServer();
        $serverNode = new Node($idServer);
        
        // Validate Servers. Must exist like node and server
        if (! ($serverNode->get('IdNode') > 0)) {
            $this->messages->add(_('The server could not be loaded'), MSG_TYPE_ERROR);
        } else {
            $server = new Server();
            $servers = $server->find('IdServer', 'IdNode = %s', array(
                $serverNode->get('IdNode')
            ), MONO);
            if (! (count($servers) > 0)) {
                $this->messages->add(_('The document belongs to a server which has not any configured physical server.'), MSG_TYPE_ERROR);
            }
        }
        if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
            return false;
        }
        return true;
    }
    
    private function validateInSelectedGroup($group, $workflow, $idState, $idGroup)
    {
        if (! $group->get('IdGroup') > 0) {
            $this->messages->add(sprintf(_('No information about the selected group (%s) could be obtained'), $idGroup), MSG_TYPE_ERROR);
        }
        if (! $workflow->pipeStatus->get('id') > 0) {
            $this->messages->add(sprintf(_('No information about the selected workflow state (%s) could be obtained'), $idState), MSG_TYPE_ERROR);
        }
        if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
            $this->render(array(
                'messages' => $this->messages->messages
            ), NULL, 'messages_in_progress_action.tpl');
            return false;
        }
        return true;
    }

}