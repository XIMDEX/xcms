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

if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Users_ORM.class.php';

class User extends Users_ORM {
	var $userID;
	var $numErr;				// Error code
   	var $msgErr;				// Error message
	var $errorList= array(
		1 => 'User does not exist',
		2 => 'An user with this login already exists',
		3 => 'Arguments missing',
		4 => 'Some of the connections with your groups could not be deleted',
		5 => 'Database connection error',
		6 => 'Error in associated object',
		7 => 'Role for group missing'
	);	// Class error list

	function __construct($params = NULL) {
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
	function GetAllUsers() {
		return $this->find('IdUser', '1 ORDER BY Name', NULL, MONO);
	}

    // This function returns all the groups belonging to an user
	function GetGroupList() {
		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$dbObj = new DB();
			$sql = sprintf("SELECT IdGroup FROM RelUsersGroups WHERE IdUser = %d",
					$this->get('IdUser'));
			$dbObj->Query($sql);
			if(!$dbObj->numErr) {
				$salida = array();
				while(!$dbObj->EOF) {
					$salida[] = $dbObj->GetValue("IdGroup");
					$dbObj->Next();
				}
	    		return $salida;
			} else SetError(5);
		} else $this->SetError(1);
		return null;
	}

 	// Returns an obgect idUser
 	function GetID(){
		$this->ClearError();
		return $this->get('IdUser');
	}

    /* *** getters *** */

    // Returns the user name associated to an idUser
	function GetRealName(){
		return $this->get("Name");
	}

   	// Returns the user login
   	function GetLogin(){
		return $this->get("Login");
	}

   	// Returns de user email
	function GetEmail(){
		return $this->get("Email");
	}

   	// Returns user locale
	function GetLocale(){
		return $this->get("Locale");
	}

   	// Returns user's number of access
	function GetNumAccess(){
		return $this->get("NumAccess");
	}

    /* *** setters *** */

  	// It allows to change an object idUser. It avoid to have to destroy and create again
   	function SetID($id){
		parent::GenericData($id);
		if (!($this->get('IdUser') > 0)) {
			$this->SetError(1);
			return null;
		}
		return $this->get('IdUser');
	}

   	// Updating database with the new user name
	function SetRealName($name){
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
	function SetPassword($pass){
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
	function SetEmail($email){
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

	function SetLocale($code){
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
	function SetByLogin($login){
		$this->ClearError();
		$dbObj = new DB();
		$query = sprintf("SELECT IdUser FROM Users WHERE Login = %s",
					$dbObj->sqlEscapeString($login));
		$dbObj->Query($query);
		if ($dbObj->numRows) {
			return $this->SetID($dbObj->GetValue("IdUser"));
		}
		$this->SetError(5);
		return false;
	}

    // Function which returns true if an user is in a node
    function IsOnNode($nodeID, $ignoreGeneralGroup = null){
        if(is_array($nodeID)){
            $nodeID = $nodeID['id'];
        }

		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$node = new Node($nodeID);
			if ($node->get('IdNode') > 0) {
				$userList=array();
				$userList = $node->GetUserList($ignoreGeneralGroup);
				if(in_array($this->get('IdUser'), $userList)) {
					return true;
				} else {
					return false;
				}
			}
		}else{
			$this->SetError(1);
		}
	}

    // Function which retrieve a list of roles of an user in a determined node
    function GetRolesOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$groupList = $this->GetGroupListOnNode($nodeID);
			if(!empty($groupList)) {
				foreach($groupList as $idGroup)	{
					$group = new Group($idGroup);
					$role = $group->GetRoleOnNode($nodeID);
					if($role) {
						$roleList[] = $role;
					} else {
						$role = $this->GetRoleOnGroup($idGroup);
						if($role) {
							$roleList[] = $role;
						}
					}
				}
				if(!empty($roleList) && is_array($roleList)) {
					$roleList = array_unique($roleList);
				}
				return $roleList;
			}
		} else {
			$this->SetError(1);
		}
	}

	function HasPermissionOnNode($nodeID, $pName){
		$roles  = $this->GetRolesOnNode($nodeID);
		$permission = new Permission();
		$permission->SetByName($pName);
		$pID = $permission->GetID();
		if ($roles) foreach ($roles as $idRol) {
			$role = new Role($idRol);
			$permissionList = $role->GetPermissionsList();
			if ($permissionList) if (in_array($pID, $permissionList))
				return true;
			}
		return false;
	}

	function HasPermissionOnGroup($groupID, $pName){
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

	function HasPermission($pName){
		$groupID = Config::GetValue("GeneralGroup");
		return  $this->HasPermissionOnGroup($groupID, $pName);
	}

	//Check perms with name $pname in all user groups of $nodeID
	function HasPermissionInNode($pName, $nodeID){
		$groups = $this->GetGroupListOnNode($nodeID);
		if(!empty($groups) ) {
			foreach($groups as $_group) {
				if($this->HasPermissionOnGroup($_group, $pName) ) {
					return true;
				}
			}

		}
		return false;
	}

	// Function which returns true if an user is in a node
	function IsOnGroup($groupID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d", $this->get('IdUser'), $groupID);
			$dbObj->Query($query);
			if(!$dbObj->numErr) {
				if($dbObj->numRows) {
					return true;
				}
				else {
					return false;
				}
			}
		}
		else {
			$this->SetError(1);
		}
	}

	function isDemo() {
		if(!ModulesManager::isEnabled("ximDEMOS") ) {
			return false;
		}

		$idUser = $this->get('IdUser');
		if($this->get('IdUser') > 0) {
			//Get Nodeid rol "Demo"
			$idRole = Role::GetByName("Demo");
			$query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdUser = %d AND IdRole = %d", $this->get('IdUser'), $idRole);
			$dbObj = new DB();
			$dbObj->Query($query);
			if(!$dbObj->numErr) {
				if($dbObj->numRows) {
					return true;
				}
			}
		}else {
			$this->SetError(1);
		}
		return false;
	}

    // Function which returns the role of an user in a group
	function GetRoleOnGroup($groupid) {
		$this->ClearError();
		if(!is_null($groupid)) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdRole FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d", $this->get('IdUser'), $groupid);
			$dbObj->Query($query);
			if($dbObj->numRows > 0) {
				return $dbObj->GetValue("IdRole");
			}
		}
		return NULL;
	}

