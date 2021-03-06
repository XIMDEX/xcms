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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Helpers\ServerConfig;
use Ximdex\Models\Group;
use Ximdex\Models\Node;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Utils\Serializer;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Models\NodeType;
use Ximdex\Models\NodesToPublish;

class Action_expiresection extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $nodeType = New NodeType();
        $publishabledNodeTypes = $nodeType->find('IdNodeType, Description', 'IsPublishable is true and IsFolder is false', null, true, true
            , null, 'Description');
        $values = [
            'go_method' => 'expire_section',
            'publishabledtypes' => $publishabledNodeTypes,
            'folderType' => $node->nodeType->getID() == NodeTypeConstants::SERVER ? 'server' : 'section',
            'name' => $node->getNodeName(),
            'timestamp_from' => time(),
            'has_unlimited_life_time' => SynchroFacade::HasUnlimitedLifeTime($idNode),
            'gap_info' => $this->getPublicationIntervals($idNode)
        ];
        $serverID = $node->getServer();
        $nodeServer = new Node($serverID);
        $nameServer = $nodeServer->get('Name');
        $physicalServers = $nodeServer->class->getPhysicalServerList(true);
        if (! sizeof($physicalServers)) {
            $this->messages->add(sprintf(_("There is not any defined physical server in: '%s'"), $nameServer), MSG_TYPE_ERROR);
            $values['messages'] = $this->messages->messages;
        }
        $serverConfig = new ServerConfig();
        $values['hasDisabledFunctions'] = $serverConfig->hasDisabledFunctions();
        
        // Loading Notifications default values
        $conf = \Ximdex\Modules\Manager::file('/conf/notifications.php', 'XIMDEX');
        $defaultMessage = $this->buildMessage($conf['defaultSectionMessage'], $node->get('Name'));
        $values = $values + [
            'group_state_info' => Group::getSelectableGroupsInfo($idNode),
            'required' => $conf['required'] === true ? 1 : 0,
            'defaultMessage' => $defaultMessage,
            'idNode' => $idNode,
            'name' => $node->getNodeName(),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName()
        ];
        $this->addJs('/actions/expiresection/resources/js/index.js');
        $this->addCss('/actions/expiresection/resources/css/style.css');
        $this->render($values, NULL, 'default-3.0.tpl');
    }

    public function expire_section()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $dateDown = $this->request->getParam('dateDown_timestamp');
        $markEnd = $this->request->getParam('markend') ? true : false;
        $structure = $this->request->getParam('no_structure') ? false : true;
        $levels = $this->request->getParam('levels');
        if ($levels == 'all') {
            
            // All subsections
            $level = null;
            $recursive = true;
        }
        elseif ($levels == 'deep') {
            
            // N levels of depth
            $level = abs($this->request->getParam('deeplevel'));
            $recursive = false;
        }
        else {
            
            // Zero levels, only the given section or node node
            $level = 1;
            $recursive = false;
        }
        
        // Filter by specified node type
        if ($this->request->getParam('publishType')) {
            $type = (int) $this->request->getParam('types');
        }
        else {
            $type = null;
        }
        $node = new Node($idNode);
        $nodename = $node->get('Name');
        $folderType = $node->nodeType->getID() == NodeTypeConstants::SERVER ? 'server' : 'section';
        
        // The publication times are in milliseconds
        $down = (! is_null($dateDown) && $dateDown != '') ? $dateDown / 1000 : time();
        
        // If send notifications
        $sendNotifications = $this->request->getParam('sendNotifications');
        if ((boolean) $sendNotifications) {
            $notificableUsers = $this->request->getParam('users');
            $texttosend = $this->request->getParam('texttosend');
            $sent = $this->sendNotification($idNode, $notificableUsers, $texttosend);
            if (! $sent) {
                $values = [
                    'goback' => true,
                    'messages' => $this->messages->messages
                ];
                $this->render($values, 'show_results', 'default-3.0.tpl');
                return false;
            }
        }
        
        // Publication flags
        $flagsExpiration = [
            'markEnd' => $markEnd,
            'linked' => true,
            'recursive' => $recursive,
            'childtype' => $type,
            'workflow' => false,
            'expireSection' => true,
            'level' => $level,
            'structure' => $structure,
            'nodeType' => $type,
            'force' => true
        ];
        
        // Processing the expiration of related ones 
        $sync = new SynchroFacade();
        if ($sync->expire($node, $down, $flagsExpiration) === false) {
            $this->messages->add(sprintf(_('%s %s could not sent to expire'), ucfirst($folderType), $nodename), MSG_TYPE_ERROR);
        } else {
            $this->messages->add(sprintf(_('%s %s has been successfully sent to expire'), ucfirst($folderType), $nodename), MSG_TYPE_NOTICE);
        }
        $values = [
            'messages' => $this->messages->messages
        ];
        $this->sendJSON($values);
    }
    
    /**
     * Print a JSON object with users of the selected group
     * Called from an ajax request
     * 
     * @return boolean
     */
    public function notificableUsers()
    {
        $idGroup = $this->request->getParam('groupid');
        $idNode = $this->request->getParam('nodeid');
        $group = new Group($idGroup);
        $values = [
            'messages' => [
                _('You do not belong to any group with publication privileges')
            ]
        ];
        $validateInGroup = $this->validateInSelectedGroup($group, $idGroup);
        if ($validateInGroup === false) {
            return false;
        }
        if ($group->get('IdGroup') > 0 && $validateInGroup) {
            $user = new User();
            $users = $user->getAllUsers();
            $notificableUsers = [];
            if (! empty($users) && is_array($users)) {
                foreach ($users as $idUser) {
                    $user = new User($idUser);
                    $idRole = $user->getRoleOnNode($idNode, $idGroup);
                    if (($idRole > 0)) {
                        $notificableUsers[] = [
                            'idUser' => $idUser,
                            'userName' => $user->get('Name')
                        ];
                    }
                }
            }
            $values = [
                'group' => $idGroup,
                'groupName' => $group->get('Name'),
                'notificableUsers' => $notificableUsers
            ];
        }
        header('Content-type: application/json');
        $values = Serializer::encode(SZR_JSON, $values);
        echo $values;
    }
    
    /**
     * Replace %doc macro in default Message.
     * The message is getted from conf/notifications.php
     * 
     * @param string $message
     * @param string $nodeName
     * @return string with the text replaced.
     */
    private function buildMessage(string $message, string $nodeName)
    {
        $mesg = preg_replace('/%doc/', $nodeName, $message);
        return $mesg;
    }
    
    /**
     * Obtains the publication intervals
     *
     * @param int $idNode
     * @return array With info about the available gaps
     */
    private function getPublicationIntervals(int $idNode)
    {
        $nodesToPublish = new NodesToPublish();
        $intervals = $nodesToPublish->getIntervals(null, $idNode);
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
        $gapInfo = [];
        if (count($gaps) > 0) {
            foreach ($gaps as $gap) {
                $gapInfo[] = [
                    'BEGIN_DATE' => strftime('%d/%m/%Y %H:%M:%S', $gap['start']),
                    'END_DATE' => isset($gap['end']) ? strftime('%d/%m/%Y %H:%M:%S', $gap['end']) : null,
                    'NODES' => isset($gap['nodes']) ? $gap['nodes'] : null
                ];
            }
        }
        return $gapInfo;
    }
    
    /**
     * @param Group $group
     * @param int $idGroup
     * @return boolean
     */
    private function validateInSelectedGroup(Group $group, int $idGroup)
    {
        if (! $group->get('IdGroup') > 0) {
            $this->messages->add(sprintf(_('No information about the selected group (%s) could be obtained'), $idGroup), MSG_TYPE_ERROR);
        }
        if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
            $this->render([
                    'messages' => $this->messages->messages
                ], null, 'messages_in_progress_action.tpl');
            return false;
        }
        return true;
    }
    
    /**
     * Sends notifications and sets node state
     * Called from expireSection
     *
     * @param int $idNode : Node id
     * @param array<int> $userList : Array with id of users to notificate.
     * @param string $texttosend : Text to send in notification mail.
     * @return boolean true if the notification is sended.
     */
    private function sendNotification(int $idNode, array $userList, string $texttosend = '')
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
        $user = new User($idUser);
        $userName = $user->get('Name');
        $nodeName = $node->get('Name');
        $nodePath = $node->getPath();
        $subject = _('Ximdex CMS: Published section:') . ' ' . $nodeName;
        $content = _('The user') . ' ' . $userName . ' ' . _('has published the section')
            . ' ' . $nodeName . "\n\n" . _('Full Ximdex path') . ' --> ' . $nodePath . "\n\n" . _('Comment') . ':' 
            . "\n". $texttosend . "\n\n";
        parent::sendNotifications($subject, $content, $userList);
        return true;
    }
}
