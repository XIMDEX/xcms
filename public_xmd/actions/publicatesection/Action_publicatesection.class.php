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

class Action_publicatesection extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = (int)$this->request->getParam("nodeid");
        $node = new Node($idNode);
        $nodeTypeName = $node->nodeType->GetName();
        $nodeType = New NodeType();
        $publishabledNodeTypes = $nodeType->find('IdNodeType, Description', 'IsPublishable is true and IsFolder is false'
            , null, true, true, null, 'Description');
        $values = array(
            'go_method' => 'publicate_section',
            'publishabledtypes' => $publishabledNodeTypes,
            'synchronizer_to_use' => \Ximdex\Modules\Manager::isEnabled('ximSYNC') ? 'ximSYNC' : 'default',
            'ximpublish_tools_enabled' => \Ximdex\Modules\Manager::isEnabled('ximPUBLISHtools'),
            'folderType' => $node->nodeType->getID() == NodeTypeConstants::SERVER ? 'server' : 'section',
            'name' => $node->GetNodeName(),
            'timestamp_from' => time(),
            'has_unlimited_life_time' => SynchroFacade::HasUnlimitedLifeTime($idNode),
            'gap_info' => $this->getPublicationIntervals($idNode)
        );
        $serverID = $node->getServer();
        $nodeServer = new Node($serverID);
        $nameServer = $nodeServer->get('Name');
        $physicalServers = $nodeServer->class->GetPhysicalServerList(true);
        if (!(sizeof($physicalServers) > 0)) {
            $this->messages->add(sprintf(_("There is not any defined physical server in: '%s'"), $nameServer), MSG_TYPE_ERROR);
            $values['messages'] = $this->messages->messages;
        }
        $serverConfig = new ServerConfig();
        $values['hasDisabledFunctions'] = $serverConfig->hasDisabledFunctions();
        
        // Loading Notifications default values
        $conf = \Ximdex\Modules\Manager::file('/conf/notifications.php', 'XIMDEX');
        $defaultMessage = $this->buildMessage($conf["defaultSectionMessage"], $node->get('Name'));
        $values = $values + array(
            'group_state_info' => Group::getSelectableGroupsInfo($idNode),
            'required' => $conf['required'] === true ? 1 : 0,
            'defaultMessage' => $defaultMessage,
            'idNode' => $idNode,
            'name' => $node->GetNodeName()
        );
        $this->addJs('/actions/publicatesection/resources/js/index.js');
        $this->addCss('/actions/publicatesection/resources/css/style.css');
        $this->addCss('/assets/style/jquery/ximdex_theme/widgets/calendar/calendar.css');
        $this->render($values, NULL, 'default-3.0.tpl');
    }

    public function publicate_section()
    {
        $idNode = (int)$this->request->getParam('nodeid');
        $levels = $this->request->getParam('levels');
        if ($levels == 'all') {
            
            // All subsections
            $level = null;
            $recurrence = true;
        }
        elseif ($levels == 'deep') {
            
            // N levels of depth
            $level = abs($this->request->getParam('deeplevel'));
            $recurrence = false;
        }
        else {
            
            // Zero levels, only the given section or node node
            $level = 1;
            $recurrence = false;
        }
        $forcePublication = $this->request->getParam('force') ? true : false;
        
        // Filter by specified node type
        if ($this->request->getParam('publishType')) {
            $type = (int) $this->request->getParam('types');
        }
        else {
            $type = null;
        }
        $noUseDrafts = $this->request->getParam('latest') ? false : true;
        $node = new Node($idNode);
        $nodename = $node->get('Name');
        $folderType = $node->nodeType->getID() == NodeTypeConstants::SERVER ? 'server' : 'section';
        
        // The publication times are in milliseconds.
        $dateUp = $this->request->getParam('dateUp_timestamp');
        $dateDown = $this->request->getParam('dateDown_timestamp');
        $up = (! is_null($dateUp) && $dateUp != "") ? $dateUp / 1000 : time();
        $down = (! is_null($dateDown) && $dateDown != "") ? $dateDown / 1000 : null;
        if ($down and $down <= $up) {
            $this->messages->add('Expiration date cannot be older than beginning one', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages);
            $this->render($values, 'index', 'default-3.0.tpl');
            return false;
        }
        $markEnd = $this->request->getParam('markend') ? true : false;
        $structure = $this->request->getParam('no_structure') ? false : true;
        
        // If send notifications
        $sendNotifications = $this->request->getParam('sendNotifications');
        if ((boolean) $sendNotifications) {
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
        
        // Publication flags
        $flagsPublication = array(
            'markEnd' => $markEnd,
            'linked' => true,
            'recurrence' => $recurrence,
            'childtype' => $type,
            'workflow' => false,
            'force' => $forcePublication,
            'lastPublished' => $noUseDrafts,
            'publicateSection' => true,
            'level' => $level,
            'structure' => $structure,
            'nodeType' => $type
        );
        $syncFac = new SynchroFacade();
        $result = $syncFac->pushDocInPublishingPool($idNode, $up, $down, $flagsPublication, $recurrence);
        $this->messages->add(sprintf(_("%s %s has been successfully sent to publish"), ucfirst($folderType), $nodename), MSG_TYPE_NOTICE);
        $values = array('messages' => $this->messages->messages);
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
        $values = array('messages' => array(_('You do not belong to any group with publication privileges')));
        $validateInGroup = $this->validateInSelectedGroup($group, $idGroup);
        if ($validateInGroup === false) {
            return false;
        }
        if ($group->get('IdGroup') > 0 && $validateInGroup) {
            $user = new User();
            $users = $user->GetAllUsers();
            $notificableUsers = array();
            if (! empty($users) && is_array($users)) {
                foreach ($users as $idUser) {
                    $user = new User($idUser);
                    $idRole = $user->GetRoleOnNode($idNode, $idGroup);
                    if (($idRole > 0)) {
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
                'notificableUsers' => $notificableUsers
            );
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
    private function getPublicationIntervals($idNode)
    {
        $nodesToPublish = new NodesToPublish();
        $intervals = $nodesToPublish->getIntervals(null, $idNode);
        return $this->formatInterval($intervals);
    }
    
    /**
     * Format the date intevals array
     *
     * @param $gaps
     * @return array With info about the available gaps
     */
    private function formatInterval($gaps)
    {
        $gapInfo = array();
        if (count($gaps) > 0) {
            foreach ($gaps as $gap) {
                $gapInfo[] = array(
                    'BEGIN_DATE' => strftime("%d/%m/%Y %H:%M:%S", $gap['start']),
                    'END_DATE' => isset($gap['end']) ? strftime("%d/%m/%Y %H:%M:%S", $gap['end']) : null,
                    'NODES' => isset($gap['nodes']) ? $gap['nodes'] : null
                );
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
            $this->render(array('messages' => $this->messages->messages), null, 'messages_in_progress_action.tpl');
            return false;
        }
        return true;
    }
    
    /**
     * Sends notifications and sets node state
     * Called from publicateNode
     *
     * @param int $idNode : Node id
     * @param array<int> $userList : Array with id of users to notificate.
     * @param string $texttosend : Text to send in notification mail.
     * @return boolean true if the notification is sended.
     */
    private function sendNotification($idNode, $userList, $texttosend = "")
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
        $subject = _("Ximdex CMS: Published section:") . " " . $nodeName;
        $content = _("The user") . " " . $userName . " " . _("has published the section")
            . " " . $nodeName . "\n" . "\n" . _("Full Ximdex path") . " --> " . $nodePath . "\n" . "\n" . _("Comment") . ":" 
            . "\n". $texttosend . "\n" . "\n";
        parent::sendNotifications($subject, $content, $userList);
        return true;
    }
}