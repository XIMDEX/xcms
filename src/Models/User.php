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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Runtime\App;
use XimdexApi\core\Token;
use Ximdex\Runtime\Session;
use Ximdex\Runtime\Constants;
use Ximdex\Models\ORM\UsersOrm;
use Ximdex\Models\ORM\RelUsersGroupsOrm;
use Ximdex\Models\ORM\RelRolesActionsOrm;
use Ximdex\NodeTypes\NodeTypeConstants;

class User extends UsersOrm
{
    const XIMDEX_ID = 301;
    
    /**
     * Error code
     * 
     * @var int
     */
    public $numErr;
    
    /**
     * Error message
     * 
     * @var string
     */
    public $msgErr;
    
    /**
     * Error list
     * 
     * @var array
     */
    public $errorList = [];

    public function __construct(int $id = null)
    {
        $this->errorList[1] = _('User does not exist');
        $this->errorList[2] = _('An user with this login already exists');
        $this->errorList[3] = _('Arguments missing');
        $this->errorList[4] = _('Some of the connections with your groups could not be deleted');
        $this->errorList[5] = _('Database connection error');
        $this->errorList[6] = _('Error in associated object');
        $this->errorList[7] = _('Role for group missing');
        parent::__construct($id);
    }

    /**
     * Class constructor function. If a param is received, $idUser is initialized.
     * 
     * @return array|boolean
     */
    public function getAllUsers()
    {
        return $this->find('IdUser', null, null, MONO, true, null, 'Name');
    }

