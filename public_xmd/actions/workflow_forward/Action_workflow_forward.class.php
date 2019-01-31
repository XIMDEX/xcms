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

use Ximdex\Logger;
use Ximdex\Helpers\ServerConfig;
use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\Role;
use Ximdex\Models\Server;
use Ximdex\Models\User;
use Ximdex\Models\NodesToPublish;
use Ximdex\Models\Workflow;
use Ximdex\Modules\Manager;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Utils\Serializer;
use Ximdex\Sync\SynchroFacade;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * Move a node to next state
 * If the node is not a structured document the next state will be publication
 */
class Action_workflow_forward extends ActionAbstract
{
    /**
     * Default method.
     * Generate the next action form
     *
     * Request params:
     *
     * * nodeid: Node identificator. The node to forward to next state
     * * nodes: Node ids array. If nodeid is null get the first node in the array
     * 
     * @return void
     */
    public function index()
    {
        // Get nodeid or first in nodes if nodeid doesn't exist
        $idNode = (int) $this->request->getParam('nodeid');
        if (empty($idNode)) {
            $nodes = $this->request->getParam('nodes');
            $idNode = $nodes[0];
        }
        $node = new Node($idNode);
        if (! $this->validateInIndex($idNode)) {
            $this->renderMessages();
        }
        
        // Loading resources for the action form
        $this->addJs('/actions/workflow_forward/resources/js/workflow_forward.js');
        $this->addCss('/actions/workflow_forward/resources/css/style.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/calendar/calendar.css');
        
        // Get the current user to check his permissions
        $idUser = \Ximdex\Runtime\Session::get('userID');
        $user = new User($idUser);
        
        // Getting user roles on current node
        $userRoles = $user->GetRolesOnNode($idNode);
        
        // Getting current state
        $node = new Node($idNode);
        $workflow = new Workflow($node->nodeType->getWorkflow(), $node->GetState());
        
        // Getting next state
        $nextState = $workflow->getNextState();
        if (! $nextState) {
            $this->messages->add(_('This node is already in final state.'), MSG_TYPE_WARNING);
            $values = array(
                'messages' => $this->messages->messages,
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName()
            );
            $this->render($values, 'show_results', 'default-3.0.tpl');
            return;
        }
        
        // If node in final state show the latest form
        $workflowNext = new Workflow($node->nodeType->getWorkflow(), $nextState);
        $nextStateName = $workflowNext->getStatusName();
        $AllStates = $workflow->getAllStates();
        $find = false;
        $AllowedStates = [];
        $foundRol = false;
        foreach ($AllStates as $state) {
            
            // If this state is after currentState, append the next state
            if ($find) {
                
                // This is the next state
                foreach ($userRoles as $myIdRole) {
                    $role = new Role($myIdRole);
                    if ($role->hasState($state)) {
                        $workflowAll = new Workflow($node->nodeType->getWorkflow(), $state);
                        $AllowedStates[$state] = $workflowAll->getStatusName();
                        $foundRol = true;
                        break;
                    }
                }
                if (! $foundRol)
                    continue;
            }
            
            // If found the current state, we activate the flag
            if ($state == $node->GetState())
                $find = true;
        }
        
        // If is not allowed, send a message and the method finish
        if (! $foundRol) {
            $this->messages->add(_('You have not privileges to move forward the node to next status.'), MSG_TYPE_WARNING);
            $this->messages->add(_('You have not assigned a role with privileges to modify workflow status on any of groups associated with the node or the section which contains it.')
                , MSG_TYPE_WARNING);
            $values = array(
                'messages' => $this->messages->messages,
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName()
            );
            $this->render($values, 'show_results', 'default-3.0.tpl');
            return;
        }
        
        // Getting next state
        $workflowNext = new Workflow($node->nodeType->getWorkflow(), $nextState);
        $nextStateName = $workflowNext->getStatusName();
        
        // Loading Notifications default values
        $conf = Manager::file('/conf/notifications.php', 'XIMDEX');
        $defaultMessage = $this->buildMessage($conf['defaultMessage'], $nextStateName, $node->get('Name'));
        $values = array(
            'group_state_info' => Group::getSelectableGroupsInfo($idNode),
            'state' => $nextStateName,
            'stateid' => $nextState,
            'required' => $conf['required'] === true ? 1 : 0,
            'defaultMessage' => $defaultMessage,
            'idNode' => $idNode,
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'name' => $node->GetNodeName()
        );
        if ($workflowNext->isFinalState()) {
            $user = new User(Ximdex\Runtime\Session::get('userID'));
            if (! $user->hasPermission('structural_publication') and $node->nodeType->getIsStructuredDocument()) {
                $this->messages->add(_('You can not publish a structured document'), MSG_TYPE_WARNING);
                $this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
                return;
            }
            $values = array_merge(array(
                'go_method' => 'publicateNode',
                'hasDisabledFunctions' => $this->hasDisabledFunctions(),
                'globalForcedEnabled' => FORCE_PUBLICATION,
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName()
            ), $values);
            $values = array_merge($values, $this->buildExtraValues($idNode));
            $this->render($values, null, 'default-3.0.tpl');
        } else {
            $defaultMessage = $this->buildMessage($conf['defaultMessage'], _('next'), $node->get('Name'));
            
            // Set default Message
            $values = array_merge(array(
                'go_method' => 'publicateForm',
                'allowedstates' => $AllowedStates,
                'nextStateName' => $nextStateName,
                'currentStateName' => $workflow->getStatusName(),
                'defaultMessage' => $defaultMessage
            ), $values);
            $this->render($values, 'next_state.tpl', 'default-3.0.tpl');
        }
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

    private function hasDisabledFunctions()
    {
        $serverConfig = new ServerConfig();
        return $serverConfig->hasDisabledFunctions();
    }

    /**
     * Load the form to forward to a later state
     *
     * Request params:
     *
     * * nodeid:
     * * nextstate: idState
     * * sendNotifications : Boolean
     * * users: To notificate
     * * stateid: Current node state
     * * texttosend: into notification
     */
    public function publicateForm()
    {
        // Loading request params
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $user = new User(Ximdex\Runtime\Session::get('userID'));
        if (! $user->hasPermission('structural_publication') and $node->nodeType->getIsStructuredDocument()) {
            $this->messages->add(_('You can not publish a structured document'), MSG_TYPE_WARNING);
            $this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
            return;
        }
        $nextState = $this->request->getParam('nextstate');
        $conf = Manager::file('/conf/notifications.php', 'XIMDEX');
        $workflow = new Workflow($node->nodeType->getWorkflow(), $nextState);
        $sendNotifications = $this->request->getParam('sendNotifications');
        $notificableUsers = $this->request->getParam('users');
        $idState = (int) $this->request->getParam('nextstate');
        $texttosend = $this->request->getParam('texttosend');
        
        // If must send notifications
        if ((bool) $sendNotifications) {
            $sent = $this->sendNotification($idNode, $idState, $notificableUsers, $texttosend);
            if (! $sent) {
                $values = array(
                    'goback' => true,
                    'messages' => $this->messages->messages,
                    'nodeTypeID' => $node->nodeType->getID(),
                    'node_Type' => $node->nodeType->GetName()
                );
                $this->render($values, 'show_results', 'default-3.0.tpl');
                return false;
            }
        }
        
        // If the next state is final state, it must be publication, so we move to publicateForm
        if ($workflow->isFinalState()) {
            $this->addJs('/actions/workflow_forward/resources/js/workflow_forward.js');
            $defaultMessage = $this->buildMessage($conf['defaultMessage'], $workflow->getStatusName(), $node->GetNodeName());
            $values = array(
                'group_state_info' => Group::getSelectableGroupsInfo($idNode),
                'go_method' => 'publicateNode',
                'state' => $workflow->getStatusName(),
                'required' => $conf['required'] === true ? 1 : 0,
                'defaultMessage' => $defaultMessage,
                'hasDisabledFunctions' => $this->hasDisabledFunctions(),
                'stateid' => $idState,
                'globalForcedEnabled' => FORCE_PUBLICATION,
                'name' => $node->GetNodeName(),
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName()
            );
            $values = array_merge($values, $this->buildExtraValues($idNode));
            $this->render($values, 'index.tpl', 'default-3.0.tpl');
        } else {
            
            // If the next state is not the final, we show a success message
            if ($node->setState($nextState) === false) {
                $values = array(
                    'goback' => true,
                    'messages' => $node->messages->messages,
                    'nodeTypeID' => $node->nodeType->getID(),
                    'node_Type' => $node->nodeType->GetName()
                );
                $this->render($values, 'show_results', 'default-3.0.tpl');
            } else {
                $values = array(
                    'go_method' => 'publicateForm',
                    'nextState' => $nextState,
                    'currentState' => $node->GetState(),
                    'nodeTypeID' => $node->nodeType->getID(),
                    'node_Type' => $node->nodeType->GetName()
                );
                $this->addCss('/actions/workflow_forward/resources/css/style.css');
                $this->render($values, 'success.tpl', 'default-3.0.tpl');
            }
        }
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
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $user = new User(Ximdex\Runtime\Session::get('userID'));
        if (! $user->hasPermission('structural_publication') and $node->nodeType->getIsStructuredDocument()) {
            $this->messages->add(_('You can not publish a structured document'), MSG_TYPE_WARNING);
            $this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
            return;
        }
        $structure = $this->request->getParam('no_structure') ? false : true;
        
        // The publication times are in milliseconds
        $dateUp = (int) $this->request->getParam('dateUp_timestamp');
        $dateDown = (int) $this->request->getParam('dateDown_timestamp');
        $up = (! is_null($dateUp) && $dateUp != '') ? $dateUp / 1000 : time();
        $down = (! is_null($dateDown) && $dateDown != '') ? $dateDown / 1000 : null;
        if ($down and $down <= ($up + 58)) {
            $this->messages->add('Expiration date has to be later than beginning one', MSG_TYPE_ERROR);
            $values = array('messages' => $this->messages->messages);
            $this->sendJSON($values);
        }
        $markEnd = $this->request->getParam('markend') ? true : false;
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
        $sendNotifications = (bool) $this->request->getParam('sendNotifications');
        $notificableUsers = $this->request->getParam('users') ?? [];
        $idState = (int) $this->request->getParam('stateid');
        $texttosend = $this->request->getParam('texttosend') ?? '';
        $lastPublished = $this->request->getParam('latest') ? false : true;
        $useCache = $this->request->getParam('use_cache') ? true : false;
        Logger::debug('ADDSECTION publicateNode PRE');
        $this->sendToPublish($idNode, $up, $down, $markEnd, $force, $structure, $deepLevel, $sendNotifications, $notificableUsers, $idState
            , $texttosend, $lastPublished, $useCache);
    }

    /**
     * Print a JSON object with users of the selected group
     * Called from an ajax request
     * 
     * @return void
     */
    public function notificableUsers()
    {
        $idGroup = (int) $this->request->getParam('groupid');
        $idState = (int) $this->request->getParam('stateid');
        $idNode = (int) $this->request->getParam('nodeid');
        if ($idNode) {
            $node = new Node($idNode);
            $workflow = new Workflow($node->nodeType->getWorkflow(), $idState);
        } else {
            $workflow = new Workflow();
        }
        $values = array(
            'messages' => array(
                _('You do not belong to any group with publication privileges')
            )
        );
        $group = new Group($idGroup);
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
                'stateName' => $workflow->getStatusName(),
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
    private function getPublicationIntervals(int $idNode)
    {
        $nodesToPublish = new NodesToPublish();
        $intervals = $nodesToPublish->getIntervals($idNode);
        return $this->formatInterval($intervals);
    }

    /**
     * Format the date intevals array
     *
     * @param array $gaps
     * @return array With info about the available gaps
     */
    private function formatInterval(array $gaps)
    {
        $gapInfo = array();
        if (count($gaps) > 0) {
            foreach ($gaps as $gap) {
                $gapInfo[] = array(
                    'BEGIN_DATE' => strftime('%d/%m/%Y %H:%M:%S', $gap['start']),
                    'END_DATE' => isset($gap['end']) ? strftime('%d/%m/%Y %H:%M:%S', $gap['end']) : null
                );
            }
        }
        return $gapInfo;
    }

    /**
     * Build params for the value array
     * 
     * @param int $idNode : the current Node
     * @return array
     */
    private function buildExtraValues(int $idNode)
    {
        setlocale(LC_TIME, 'es_ES');
        $idUser = \Ximdex\Runtime\Session::get('userID');
        $user = new User($idUser);
        $node = new Node($idNode);
        $nodeTypeName = $node->nodeType->GetName();
        $gapInfo = $this->getPublicationIntervals($idNode);
        return array(
            'gap_info' => $gapInfo,
            'has_unlimited_life_time' => SynchroFacade::HasUnlimitedLifeTime($idNode),
            'timestamp_from' => time(),
            'advanced_publication' => $user->hasPermission('advanced_publication') ? '1' : '0',
            'nodetypename' => $nodeTypeName,
            'show_rep_option' => true
        );
    }

    /**
     * Sends notifications and sets node state
     * Called from publicateNode
     *
     * @param int $idNode
     * @param int $idState : target state to promote the node
     * @return bool
     */
    private function promoteNode(int $idNode, int $idState) : bool
    {
        $node = new Node($idNode);
        $result = $node->SetState($idState);
        if ($result) {
            $this->messages->add(_('State has been successfully changed'), MSG_TYPE_NOTICE);
            return true;
        }
        $this->messages->mergeMessages($node->messages);
        return false;
    }

    /**
     * Sends notifications and sets node state
     * Called from publicateNode
     *
     * @param int $idNode : Node id
     * @param int $idState : Target state in workflow
     * @param array<int> $userList : Array with id of users to notificate
     * @param string $texttosend : Text to send in notification mail
     * @return boolean true if the notification is sended
     */
    private function sendNotification(int $idNode, int $idState, array $userList, string $texttosend = '')
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
        $idUser = \Ximdex\Runtime\Session::get('userID');
        $node = new Node($idNode);
        $idActualState = $node->get('IdState');
        $actualWorkflowStatus = new Workflow($node->nodeType->getWorkflow(), $idActualState);
        $nextWorkflowStatus = new Workflow($node->nodeType->getWorkflow(), $idState);
        $user = new User($idUser);
        $userName = $user->get('Name');
        $nodeName = $node->get('Name');
        $nodePath = $node->GetPath();
        $nextStateName = $nextWorkflowStatus->getStatusName();
        $actualStateName = $actualWorkflowStatus->getStatusName();
        $subject = _('Ximdex CMS: new state for document:') . ' ' . $nodeName;
        $content = _('State forward notification.') . "\n\n" . _('The user') . ' ' . $userName . ' ' . _('has changed the state of') 
            . ' ' . $nodeName . "\n\n" . _('Full Ximdex path') . ' --> ' . $nodePath . "\n\n" . _('Initial state') . ' --> ' 
            . $actualStateName . "\n" . _('Final state') . ' --> ' . $nextStateName . "\n\n\n" . _('Comment') . ':' . "\n" 
            . $texttosend . "\n\n";
        parent::sendNotifications($subject, $content, $userList);
        return true;
    }

    private function sendToPublish(int $idNode, int $up, ?int $down, bool $markEnd, bool $force, bool $structure, int $deepLevel
        , bool $sendNotifications, array $notificableUsers, int $idState, string $texttosend, bool $lastPublished, bool $useCache = true)
    {
        Logger::debug('ADDSECTION publicateNode sendToPublish parent');
        $this->addJs('/actions/workflow_forward/resources/js/workflow_forward.js');
        
        // If send notifications
        if ((boolean) $sendNotifications) {
            $sent = $this->sendNotification($idNode, $idState, $notificableUsers, $texttosend);
            if (! $sent) {
                $values = array(
                    'goback' => true,
                    'messages' => $this->messages->messages
                );
                $this->render($values, 'show_results', 'default-3.0.tpl');
                return false;
            }
        }
        
        // Move the node to next state
        if (! $this->promoteNode($idNode, $idState)) {
            $values = array(
                'goback' => true,
                'messages' => $this->messages->messages
            );
            $this->render($values, 'show_results', 'default-3.0.tpl');
            return false;
        }
        $node = new Node($idNode);
        $flagsPublication = $this->buildFlagsPublication($markEnd, $structure, $deepLevel, $force, $lastPublished, $useCache);
        
        // Adding node to NodesToPublish
        $syncFac = new SynchroFacade();
        $result = $syncFac->pushDocInPublishingPool($idNode, $up, $down, $flagsPublication);
        $arrayOpciones = array(
            'ok' => _(' were successfully published'),
            'notok' => _(' were not published, due to some error along the process'),
            'unchanged' => _(' were not published because they are already published in its latest version')
        );
        $valuesToShow = array();
        $keysOpciones = array_keys($arrayOpciones);
        foreach ($keysOpciones as $idOpcion) {
            if (array_key_exists($idOpcion, $result)) {
                foreach ($result[$idOpcion] as $idNode => $physicalServer) {
                    if (gettype($idNode) == 'string') {
                        $idNode = intval(str_replace('#', '', $idNode));
                    }
                    $nodePublished = new Node($idNode);
                    foreach ($physicalServer as $idPhysicalServer => $channel) {
                        $server = new Server($idPhysicalServer);
                        $keys = array_keys($channel);
                        foreach ($keys as $idChannel) {
                            $channel = new Node($idChannel);
                            $valuesToShow[$idOpcion][] = array(
                                'NODE' => $nodePublished->get('Name'),
                                'PATH' => $nodePublished->GetPath(),
                                'SERVER' => $server->get('Description'),
                                'CHANNEL' => $channel->get('Name')
                            );
                        }
                    }
                }
            }
        }
        $node = new Node($idNode);
        $workflow = new Workflow($node->nodeType->getWorkflow());
        $firstState = $workflow->getInitialState();
        if ($node->SetState($firstState) === false) {
            $values = array(
                'goback' => true,
                'messages' => $node->messages->messages,
                'nodeTypeID' => $node->nodeType->getID(),
                'node_Type' => $node->nodeType->GetName()
            );
            $this->render($values, 'show_results', 'default-3.0.tpl');
            return false;
        }
        Logger::debug('ADDSECTION sendToPublish pre render');
        $values = array(
            'options' => $arrayOpciones,
            'result' => $valuesToShow,
            'messages' => $this->messages->messages,
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName()
        );
        Logger::debug('ADDSECTION sendToPublish pre render else value: ' . print_r($values, true));
        $this->render($values, 'show_results', 'default-3.0.tpl');
        return true;
    }

    private function buildFlagsPublication(bool $markEnd, bool $structure = true, int $deepLevel = 1, bool $force = false
        , bool $lastPublished = false, bool $useCache = true)
    {
        // Creating flags to publicate
        $flagsPublication = array(
            'markEnd' => $markEnd,
            'structure' => $structure,
            'deeplevel' => $deepLevel,
            'force' => $force,
            'recurrence' => false,
            'workflow' => true,
            'lastPublished' => $lastPublished,
            'useCache' => $useCache
        );
        return $flagsPublication;
    }

    /**
     * Validate if a node can be forwarded
     *
     * @param int $idNode : The selected node identificator.
     * @return boolean : true if the node exists and has a valid server and it is not a dependant node (Shared Workflow).
     */
    private function validateInIndex(int $idNode)
    {
        // Check if idnode exist
        $node = new Node($idNode);
        if (! $node->get('IdNode')) {
            $this->messages->add(_('The node could not be loaded'), MSG_TYPE_ERROR);
            return false;
        }
        
        // Get Server node for selected node
        $idServer = $node->getServer();
        $serverNode = new Node($idServer);
        
        // Validate Servers. Must exist like node and server
        if (! $serverNode->get('IdNode')) {
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
        
        // If the workflow is dependant on other node
        $idWorkFlowSlave = $node->get('SharedWorkflow');
        if ($idWorkFlowSlave) {
            $masterNode = new Node($idWorkFlowSlave);
            $values = array(
                'path_master' => $masterNode->GetPath()
            );
            $this->render($values, 'linked_document', 'default-3.0.tpl');
            return false;
        }
        return true;
    }

    private function validateInSelectedGroup(Group $group, Workflow $workflow, int $idState, int $idGroup)
    {
        if (! $group->get('IdGroup')) {
            $this->messages->add(sprintf(_('No information about the selected group (%s) could be obtained'), $idGroup), MSG_TYPE_ERROR);
        }
        if (! $workflow->getStatusID()) {
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