	function GetRoles(){
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

	// Function which returns true if the indicated pass is correct
	function CheckPassword($pass){
		$this->ClearError();
		if ($this->get('IdUser') > 0) {
			$dbObj = new DB();
			$query = sprintf("SELECT Pass FROM Users WHERE IdUser = %d",$this->get('IdUser'));
			$dbObj->Query($query);
			if (!strcmp(md5($pass), $dbObj->GetValue("Pass")) ) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			$this->SetError(1);
		}
	}

	// Function which returns the list of groups subscribed by a node belonging to the user
	function GetGroupListOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
	        	$userGroups = $this->GetGroupList();
        	    	$node = new Node($nodeID);
        	    	$nodeGroups = $node->GetGroupList();

			if($node->get('IdNode') > 0 && is_array($userGroups) && is_array($nodeGroups)) {
    	        		return array_intersect($userGroups, $nodeGroups);
			}
			else {
				$this->SetError(6);
			}
		}
		else {
			$this->SetError(1);
		}
	}

	// Function which retrieve a list of actions of an user for given node and group
	function GetToolbarOnNode($nodeID, $groupID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
       		$roleID = $this->GetRoleOnGroup($groupID);
       		if(!is_null($roleID)){
           		$role = new Role($roleID);
        		$actions = $role->GetActionsOnNode($nodeID);
				if(!$role->numErr)
       	    		return $actions;
				else
					$this->SetError(6);
            }
            else
				$this->SetError(7);
		}
		else
			$this->SetError(1);
	}

	// Function which returns the list of actions of an user for given node and group
	function GetRoleOnNode($nodeID, $groupID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
        	$roleID = $this->GetRoleOnGroup($groupID);
       		if(!is_null($roleID)){
				$group = new Group($groupID);
				$roleGroupID = $group->GetRoleOnNode($nodeID);
				if($roleGroupID)
        			return $roleGroupID;
				else
					return $roleID;
           	}
       		else
				$this->SetError(7);
		}
		else
			$this->SetError(1);
	}

	// Function which returns an array of tollbars for each subscribed group
	function GetToolbarsOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$groupIDs = $this->GetGroupListOnNode($nodeID);
			if(!$this->numErr) {
				foreach ($groupIDs as $groupID) {
					$salida[] = $this->GetToolbarOnNode($nodeID,$groupID);
				}
			}

			if(!$this->numErr)
				return $salida;
		} else {
			$this->SetError(1);
		}
	}

	function add() {
		$this->CreateNewUser($this->get('Name'), $this->get('Login'),$this->get('Pass'), $this->get('Name'),$this->get('Email'), $this->get('Locale'));
	}

	// Function which creates a new user if it does not exist in the system previously, and load the idUser
	function set($attrib, $value) {
  		if ($attrib == 'Pass')
   			$value = md5($value);
   		return parent::set($attrib, $value);
   	}

	function CreateNewUser($realname, $login, $pass, $email, $locale, $roleID, $idUser) {

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

	function delete() {
		$this->DeleteUser();
	}

	// Function which delete the current user
	function DeleteUser(){
		$this->ClearError();
		if($this->get('IdUser') > 0) {
			$groupList = $this->GetGroupList();			
       			$dbObj = new DB();

			$query = sprintf("DELETE FROM UnverifiedUsers where email=%s",
					$this->get('Email'));
				
			$dbObj->Execute($query);
			parent::delete();
		}
		else
			$this->SetError(1);
	}

	/// Cleaning class errors
	function ClearError(){
		$this->numErr = null;
		$this->msgErr = null;
	}

	/// Loading a class error
	function SetError($code){
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	// Returning true if the class has produced an error
	function HasError(){
		return ($this->numErr != null);
	}

	/**
	 * 
	 * @return [type] [description]
	 */
	public function afterLogin(){
		$numAccess = $this->NumAccess;
		$numAccess++;
		$this->set('NumAccess', $numAccess);
		$this->set('LastLogin', time());
		$this->update();
	}

    public function getLastestDocs(){
        $docs=array();
        $v = new Version();
        $docs=$v->getLastestDocsByUser($this->GetID()); 
        foreach($docs as $i => $doc){
            $d = new Node($doc["IdNode"]);
            $docs[$i]["name"]=$d->GetNodeName();
            $docs[$i]["nodetype"]=$d->GetNodeType();
            $docs[$i]["path"]=str_replace("/Ximdex/Projects","",$d->GetPath());
        }
        return $docs;
    }
}
?>