    /**
     * This function returns all the groups belonging to an user
     * 
     * @return array|NULL
     */
    public function getGroupList()
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $sql = sprintf('SELECT IdGroup FROM RelUsersGroups WHERE IdUser = %d', $this->get('IdUser'));
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query($sql);
            if (! $dbObj->numErr) {
                $salida = array();
                while (! $dbObj->EOF) {
                    $salida[] = $dbObj->getValue('IdGroup');
                    $dbObj->next();
                }
                return $salida;
            }
            setError(5);
        } else {
            $this->SetError(1);
        }
        return null;
    }

    /**
     * Returns an obgect idUser
     * 
     * @return boolean|string
     */
    public function getID()
    {
        $this->clearError();
        return $this->get('IdUser');
    }

    /**
     * Returns the user name associated to an idUser
     * 
     * @return boolean|string
     */
    public function getRealName()
    {
        return $this->get('Name');
    }

    /**
     * Returns the user login
     * 
     * @return boolean|string
     */
    public function getLogin()
    {
        return $this->get('Login');
    }

    /**
     * Returns de user email
     * 
     * @return boolean|string
     */
    public function getEmail()
    {
        return $this->get('Email');
    }

    /**
     * Returns user locale
     * 
     * @return boolean|string
     */
    public function getLocale()
    {
        return $this->get('Locale');
    }

    /**
     * Returns user's number of access
     * 
     * @return boolean|string
     */
    public function getNumAccess()
    {
        return $this->get('NumAccess');
    }

    /**
     * It allows to change an object idUser. It avoid to have to destroy and create again
     * 
     * @param int $id
     * @return NULL|boolean|string
     */
    public function setID(int $id)
    {
        parent::__construct($id);
        if (! $this->get('IdUser')) {
            $this->setError(1);
            return null;
        }
        return $this->get('IdUser');
    }

    /**
     * Updating database with the new user name
     * 
     * @param string $name
     * @return boolean|string|NULL|number
     */
    public function setRealName(string $name)
    {
        if (! $this->get('IdUser')) {
            $this->setError(1);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return (bool) $this->update();
        }
        return false;
    }

    /**
     * Modifies the user pass
     * 
     * @param string $pass
     * @return boolean|boolean|string|NULL|number
     */
    public function setPassword(string $pass)
    {
        if (! $this->get('IdUser')) {
            $this->setError(1);
            return false;
        }
        $result = $this->set('Pass', $pass);
        if ($result) {
            return (bool) $this->update();
        }
        return false;
    }

    /**
     * Modifies the user email
     * 
     * @param string $email
     * @return boolean|string|NULL|number
     */
    public function setEmail(string $email)
    {
        if (! $this->get('IdUser')) {
            $this->setError(1);
            return false;
        }
        $result = $this->set('Email', $email);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function setLocale(string $code = null)
    {
        if (! $this->get('IdUser')) {
            $this->setError(1);
            return false;
        }
        $result = $this->set('Locale', $code);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Looks for an user by login, and load the corresponding idUser
     * 
     * @param string $login
     * @return NULL|string|boolean
     */
    public function setByLogin(string $login)
    {
        $this->clearError();
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdUser FROM Users WHERE Login = %s', $dbObj->sqlEscapeString($login));
        $dbObj->query($query);
        if ($dbObj->numRows) {
            return $this->setID($dbObj->getValue('IdUser'));
        }
        $this->setError(5);
        return false;
    }

    /**
     * Function which returns true if an user is in a node
     * 
     * @param int $nodeID
     * @param bool $ignoreGeneralGroup
     * @return boolean
     */
    public function isOnNode(int $nodeID, bool $ignoreGeneralGroup = false)
    {
        if (is_array($nodeID)) {
            $nodeID = $nodeID['id'];
        }
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            $node = new Node($nodeID);
            if ($node->get('IdNode') > 0) {
                $userList = array();
                $userList = $node->getUserList($ignoreGeneralGroup);
                if (in_array($this->get('IdUser'), $userList)) {
                    return true;
                }
            }
        } else {
            $this->setError(1);
        }
        return false;
    }

    /**
     * Function which retrieve a list of roles of an user in a determined node
     * 
     * @param int $nodeID
     * @return array|boolean
     */
    public function getRolesOnNode(int $nodeID)
    {
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            $groupList = $this->getGroupListOnNode($nodeID);
            if (! empty($groupList)) {
                $roleList = [];
                foreach ($groupList as $idGroup) {
                    $group = new Group($idGroup);
                    $role = $group->getRoleOnNode($nodeID);
                    if ($role) {
                        $roleList[] = $role;
                    } else {
                        $role = $this->getRoleOnGroup($idGroup);
                        if ($role) {
                            $roleList[] = $role;
                        }
                    }
                }
                if (! empty($roleList) && is_array($roleList)) {
                    $roleList = array_unique($roleList);
                }
                return $roleList;
            }
        } else {
            $this->setError(1);
        }
        return false;
    }

    public function hasPermissionOnNode(int $nodeID, string $pName)
    {
        $roles = $this->getRolesOnNode($nodeID);
        $permission = new Permission();
        $permission->setByName($pName);
        $pID = $permission->getID();
        if ($roles) {
            foreach ($roles as $idRol) {
                $role = new Role($idRol);
                $permissionList = $role->getPermissionsList();
                if ($permissionList) {
                    if (in_array($pID, $permissionList)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function hasPermissionOnGroup(int $groupID, string $pName)
    {
        $permission = new Permission();
        $permission->setByName($pName);
        if (! $permission->getID()) {
            $this->messages->add(_('Permission') . " {$pName} " . ('does not exists'), MSG_TYPE_ERROR);
            return false;
        }
        if (! $roleId = $this->getRoleOnGroup($groupID)) {
            return false;
        }
        $role = new Role($roleId);
        $permissionList = $role->getPermissionsList();
        if (! empty($permissionList)) {
            if (in_array($permission->getID(), $permissionList)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $pName)
    {
        $groupID = Group::getGeneralGroup();
        return $this->hasPermissionOnGroup($groupID, $pName);
    }

    /**
     * Check perms with name $pname in all user groups of $nodeID
     * 
     * @param string $pName
     * @param int $nodeID
     * @return boolean
     */
    public function hasPermissionInNode(string $pName, int $nodeID)
    {
        $groups = $this->GetGroupListOnNode($nodeID);
        if (! empty($groups)) {
            foreach ($groups as $_group) {
                if ($this->HasPermissionOnGroup($_group, $pName)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Function which returns true if an user is in a node
     * 
     * @param int $groupID
     * @return boolean
     */
    public function isOnGroup(int $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d', $this->get('IdUser'), $groupID);
            $dbObj->Query($query);
            if (! $dbObj->numErr) {
                if ($dbObj->numRows) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Function which returns the role of an user in a group
     * 
     * @param int $groupid
     * @return boolean|string|NULL
     */
    public function getRoleOnGroup(int $groupid)
    {
        $this->clearError();
        if (! is_null($groupid)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT IdRole FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d', $this->get('IdUser'), $groupid);
            $dbObj->query($query);
            if ($dbObj->numRows > 0) {
                return (int) $dbObj->getValue('IdRole');
            }
        }
        return null;
    }

    public function getRoles()
    {
        $this->ClearError();
        $query = sprintf('SELECT DISTINCT IdRole FROM RelUsersGroups WHERE IdUser = %d group by IdRole', $this->get('IdUser'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($query);
        $roles = array();
        while (! $dbObj->EOF) {
            $roles[] = $dbObj->GetValue('IdRole');
            $dbObj->Next();
        }
        return $roles;
    }

    /**
     * Function which returns true if the indicated pass is correct
     * 
     * @param string $pass
     * @return boolean
     */
    public function checkPassword(string $pass)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT Pass FROM Users WHERE IdUser = %d', $this->get('IdUser'));
            $dbObj->Query($query);
            if (! strcmp(md5($pass), $dbObj->GetValue('Pass'))) {
                return true;
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    /**
     * Function which returns the list of groups subscribed by a node belonging to the user
     * 
     * @param int $nodeID
     * @return array
     */
    public function getGroupListOnNode(int $nodeID)
    {
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            $userGroups = $this->getGroupList();
            $node = new Node($nodeID);
            $nodeGroups = $node->getGroupList();
            if ($node->get('IdNode') > 0 && is_array($userGroups) && is_array($nodeGroups)) {
                return array_intersect($userGroups, $nodeGroups);
            }
            $this->setError(6);
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Function which retrieve a list of actions of an user for given node and group
     * 
     * @param int $nodeID
     * @param int $groupID
     * @return array|boolean
     */
    public function getToolbarOnNode(int $nodeID, int $groupID)
    {
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (! is_null($roleID)) {
                $role = new Role($roleID);
                $actions = $role->getActionsOnNode($nodeID);
                if (! $role->numErr) {
                    return $actions;
                }
                $this->SetError(6);
            } else {
                $this->SetError(7);
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    /**
     * Function which returns the list of actions of an user for given node and group
     * 
     * @param int $nodeID
     * @param int $groupID
     * @return NULL|string|boolean
     */
    public function getRoleOnNode(int $nodeID, int $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (! is_null($roleID)) {
                $group = new Group($groupID);
                $roleGroupID = $group->GetRoleOnNode($nodeID);
                if ($roleGroupID) {
                    return $roleGroupID;
                }
                return $roleID;
            } else {
                $this->SetError(7);
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    /**
     * Function which returns an array of tollbars for each subscribed group
     * 
     * @param int $nodeID
     * @return array|boolean
     */
    public function getToolbarsOnNode(int $nodeID)
    {
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            $groupIDs = $this->GetGroupListOnNode($nodeID);
            $salida = [];
            if (! $this->numErr) {
                foreach ($groupIDs as $groupID) {
                    $salida[] = $this->GetToolbarOnNode($nodeID, $groupID);
                }
            }
            if (! $this->numErr) {
                return $salida;
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add(bool $useAutoIncrement = true)
    {
        return $this->createNewUser($this->get('Name'), $this->get('Login'), $this->get('Pass'), $this->get('Name'), $this->get('Email')
            , $this->get('Locale'));
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::set()
     */
    public function set(string $attrib, string $value = null) : bool
    {
        if ($attrib == 'Pass')
            $value = md5($value);
        return parent::set($attrib, $value);
    }

    /**
     * Function which creates a new user if it does not exist in the system previously, and load the idUser
     * 
     * @param $realname
     * @param $login
     * @param $pass
     * @param $email
     * @param $locale
     * @param $roleID
     * @param $idUser
     * @return NULL|boolean|string
     */
    public function createNewUser(string $realname, string $login, string $pass, string $email, string $locale = null, int $roleID
        , int $idUser = null)
    {
        if (is_null($idUser)) {
            Logger::error('The node must be previously created');
            return false;
        }
        $this->set('IdUser', $idUser);
        $this->set('Login', $login);
        $this->set('Pass', $pass);
        $this->set('Name', $realname);
        $this->set('Email', $email);
        $this->set('Locale', $locale);
        $this->set('NumAccess', 0);
        if (! parent::add()) {
            Logger::error('Error in User persistence for ' . $idUser);
            return null;
        }
        $group = new Group();
        $group->setGeneralGroup();
        $group->addUserWithRole($idUser, $roleID);
        return $this->get('IdUser');
    }

    public function delete()
    {
        $this->deleteUser();
    }

    /**
     * Function which delete the current user
     */
    public function deleteUser()
    {
        $this->clearError();
        if ($this->get('IdUser') > 0) {
            return parent::delete();
        }
        $this->setError(1);
        return false;
    }

    /**
     * Cleaning class errors
     */
    public function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    /**
     * Loading a class error
     * 
     * @param int $code
     */
    public function setError(int $code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // Returning true if the class has produced an error
    public function hasError()
    {
        return ($this->numErr != null);
    }

    public function afterLogin()
    {
        $numAccess = $this->NumAccess;
        $numAccess++;
        $this->set('NumAccess', $numAccess);
        $this->set('LastLogin', time());
        $this->update();
    }

    public function getLastestDocs()
    {
        $docs = array();
        $v = new Version();
        $docs = $v->getLastestDocsByUser($this->GetID());
        return $docs;
    }

    /**
     * Get an array with allowed actions on a node
     * 
     * @param int $idNode
     * @param bool $includeActionsWithNegativeSort
     * @return array
     * @since Ximdex 3.6
     */
    public function getActionsOnNode(int $idNode, bool $includeActionsWithNegativeSort = false)
    {
        $result = array();

        // Getting no specific not allowed actions for $idNode
        $noActionsInNode = new NoActionsInNode();
        $arrayForbiddenActions = $noActionsInNode->getForbiddenActions($idNode);

        /**
         * To get the actions the steps are:
         * 1. Get the Groups on node for the current User
         * 2. For every group, the user roles
         * 3. For every role get the actions on idnode
         */

        // Getting groups for the user for $idNode
        $arrayGroups = $this->getGroupListOnNode($idNode);
        $arrayActions = array();
        if (! empty($arrayGroups)) {
            
            // Getting roles for the user for every group
            $arrayRoles = array();
            foreach ($arrayGroups as $idGroup) {
                if ($role = $this->getRoleOnGroup($idGroup)) {
                    if (! in_array($role, $arrayRoles)) {
                        $arrayRoles[] = $role;
                    }
                }
            }
            if (! empty($arrayRoles)) {
                
                // Getting actions for every rol
                foreach ($arrayRoles as $idRol) {
                    $role = new Role($idRol);
                    $arrayActions = array_merge($arrayActions, $role->getActionsOnNode($idNode, $includeActionsWithNegativeSort));
                }
            }
        }
        $arrayActions = array_unique($arrayActions);
        
        // Deleting not allowed actions from actions array.
        $result = array_diff($arrayActions, $arrayForbiddenActions);
        return $result;
    }

    /**
     * Calculates the posible actions for a group of nodes
     * It depends on roles, states and nodetypes of nodes
     * 
     * @param array $nodes IdNodes array
     * @return array IdActions array
     * @since Ximdex 3.6
     */
    public function getActionsOnNodeList(array $nodes)
    {
        $result = array();
        $nodes = array_unique($nodes);
        $actionsArray = array();
        
        // Get idactions for everynode
        foreach ($nodes as $idNode) {
            $aux = $this->getActionsOnNode($idNode);
            $actionsArray = array_merge($actionsArray, $aux);
        }

        /**
         * Actions can be different idactions and same command, params
         * and module, so we must group this actions.
         * The idActions returned have allowed commands for the selected
         * nodes.
         */
        if (count($nodes) > 1) {
            foreach ($actionsArray as $idAction) {
                $action = new Action($idAction);
                $command = $action->get('Command');
                $aliasModule = $action->get('Module') ? $action->get('Module') : 'nomodule';
                $aliasParam = $action->get('Params') ? $action->get('Params') : 'noparams';
                $founded = [];
                $founded[$command][$aliasModule][$aliasParam] = $founded[$command][$aliasModule][$aliasParam] + 1;
            }
        } else {
            return $actionsArray;
        }
        return $result;
    }

    /**
     * Check if the user can run the action for a node
     * 
     * @since Ximdex 3.6
     * @param int $idNode
     * @param int $idAction
     * @return boolean True if the action is allowed. Otherwise false
     */
    public function isAllowedAction(int $idNode, int $idAction)
    {
        // The action cant be specifically forbidden
        $noActionsInNode = new NoActionsInNode();
        if ($noActionsInNode->isActionForbiddenForNode($idNode, $idAction)) {
            return false;
        }
        $arrayActions = $this->getActionsOnNode($idNode, true);
        return in_array($idAction, $arrayActions);
    }

    public function login(string $name, string $password)
    {
        $this->setByLogin($name);
        if ($this->checkPassword($password)) {

            // Is a valid user !
            $user_id = $this->getID();
            $user = new user($user_id);
            $user_locale = $user->get('Locale');
            if (empty($user_locale))
                $user_locale =  App::getValue('locale');

            // STOPPER
            $stopperFilePath =  XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/login.stop';
            if ($user->getID() != User::XIMDEX_ID && file_exists($stopperFilePath)) {
                
                // login closed
                return false;
            }
            unset($user);

            // Add new session system
            Session::set('user_name', $name);
            Session::set('logged', $user_id);
            Session::set('userID', $user_id);
            Session::set('locale', $user_locale);
            Session::set('loginTimestamp', time());
            $session_info = session_get_cookie_params();
            $session_lifetime = $session_info['lifetime']; // session cookie lifetime in seconds
            $session_duration = $session_lifetime != 0 ? $session_lifetime : session_cache_expire() * 60;
            $loginTimestamp = Session::get('loginTimestamp');
            setcookie('loginTimestamp', $loginTimestamp, 0,  '/');
            setcookie('sessionLength', $session_duration , 0,  '/');
            return true;
        }
        return false;
    }

    public function logout()
    {
        Session::destroy();
    }

    public function hasAccess(int $nodeId)
    {
        if ($nodeId == Node::ID_XIMDEX || $nodeId == Node::ID_PROJECTS || $nodeId == Node::ID_CONTROL_CENTER 
                || $this->getID() == self::XIMDEX_ID) {
            return true;
        }
        $user_groups = $this->getGroupList();
        $generalGroup = [Group::getGeneralGroup()];
        $user_groups = array_diff($user_groups, $generalGroup);
        $node = new Node($nodeId);
        $node_groups = $node->getGroupList();
        $node_groups = array_diff($node_groups, $generalGroup);
        $rel_groups = array_intersect($user_groups, $node_groups);
        if (count($rel_groups) > 0 || $this->isOnNode($nodeId, true)) {
            return true;
        }
        return false;
    }

    public function canRead(array $params = null)
    {
        $wfParams = $this->parseParams($params);
        $nodeId = $wfParams['node_id'];
        if ($this->hasPermission('view all nodes')) {
            return true;
        }
        if ($this->hasAccess($nodeId)) {
            return true;
        }
        return false;
    }
    
    /**
     * Comprueba si un usuario puede escribir un nodo
     *
     * @param array $params array asociativo que debe contener las claves node_id o node_type
     * @return bool (true, si puede escribir, false en caso contrario)
     */
    public function canWrite(array $params) : bool
    {
        $wfParams = $this->parseParams($params);
        $workFlowId = null;
        if (isset($wfParams['node_id'])) {
            $nodeId = (int) $wfParams['node_id'];

            // Usuario ximdex
            if ($this->getID() == self::XIMDEX_ID) {
                return true;
            }
            if (! $this->hasAccess($nodeId)) {
                return false;
            }
            $node = new Node($nodeId);
            $workflow = new Workflow($node->nodeType->getWorkflow());
            $workFlowId = $workflow->get('id');
        } else {
            // Logger::warning('No node ID given in canWrite method');
            return false;
        }
        if (! isset($wfParams['node_type'])) {
            // return false;
            $nodeTypeId = $node->GetNodeType();
        } else {
            $nodeTypeId = (int) $wfParams['node_type'];
        }
        if ($this->checkContext($nodeTypeId, Constants::CREATE)) {
            return true;
        }

        // Check groups&roles and defined actions...
        $userRoles = $this->getRoles();
        if (! is_array($userRoles)) {
            return false;
        }
        $nodeType = new NodeType($nodeTypeId);
        $actionId = $nodeType->getConstructor();
        if (! $actionId) {
            Logger::warning(sprintf('The nodetype %d has no create action associated', $nodeTypeId));
            return false;
        }
        foreach ($userRoles as $userRole) {
            $role = new Role($userRole);
            if ($role->hasAction($actionId, $node->GetState(), $workFlowId)) {
                return true;
            }
        }

        // Not write action found for roles of userId
        return false;
    }

    public function canDelete(array $params)
    {
        // TODO extend relation table with delete actions/nodetypes mapping
        $wfParams = $this->parseParams($params);
        if (! isset($wfParams['node_type'])) {
            return false;
        }
        $nodeTypeId = (int)$wfParams['node_type'];
        if ($this->checkContext($nodeTypeId, Constants::DELETE)) {
            return true;
        }
        return $this->canWrite($params);
    }

    public function canModify(array $params)
    {
        // TODO extend relation table with modify actions/nodetypes mapping
        $wfParams = $this->parseParams($params);
        if (! isset($wfParams['node_type'])) {
            return false;
        }
        $nodeTypeId = (int)$wfParams['node_type'];
        if ($this->checkContext( $nodeTypeId, Constants::UPDATE)) {
            return true;
        }
        return $this->canWrite($params);
    }

    protected function parseParams(array $params = null)
    {
        $formedParams = array();
        if (is_array($params)) {
            if (isset($params['node_id']) && $params['node_id'] > 0) {
                $nodeId = (int)$params['node_id'];
                if (isset($params['node_type']) && $params['node_type'] > 0) {
                    $formedParams['node_id'] = $nodeId;
                    $formedParams['node_type'] = (int)$params['node_type'];
                } else {
                    $node = new Node($nodeId);
                    $idNodeType = $node->GetNodeType();
                    $formedParams['node_id'] = $nodeId;
                    $formedParams['node_type'] = $idNodeType;
                    unset($node);
                }
                return $formedParams;
            }
            if (isset($params['node_type']) && $params['node_type'] > 0) {
                $idNodeType = $params['node_type'];

                // TODO Check if is a valid nodetype
                $formedParams['node_type'] = $idNodeType;
                return $formedParams;
            }
        }
        return null;
    }

    protected function checkContext(int $idNodeType, string $mode)
    {
        $nodeTypeMode = new NodetypeMode();
        $idAction = $nodeTypeMode->getActionForOperation($idNodeType, $mode);
        if (! $idAction) {
            return false;
        }
        $relRolesActions = new RelRolesActionsOrm();
        $result = $relRolesActions->find('IdRol', 'IdAction = %s', array($idAction), MONO);
        $idRol = count($result) == 1 ? $result[0] : NULL;
        if (! $idRol) {
            return false;
        }
        $relUserGroup = new RelUsersGroupsOrm();
        $relations = $relUserGroup->find('IdRel', 'IdUser = %s AND IdRole = %s', array($this->getID(), $idRol), MONO);
        return (count($relations) > 0);
    }
    
    /**
     * Return current logged user object
     * 
     * @return User
     */
    public static function getMe() : User
    {
        $userId = (int) Session::get('userID');
        return new static($userId);
    }
    
    public static function getUsersByRole(int $idRol) : array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = 'SELECT DISTINCT IdUser FROM RelUsersGroups WHERE IdRole = ' . $idRol;
        $dbObj->Query($sql);
        if ($dbObj->getDesErr()) {
            throw new \Exception($dbObj->getDesErr());
        }
        $res = [];
        while (! $dbObj->EOF) {
            $res[] = $dbObj->GetValue('IdUser');
            $dbObj->Next();
        }
        return $res;
    }
    
    /**
     * Send email notifications to all administrator users
     *
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public static function sendNotifications(string $subject, string $message) : bool
    {
        try {
            $administrators = self::getUsersByRole(Role::ADMINISTRATOR);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        foreach ($administrators as $id) {
            $user = new static($id);
            if (! mail($user->getEmail(), $subject, $message)) {
                Logger::warning('Cannot send an email notification to ' . $user->getEmail());
            }
        }
        return true;
    }

    /**
     * Find user by given token
     *
     * @param string $token
     * @return User
     */
    public static function getByToken(string $token) : User
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $data = Token::decryptToken($token)['user'];
        $sql = "SELECT IdNode FROM Nodes WHERE Nodes.IdNodeType = " . NodeTypeConstants::USER . " AND Nodes.Name = '{$data}'";
        $dbObj->query($sql);
        if ($dbObj->getDesErr()) {
            throw new \Exception($dbObj->getDesErr());
        }
        return new static($dbObj->getValue('IdNode'));
    }
}
