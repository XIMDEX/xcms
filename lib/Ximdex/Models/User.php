<?php

namespace Ximdex\Models;

use Ximdex\Logger as XMD_Log;
use Ximdex\Models\ORM\UsersORM;
use Ximdex\Runtime\App;
use Permission;
use Group;
use Role;
use Ximdex\Models\Node;
use DB_legacy as DB;
use ModulesManager;
use NoActionsInNode;
use Version;

class User extends UsersORM
{

    var $userID;
    var $numErr;    // Error code
    var $msgErr;    // Error message
    var $errorList = array(
        1 => 'User does not exist',
        2 => 'An user with this login already exists',
        3 => 'Arguments missing',
        4 => 'Some of the connections with your groups could not be deleted',
        5 => 'Database connection error',
        6 => 'Error in associated object',
        7 => 'Role for group missing'
    ); // Class error list

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

    function getRealName()
    {
        return $this->get("Name");
    }

    // Returns an obgect idUser

    function getLogin()
    {
        return $this->get("Login");
    }

    /*     * ** getters *** */

    // Returns the user name associated to an idUser

    /**
     * @return bool|string
     */
    function getEmail()
    {
        return $this->get("Email");
    }

    // Returns the user login

    /**
     * @return bool|string
     */
    function getLocale()
    {
        return $this->get("Locale");
    }

    // Returns de user email

    /**
     * @return bool|string
     */
    function getNumAccess()
    {
        return $this->get("NumAccess");
    }

    // Returns user locale

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

    // Returns user's number of access

    function set($attrib, $value)
    {
        if ($attrib == 'Pass')
            $value = md5($value);
        return parent::set($attrib, $value);
    }

    /*     * ** setters *** */

    // It allows to change an object idUser. It avoid to have to destroy and create again

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

    // Updating database with the new user name

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

    // Modifies the user pass

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

    // Modifies the user email

    function setByLogin($login)
    {
        $this->ClearError();
        $dbObj = new \DB_legacy();
        $query = sprintf("SELECT IdUser FROM Users WHERE Login = %s", $dbObj->sqlEscapeString($login));
        $dbObj->Query($query);
        if ($dbObj->numRows) {
            return $this->SetID($dbObj->GetValue("IdUser"));
        }
        $this->SetError(5);
        return false;
    }

    function setID($id)
    {
        parent::__construct($id);
        if (!($this->get('IdUser') > 0)) {
            $this->SetError(1);
            return null;
        }
        return $this->get('IdUser');
    }

    // looks for an user by login, and load the corresponding idUser

