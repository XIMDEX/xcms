<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

class User extends UsersOrm
{
    const ROLE_ADMINISTRATOR = 201;
    const XIMDEX_ID = 301;
    
    public $userID;
    public $numErr;    // Error code
    public $msgErr;    // Error message
    public $errorList; // Error list

    function __construct($params = NULL)
    {
        $this->errorList[1] = _('User does not exist');
        $this->errorList[2] = _('An user with this login already exists');
        $this->errorList[3] = _('Arguments missing');
        $this->errorList[4] = _('Some of the connections with your groups could not be deleted');
        $this->errorList[5] = _('Database connection error');
        $this->errorList[6] = _('Error in associated object');
        $this->errorList[7] = _('Role for group missing');
        parent::__construct($params);
    }

    // Class constructor function. If a param is received, $idUser is initialized.
    function getAllUsers()
    {
        return $this->find('IdUser', '1 ORDER BY Name', NULL, MONO);
    }

    // This function returns all the groups belonging to an user
    function getGroupList()
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf('SELECT IdGroup FROM RelUsersGroups WHERE IdUser = %d', $this->get('IdUser'));
            $dbObj->Query($sql);
            if (!$dbObj->numErr) {
                $salida = array();
                while (!$dbObj->EOF) {
                    $salida[] = $dbObj->GetValue('IdGroup');
                    $dbObj->Next();
                }
                return $salida;
            } else
                SetError(5);
        } else {
            $this->SetError(1);
        }
        return null;
    }

    // Returns an obgect idUser
    function getID()
    {
        $this->ClearError();
        return $this->get('IdUser');
    }

    /*     * ** getters *** */

    // Returns the user name associated to an idUser
    function getRealName()
    {
        return $this->get('Name');
    }

    // Returns the user login
    function getLogin()
    {
        return $this->get('Login');
    }

    // Returns de user email
    function getEmail()
    {
        return $this->get('Email');
    }

    // Returns user locale
    function getLocale()
    {
        return $this->get('Locale');
    }

    // Returns user's number of access
    function getNumAccess()
    {
        return $this->get('NumAccess');
    }

    /*     * ** setters *** */

    // It allows to change an object idUser. It avoid to have to destroy and create again
    function setID($id)
    {
        parent::__construct($id);
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return null;
        }
        return $this->get('IdUser');
    }

    // Updating database with the new user name
    function setRealName($name)
    {
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Modifies the user pass
    function setPassword($pass)
    {
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Pass', $pass);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Modifies the user email
    function setEmail($email)
    {
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Email', $email);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    function setLocale($code)
    {
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Locale', $code);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // looks for an user by login, and load the corresponding idUser
    function setByLogin($login)
    {
        $this->ClearError();
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdUser FROM Users WHERE Login = %s', $dbObj->sqlEscapeString($login));
        $dbObj->Query($query);
        if ($dbObj->numRows) {
            return $this->SetID($dbObj->GetValue('IdUser'));
        }
        $this->SetError(5);
        return false;
    }

    // Function which returns true if an user is in a node
    function isOnNode($nodeID, $ignoreGeneralGroup = null)
    {
        if (is_array($nodeID)) {
            $nodeID = $nodeID['id'];
        }
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $node = new Node($nodeID);
            if ($node->get('IdNode') > 0) {
                $userList = array();
                $userList = $node->GetUserList($ignoreGeneralGroup);
                if (in_array($this->get('IdUser'), $userList)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            $this->SetError(1);
        }
    }

    // Function which retrieve a list of roles of an user in a determined node
    function getRolesOnNode($nodeID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $groupList = $this->GetGroupListOnNode($nodeID);
            if (!empty($groupList)) {
                $roleList = [];
                foreach ($groupList as $idGroup) {
                    $group = new Group($idGroup);
                    $role = $group->GetRoleOnNode($nodeID);
                    if ($role) {
                        $roleList[] = $role;
                    } else {
                        $role = $this->GetRoleOnGroup($idGroup);
                        if ($role) {
                            $roleList[] = $role;
                        }
                    }
                }
                if (!empty($roleList) && is_array($roleList)) {
                    $roleList = array_unique($roleList);
                }
                return $roleList;
            }
        } else {
            $this->SetError(1);
        }
    }

    function hasPermissionOnNode($nodeID, $pName)
    {
        $roles = $this->GetRolesOnNode($nodeID);
        $permission = new Permission();
        $permission->SetByName($pName);
        $pID = $permission->GetID();
        if ($roles) {
            foreach ($roles as $idRol) {
                $role = new Role($idRol);
                $permissionList = $role->GetPermissionsList();
                if ($permissionList)
                    if (in_array($pID, $permissionList))
                        return true;
            }
        }
        return false;
    }

    function hasPermissionOnGroup($groupID, $pName)
    {
        $permission = new Permission();
        $permission->SetByName($pName);
        $pID = $permission->GetID();
        $role = new Role($this->GetRoleOnGroup($groupID));
        $permissionList = $role->GetPermissionsList();
        if (!empty($permissionList)) {
            if (in_array($pID, $permissionList)) {
                return true;
            }
        }
        return false;
    }

    function hasPermission($pName)
    {
        $groupID = App::getValue('GeneralGroup');
        return $this->HasPermissionOnGroup($groupID, $pName);
    }

    // Check perms with name $pname in all user groups of $nodeID
    function hasPermissionInNode($pName, $nodeID)
    {
        $groups = $this->GetGroupListOnNode($nodeID);
        if (!empty($groups)) {
            foreach ($groups as $_group) {
                if ($this->HasPermissionOnGroup($_group, $pName)) {
                    return true;
                }
            }
        }
        return false;
    }

    // Function which returns true if an user is in a node
    function isOnGroup($groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d', $this->get('IdUser'), $groupID);
            $dbObj->Query($query);
            if (!$dbObj->numErr) {
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

    // Function which returns the role of an user in a group
    function getRoleOnGroup($groupid)
    {
        $this->ClearError();
        if (! is_null($groupid)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT IdRole FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d', $this->get('IdUser'), $groupid);
            $dbObj->Query($query);
            if ($dbObj->numRows > 0) {
                return $dbObj->GetValue('IdRole');
            }
        }
        return NULL;
    }

    function getRoles()
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

    // Function which returns true if the indicated pass is correct
    function checkPassword($pass)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf('SELECT Pass FROM Users WHERE IdUser = %d', $this->get('IdUser'));
            $dbObj->Query($query);
            if (!strcmp(md5($pass), $dbObj->GetValue('Pass'))) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->SetError(1);
        }
    }

    // Function which returns the list of groups subscribed by a node belonging to the user
    function getGroupListOnNode($nodeID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $userGroups = $this->GetGroupList();
            $node = new Node($nodeID);
            $nodeGroups = $node->GetGroupList();

            if ($node->get('IdNode') > 0 && is_array($userGroups) && is_array($nodeGroups)) {
                return array_intersect($userGroups, $nodeGroups);
            } else {
                $this->SetError(6);
            }
        } else {
            $this->SetError(1);
        }
    }

    // Function which retrieve a list of actions of an user for given node and group
    function getToolbarOnNode($nodeID, $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (!is_null($roleID)) {
                $role = new Role($roleID);
                $actions = $role->GetActionsOnNode($nodeID);
                if (!$role->numErr)
                    return $actions;
                else
                    $this->SetError(6);
            } else
                $this->SetError(7);
        } else
            $this->SetError(1);
    }

    // Function which returns the list of actions of an user for given node and group
    function getRoleOnNode($nodeID, $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (!is_null($roleID)) {
                $group = new Group($groupID);
                $roleGroupID = $group->GetRoleOnNode($nodeID);
                if ($roleGroupID)
                    return $roleGroupID;
                else
                    return $roleID;
            } else
                $this->SetError(7);
        } else
            $this->SetError(1);
    }

    // Function which returns an array of tollbars for each subscribed group
    function getToolbarsOnNode($nodeID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $groupIDs = $this->GetGroupListOnNode($nodeID);
            $salida = [];
            if (!$this->numErr) {
                foreach ($groupIDs as $groupID) {
                    $salida[] = $this->GetToolbarOnNode($nodeID, $groupID);
                }
            }
            if (!$this->numErr) {
                return $salida;
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add(bool $useAutoIncrement = true)
    {
        $this->CreateNewUser($this->get('Name'), $this->get('Login'), $this->get('Pass'), $this->get('Name'), $this->get('Email')
            , $this->get('Locale'));
    }

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
    function createNewUser($realname, $login, $pass, $email, $locale, $roleID, $idUser)
    {
        if (is_null($idUser)) {
            Logger::error('The node must be previously created');
            return NULL;
        }
        $this->set('IdUser', $idUser);
        $this->set('Login', $login);
        $this->set('Pass', $pass);
        $this->set('Name', $realname);
        $this->set('Email', $email);
        $this->set('Locale', $locale);
        if (!parent::add()) {
            Logger::error('Error in User persistence for ' . $idUser);
            return NULL;
        }
        $group = new Group();
        $group->SetGeneralGroup();
        $group->AddUserWithRole($idUser, $roleID);
        return $this->get('IdUser');
    }

    function delete()
    {
        $this->DeleteUser();
    }

    // Function which delete the current user
    function deleteUser()
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            parent::delete();
        } else
            $this->SetError(1);
    }

    // Cleaning class errors
    function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    // Loading a class error
    function setError($code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // Returning true if the class has produced an error
    function hasError()
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
     * @param int $idNode Node to check Actions
     * @return array idActions array.
     * @version Ximdex 3.6
     */
    public function getActionsOnNode($idNode, $includeActionsWithNegativeSort = false)
    {
        $result = array();

        // Getting no specific not allowed actions for $idNode
        $noActionsInNode = new \Ximdex\Models\NoActionsInNode();
        $arrayForbiddenActions = $noActionsInNode->getForbiddenActions($idNode);

        /**
         * To get the actions the steps are:
         * 1. Get the Groups on node for the current User
         * 2. For every group, the user roles.
         * 3. For every role get the actions on idnode
         */

        // Getting groups for the user for $idNode
        $arrayGroups = $this->GetGroupListOnNode($idNode);
        $arrayActions = array();
        if (! empty($arrayGroups)) {
            
            // Getting roles for the user for every group
            $arrayRoles = array();
            foreach ($arrayGroups as $idGroup) {
                $aux = array();
                $aux[] = $this->GetRoleOnGroup($idGroup);
                $arrayRoles = array_merge($arrayRoles, $aux);
            }
            if (! empty($arrayRoles)) {
                
                // Getting actions for every rol
                foreach (array_unique($arrayRoles) as $idRol) {
                    $role = new Role($idRol);
                    $arrayActions = array_merge($arrayActions, $role->GetActionsOnNode($idNode, $includeActionsWithNegativeSort));
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
     * @param array $nodes IdNodes array.
     * @return array IdActions array
     * @since Ximdex 3.6
     */
    public function getActionsOnNodeList($nodes)
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
     * @return boolean True if the action is allowed. Otherwise false.
     */
    public function isAllowedAction($idNode, $idAction)
    {
        // The action cant be specifically forbidden
        $noActionsInNode = new NoActionsInNode();
        if ($noActionsInNode->isActionForbiddenForNode($idNode, $idAction)) {
            return false;
        }
        $arrayActions = $this->getActionsOnNode($idNode, true);
        return in_array($idAction, $arrayActions);
    }

    /**
     * @param $name
     * @param $password
     * @return boolean
     */
    public function login($name, $password)
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

    /**
     * @param $userId
     * @param $nodeId
     * @return bool
     */
    public function hasAccess($nodeId)
    {
        // TODO define as global constans nodeid=10000 && nodeid=2
        if ($nodeId == 1 || $nodeId == 10000 || $nodeId == 2 || $this->getID() == self::XIMDEX_ID) {
            return true;
        }
        $user_groups = $this->getGroupList();
        $group = new Group();
        $generalGroup = array($group->getGeneralGroup());
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

    /**
     * @param $userId
     * @param $params
     * @return boolean
     */
    public  function canRead($params)
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
     * @param int $userId
     * @param array $params array asociativo que debe contener las claves node_id o node_type
     * @return bool (true, si puede escribir, false en caso contrario)
     */
    public function canWrite($params) : bool
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
        $userRoles = $this->GetRoles();
        if (! is_array($userRoles)) {
            return false;
        }
        // $userRoles = array_unique($userRoles);
        $nodeType = new NodeType($nodeTypeId);
        $actionId = $nodeType->GetConstructor();
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

    /**
     * @param $userId
     * @param $params
     * @return boolean
     */
    public function canDelete($params)
    {
        // TODO extend relation table with delete actions/nodetypes mapping
        $wfParams = $this->parseParams($params);
        if (!isset($wfParams['node_type'])) {
            return false;
        }
        $nodeTypeId = (int)$wfParams['node_type'];
        if ($this->checkContext($nodeTypeId, Constants::DELETE)) {
            return true;
        }
        return $this->canWrite($params);
    }

    /**
     * @param $userId
     * @param $params
     * @return boolean
     */
    public function canModify($params)
    {
        // TODO extend relation table with modify actions/nodetypes mapping
        $wfParams = $this->parseParams($params);
        if (!isset($wfParams['node_type'])) {
            return false;
        }
        $nodeTypeId = (int)$wfParams['node_type'];
        if ($this->checkContext( $nodeTypeId, Constants::UPDATE)) {
            return true;
        }
        return $this->canWrite($params);
    }

    /**
     * @param $params
     * @return array
     */
    protected function parseParams($params)
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

    protected function checkContext($idNodeType, $mode)
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
            $administrators = self::getUsersByRole(User::ROLE_ADMINISTRATOR);
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
        $sql = "SELECT IdNode FROM Nodes WHERE Nodes.IdNodeType = 5009 AND Nodes.Name = '{$data}'";
        $dbObj->Query($sql);
        if ($dbObj->getDesErr()) {
            throw new \Exception($dbObj->getDesErr());
        }
        return new static($dbObj->getValue('IdNode'));
    }
}