    /**
     * @param $nodeID
     * @param null $ignoreGeneralGroup
     * @return bool
     */
    function isOnNode($nodeID, $ignoreGeneralGroup = null)
    {
        if (is_array($nodeID)) {
            $nodeID = $nodeID['id'];
        }

        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $node = new \Ximdex\Models\Node($nodeID);
            if ($node->get('IdNode') > 0) {
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
        return false;
    }

    // Function which returns true if an user is in a node

    /**
     * @param $nodeID
     * @param $pName
     * @return bool
     */
    function hasPermissionOnNode($nodeID, $pName)
    {
        $roles = $this->GetRolesOnNode($nodeID);
        $permission = new \Permission();
        $permission->SetByName($pName);
        $pID = $permission->GetID();
        if ($roles)
            foreach ($roles as $idRol) {
                $role = new Role($idRol);
                $permissionList = $role->GetPermissionsList();
                if ($permissionList)
                    if (in_array($pID, $permissionList))
                        return true;
            }
        return false;
    }

    // Function which retrieve a list of roles of an user in a determined node

    /**
     * @param $nodeID
     * @return array
     */
    function getRolesOnNode($nodeID)
    {
        $roleList = array();
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $groupList = $this->GetGroupListOnNode($nodeID);
            if (!empty($groupList)) {
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
        return $roleList;
    }

    /**
     * @param $nodeID
     * @return array
     */
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
        return array();
    }

    function getGroupList()
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new \DB_legacy();
            $sql = sprintf("SELECT IdGroup FROM RelUsersGroups WHERE IdUser = %d", $this->get('IdUser'));
            $dbObj->Query($sql);
            if (!$dbObj->numErr) {
                $salida = array();
                while (!$dbObj->EOF) {
                    $salida[] = $dbObj->GetValue("IdGroup");
                    $dbObj->Next();
                }
                return $salida;
            } else {
                $this->SetError(5);

            }
        } else
            $this->SetError(1);
        return null;
    }

    function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    //Check perms with name $pname in all user groups of $nodeID

    function setError($code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // Function which returns true if an user is in a node

    /**
     * @param $groupid
     * @return null|String
     */
    function getRoleOnGroup($groupid)
    {
        $this->ClearError();
        if (!is_null($groupid)) {
            $dbObj = new \DB_legacy();
            $query = sprintf("SELECT IdRole FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d", $this->get('IdUser'), $groupid);
            $dbObj->Query($query);
            if ($dbObj->numRows > 0) {
                return $dbObj->GetValue("IdRole");
            }
        }
        return NULL;
    }

    /**
     * @param $pName
     * @return bool
     */
    function hasPermission($pName)
    {
        $groupID = App::getValue("GeneralGroup");
        return $this->HasPermissionOnGroup($groupID, $pName);
    }

    // Function which returns the role of an user in a group

    /**
     * @param $groupID
     * @param $pName
     * @return bool
     */
    function hasPermissionOnGroup($groupID, $pName)
    {
        $permission = new Permission();
        $permission->SetByName($pName);
        $pID = $permission->GetID();

        $role = new Role($this->GetRoleOnGroup($groupID));
        $permissionList = $role->GetPermissionsList();
        if (!empty($permissionList))
            if (in_array($pID, $permissionList))
                return true;

        return false;
    }

    /**
     * @return array
     */
    /*
     *
     */
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

    // Function which returns true if the indicated pass is correct

    /**
     * @param $groupID
     * @return bool
     */
    function isOnGroup($groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new DB();
            $query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d", $this->get('IdUser'), $groupID);
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
        return false;
    }

    // Function which returns the list of groups subscribed by a node belonging to the user

    /**
     * @return bool
     */
    function isDemo()
    {
        if (!ModulesManager::isEnabled("ximDEMOS")) {
            return false;
        }

        $idUser = $this->get('IdUser');
        if ($idUser > 0) {
            //Get Nodeid rol "Demo"
            $idRole = Role::GetByName("Demo");
            $query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdRole = %d", $this->get('IdUser'), $idRole);
            $dbObj = new DB();
            $dbObj->Query($query);
            if (!$dbObj->numErr) {
                if ($dbObj->numRows) {
                    return true;
                }
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    // Function which retrieve a list of actions of an user for given node and group


    function getRoles()
    {
        $this->ClearError();
        $query = sprintf("SELECT IdRole FROM RelUsersGroups WHERE IdUser = %d group by IdRole", $this->get('IdUser'));
        $dbObj = new DB();
        $dbObj->Query($query);
        $roles = array();
        while (!$dbObj->EOF) {
            $roles[] = $dbObj->GetValue('IdRole');
            $dbObj->Next();
        }
        return $roles;
    }

    // Function which returns the list of actions of an user for given node and group

    /**
     * @param $pass
     * @return bool
     */
    function checkPassword($pass)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $dbObj = new DB();
            $query = sprintf("SELECT Pass FROM Users WHERE IdUser = %d", $this->get('IdUser'));
            $dbObj->Query($query);
            if (!strcmp(md5($pass), $dbObj->GetValue("Pass"))) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    // Function which returns an array of tollbars for each subscribed group

    /**
     * @param $nodeID
     * @param $groupID
     * @return null|String
     */
    function getRoleOnNode($nodeID, $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (!is_null($roleID)) {
                $group = new Group($groupID);
                $roleGroupID = $group->GetRoleOnNode($nodeID);
                if ($roleGroupID) {
                    return $roleGroupID;
                } else {
                    return $roleID;
                }
            } else {
                $this->SetError(7);
            }
        } else {
            $this->SetError(1);
        }
        return null;
    }

    /**
     * @param $nodeID
     * @return array
     */
    function getToolbarsOnNode($nodeID)
    {
        $result = array();
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $groupIDs = $this->GetGroupListOnNode($nodeID);
            if (!$this->numErr) {
                foreach ($groupIDs as $groupID) {
                    $result[] = $this->GetToolbarOnNode($nodeID, $groupID);
                }
            }

            if (!$this->numErr) {
                return $result;
            }
        } else {
            $this->SetError(1);
        }
        return $result;
    }

    // Function which creates a new user if it does not exist in the system previously, and load the idUser

    /**
     * @param $nodeID
     * @param $groupID
     * @return array
     */
    function getToolbarOnNode($nodeID, $groupID)
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            $roleID = $this->GetRoleOnGroup($groupID);
            if (!is_null($roleID)) {
                $role = new Role($roleID);
                $actions = $role->GetActionsOnNode($nodeID);
                if (!$role->numErr) {
                    return $actions;
                } else {
                    $this->SetError(6);
                }
            } else {
                $this->SetError(7);
            }
        } else {
            $this->SetError(1);
        }
        return array();
    }

    /**
     *
     */
    function add()
    {
        $this->CreateNewUser($this->get('Name'), $this->get('Login'), $this->get('Pass'), $this->get('Name'), $this->get('Email'), $this->get('Locale'));
    }

    /**
     * @param $realname
     * @param $login
     * @param $pass
     * @param $email
     * @param $locale
     * @param $roleID
     * @param null $idUser
     * @return bool|null|string
     */
    function createNewUser($realname, $login, $pass, $email, $locale, $roleID, $idUser = null)
    {

        if (is_null($idUser)) {
            XMD_Log::error(_("The node must be previously created"));
            return NULL;
        }

        $this->set('IdUser', $idUser);
        $this->set('Login', $login);
        $this->set('Pass', $pass);
        $this->set('Name', $realname);
        $this->set('Email', $email);
        $this->set('Locale', $locale);

        if (!parent::add()) {
            XMD_Log::error(_("Error in User persistence for $idUser"));
            return NULL;
        }

        $group = new Group();
        $group->SetGeneralGroup();
        $group->AddUserWithRole($idUser, $roleID);

        return $this->get('IdUser');
    }

    // Function which delete the current user

    function delete()
    {
        $this->DeleteUser();
    }

    /// Cleaning class errors

    function deleteUser()
    {
        $this->ClearError();
        if ($this->get('IdUser') > 0) {
            //   $groupList = $this->GetGroupList();
            $dbObj = new DB();

            $query = sprintf("DELETE FROM UnverifiedUsers where email=%s", $this->get('Email'));

            $dbObj->Execute($query);
            parent::delete();
        } else
            $this->SetError(1);
    }

    /// Loading a class error

    /**
     * @return bool
     */
    function hasError()
    {
        return ($this->numErr != null);
    }

    // Returning true if the class has produced an error

    /**
     *
     */
    public function afterLogin()
    {
        $numAccess = $this->NumAccess;
        $numAccess++;
        $this->set('NumAccess', $numAccess);
        $this->set('LastLogin', time());
        $this->update();
    }

    /**
     * @return array
     */
    public function getLastestDocs()
    {
        //  $docs = array();
        $v = new Version();
        $docs = $v->getLastestDocsByUser($this->GetID());
        return $docs;
    }

    function getID()
    {
        $this->ClearError();
        return $this->get('IdUser');
    }

    /**
     * Get an array with allowed actions on a node
     * @version Ximdex 3.6
     */

    /**
     * @param $nodes
     * @return array
     */
    public function getActionsOnNodeList($nodes)
    {
        $result = array();

        $nodes = array_unique($nodes);
        $actionsArray = array();
        //Get idactions for everynode
        foreach ($nodes as $idNode) {
            $aux = $this->getActionsOnNode($idNode);
            $actionsArray = array_merge($actionsArray, $aux);
        }

        /* Actions can be different idactions and same command, params
         * and module, so we must group this actions.
         * The idActions returned have allowed commands for the selected
         * nodes.
         */
        //$actionsArray = array_unique($actionsArray);
        if (count($nodes) > 1) {
            foreach ($actionsArray as $idAction) {
                $action = new Action($idAction);

                $command = $action->get("Command");
                $aliasModule = $action->get("Module") ? $action->get("Module") : "nomodule";
                $aliasParam = $action->get("Params") ? $action->get("Params") : "noparams";

                if (isset($founded[$command][$aliasModule][$aliasParam]) &&
                    $founded[$command][$aliasModule][$aliasParam] == count($nodes) - 1
                ) {
                    $result[] = $idAction;
                    continue;
                }
                if (!isset($founded[$command][$aliasModule][$aliasParam])) {
                    $founded[$command][$aliasModule][$aliasParam] = 1;
                } else {
                    $founded[$command][$aliasModule][$aliasParam] =
                        $founded[$command][$aliasModule][$aliasParam] + 1;
                }
            }
        } else {
            return $actionsArray;
        }
        return $result;
    }

    /**
     * Calculates the posible actions for a group of nodes.
     * It depends on roles, states and nodetypes of nodes.
     * @since Ximdex 3.6
     *
     */

    /**
     * @param $idNode
     * @param bool $includeActionsWithNegativeSort
     * @return array
     */
    public function getActionsOnNode($idNode, $includeActionsWithNegativeSort = false)
    {

        //Getting no specific not allowed actions for $idNode
        $noActionsInNode = new NoActionsInNode();
        $arrayForbiddenActions = $noActionsInNode->getForbiddenActions($idNode);

        /* To get the actions the steps are:
         * 1. Get the Groups on node for the current User
         * 2. For every group, the user roles.
         * 3. For every role get the actions on idnode
         */

        //Getting groups for the user for $idNode
        $arrayGroups = $this->GetGroupListOnNode($idNode);
        $arrayRoles = array();
        $arrayActions = array();


        //Getting roles for the user for every group.
        foreach ($arrayGroups as $idGroup) {
            $aux = array();
            $aux[] = $this->GetRoleOnGroup($idGroup);
            $arrayRoles = array_merge($arrayRoles, $aux);
        }

        $arrayRoles = array_unique($arrayRoles);

        //Getting actions for every rol .
        foreach ($arrayRoles as $idRol) {
            $role = new Role($idRol);
            $arrayActions = array_merge($arrayActions, $role->GetActionsOnNode($idNode, $includeActionsWithNegativeSort));
        }

        $arrayActions = array_unique($arrayActions);

        //Deleting not allowed actions from actions array.
        $result = array_diff($arrayActions, $arrayForbiddenActions);

        return $result;
    }

    /**
     * Check if the user can run the action for a node
     * @param int $idNode
     * @param int $idAction
     * @return boolean True if the action is allowed. Otherwise false.
     * @since Ximdex 3.6
     */
    public function isAllowedAction($idNode, $idAction)
    {

        //The action cant be specifically forbidden.
        $noActionsInNode = new NoActionsInNode();
        if ($noActionsInNode->isActionForbiddenForNode($idNode, $idAction)) {
            return false;
        }

        $arrayActions = $this->getActionsOnNode($idNode, true);
        return in_array($idAction, $arrayActions);
    }
}