<?php

/*********************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    			    	 *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	    	 *
 *                                                                            					    	 *
 *  This program is free software: you can redistribute it and/or modify        	 *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      	 *
 *  (at your option) any later version.                                       			 *
 *                                                                            						 *
 *  This program is distributed in the hope that it will be useful,           		 *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the * 
 *  GNU Affero General Public License for more details.                       		 *
 *                                                                            						 *
 * See the Affero GNU General Public License for more details.                	 *
 * You should have received a copy of the Affero GNU General Public License  *
 * version 3 along with Ximdex (see LICENSE).                                 		 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       		 *
 *                                                                            						 *
 * @version $Revision: $                                                      				 * 	 
 *                                                                            						 *
 *                                                                            						 *
 *********************************************************************************/








if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Groups_ORM.class.php';

/// Group class

class Group extends Groups_ORM
{
	var $groupID;
	//	var $dbObj;
	var $numErr;				// Error code
	var $msgErr;				// Error message
	var $errorList= array(	// Class error list
	1 => 'Group does not exist',
	2 => 'A group with this name already exists',
	3 => 'Arguments missing',
	4 => 'Some of the node links could not be deleted',
	5 => 'Database connection error',
	6 => 'General group could not be obtained',
	7 => 'Some of the node links could not be deleted',
	8 => 'Specified relation already exists',
	9 => 'General group cant be removed'
	);

	function Group($id = null)
	{
		$this->errorList[1]=_('Group does not exist');
		$this->errorList[2]=_('A group with this name already exists');
		$this->errorList[3]=_('Arguments missing');
		$this->errorList[4]=_('Some of the node links could not be deleted');
		$this->errorList[5]=_('Database connection error');
		$this->errorList[6]=_('General group could not be obtained');
		$this->errorList[7]=_('Some of the node links could not be deleted');
		$this->errorList[8]=_('Specified relation already exists');
		$this->errorList[9]=_('General group cant be removed');
		$id = (int) $id;
		parent::GenericData($id);
		if ($this->get('IdGroup')) {
			$this->groupID = $this->get('IdGroup');
		}
	}

	/// Returns the "General" group
	function GetGeneralGroup() {
		if (Config::exists("GeneralGroup")) {
			return Config::getValue("GeneralGroup");
		}
		$this->setError(6);
		return NULL;
	}

	/// Loads the General group
	function SetGeneralGroup()
	{
		$generalGroup = $this->GetGeneralGroup();
		if ($generalGroup) {
			parent::GenericData($generalGroup);
			return $this->get('IdGroup');
		}
	}

	// REturns a list of existing idGroups 
	function GetAllGroups()
	{
		$this->ClearError();
		$dbObj = new DB();
		$sql = 'SELECT IdGroup FROM Groups';
		$dbObj->Query($sql);
		if(!$dbObj->numErr)
		{
			while(!$dbObj->EOF)
			{
				$salida[] = $dbObj->GetValue("IdGroup");
				$dbObj->Next();
			}
			return $salida;
		}
		else
		$this->SetError(5);
	}

	// Obatins a list of userId associated with this group
	function GetUserList()
	{
		$this->ClearError();
		$dbObj = new DB();
		if(!is_null($this->get('IdGroup'))) {
			$sql = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdGroup = %d", $this->get('IdGroup'));
			$dbObj->Query($sql);
			$salida = array();
			if(!$dbObj->numErr) {
				while(!$dbObj->EOF) {
					$salida[] = $dbObj->GetValue("IdUser");
					$dbObj->Next();
				}
				return $salida;
			} else {
				$this->SetError(5);
			}
		} else {
			$this->SetError(1);
		}
	}

	// Returns a list of nodes associated to a group
	function GetNodeList()
	{
		$this->ClearError();
		$dbObj = new DB();
		$salida = array();
		if($this->get('IdGroup') > 0) {
			$sql = sprintf("SELECT IdNode FROM RelGroupsNodes WHERE IdGroup = %d", $this->groupID);
			$dbObj->Query($sql);
			if(!$dbObj->numErr) {
				while(!$dbObj->EOF)
				{
					$salida[] = $dbObj->GetValue("IdNode");
					$dbObj->Next();
				}
				return $salida;
			}
			else
			$this->SetError(5);
		}
		else
		$this->SetError(1);
	}

	// Returns the groupID (class attribute)
	function GetID()
	{
		return $this->get('IdGroup');
	}

	// Allows to change the groupID without destroying and re-creating the object
	function SetID($id)
	{
		$this->ClearError();
		parent::GenericData($id);
		if (!($this->get('IdGroup') > 0)) {
			$this->groupID = null;
			$this->SetError(1);
		} else {
			$this->groupID = $this->get('IdGroup');
		}
	}

	// Devuelve el nombre del grupo correspondiente
	function GetGroupName()
	{
		return $this->get('Name');
	}

	// Nos permite cambiar el nombre a un grupo
	function SetGroupName($name)
	{
		if (!($this->get('IdGroup') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	// Creates a new group in database and loads the corresponding ID.
	function CreateNewGroup($name, $gID = NULL)
	{
		if (!is_null($gID)) {
			$this->set('IdGroup', $gID);
		}
		$this->set('Name', $name);
		$this->add();
	}


	//Deleting a group, deleting first its subscriptions
	function DeleteGroup()
	{
		$this->ClearError();

		if($this->GetGeneralGroup() == $this->get('IdGroup') ) {
			$this->SetError(9);
			return false;
		}

		if(!is_null($this->get('IdGroup'))) {
			// Deleting subscription of all groups
			$users = $this->GetUserList();
			if (sizeof($users)) {
				foreach ($users as $uid)
				{
				$this->DeleteUser($uid);
					if($this->numErr)
					{
						$this->SetError(7);
						return;
					}
				}
			}

			// Deleting also group-node relation in DB
			$dbObj = new DB();
			$dbObj->Execute(sprintf("DELETE FROM RelGroupsNodes WHERE IdGroup = %d", $this->get('IdGroup')));
			if ($dbObj->numErr) {
				$this->SetError(5);
			}

			// Deleting form DB
			$this->delete();

		}
		else
		$this->SetError(1);
	}

	//This function is not deleting an user from Users table, it is disassociate him from the group
	// It should be called desuscribeUser or something similar
	function DeleteUser($userID)
	{
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$dbObj = new DB();
			$dbObj->Execute(sprintf("DELETE FROM RelUsersGroups WHERE IdGroup= %d AND IdUser = %d", $this->get('IdGroup') ,$userID));
			if($dbObj->numErr)
			$this->SetError(5);
		}
		else
		$this->SetError(1);
	}

	// Associating an existing user to a group with a concrete role
	function AddUserWithRole($userID, $roleID)
	{
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$dbObj = new DB();
			$query = sprintf ("SELECT IdRel FROM RelUsersGroups"
			. " WHERE IdUser = %d AND IdGroup = %d AND IdRole = %d",
			$userID, $this->get('IdGroup'), $roleID);

			$dbObj->query($query);
			if ($dbObj->numRows > 0) {
				$this->SetError(8);
				return false;
			}
			$query = sprintf("INSERT INTO RelUsersGroups (IdUser, IdGroup, IdRole) VALUES (%d, %d, %d)",
			$userID, $this->get('IdGroup'), $roleID);
			$dbObj->Execute($query);
			if($dbObj->numErr)
			$this->SetError(5);
		} else
		$this->SetError(1);
	}

	/**
	 * Allows to change the role used by an user in a group
	 * @param $userID
	 * @param $roleID
	 * @return unknown_type
	 */
	function ChangeUserRole($userID, $roleID){
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$dbObj = new DB();
			$query = sprintf("UPDATE RelUsersGroups SET IdRole = %d WHERE IdGroup= %d AND IdUser= %d", $roleID, $this->get('IdGroup'), $userID);
			$dbObj->Execute($query);
			if($dbObj->numErr)
			$this->SetError(5);
		}
		else
		$this->SetError(1);
	}

	/**
	 * Returns true if the user belongs to a group
	 * @param $userID
	 * @return unknown_type
	 */
	function HasUser($userID)
	{
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$dbObj = new DB();
			$query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdGroup = %d AND IdUser = %d", $this->get('IdGroup'), $userID);
			$dbObj->Query($query);

			if(!$dbObj->numErr)
			if($dbObj->numRows) {
				return true;
			} else {
				return false;
			}
			else
			$this->SetError(5);
		}
		else
		$this->SetError(1);
	}

	/**
	 *
	 * @param $nodeID
	 * @return unknown_type
	 */
	function GetRoleOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$node = new Node($nodeID);
			if(!$node->numErr)
			return $node->GetRoleOfGroup($this->get('IdGroup'));
		}
		else
		$this->SetError(1);
	}

	/**
	 *
	 * @param $nodeID
	 * @return unknown_type
	 */
	function IsOnNode($nodeID){
		$this->ClearError();
		if($this->get('IdGroup') > 0) {
			$node = new Node($nodeID);
			if(!$node->numErr)
			{
				$groupList = $node->GetGroupList();
				if(in_array($this->GetID(), $groupList))
				return true;
				else
				return false;
			}
		}
		else
		$this->SetError(1);
	}

	/**
	 * Cleans class errors
	 * @return unknown_type
	 */
	function ClearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	 * Loads class error
	 * @param $code
	 * @return unknown_type
	 */
	function SetError($code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 * Returns true if there was an error in the class.
	 * @return unknown_type
	 */
	function HasError(){
		return ($this->numErr != null);
	}
	/**
	 *
	 * @return unknown_type
	 */
	function getUserRoleInfo() {
		$dbObj = new DB();
		$query = sprintf('SELECT IdRel, IdUser, IdRole'
		. ' FROM RelUsersGroups'
		. ' WHERE IdGroup = %s',
		$dbObj->sqlEscapeString($this->groupID));

		$dbObj->Query($query);

		if (!($dbObj->numRows > 0)) {
			return NULL;
		}

		$result = array();
		while (!$dbObj->EOF) {
			$result[$dbObj->getValue('IdRel')] = array('IdUser' => $dbObj->getValue('IdUser'),
							'IdRole' => $dbObj->getValue('IdRole'));
			$dbObj->next();
		}

		return $result;
	}

	public static function getSelectableGroupsInfo($idNode){

		$groupStateInfo = array();
		$group = new Group();
		$groupList=$group->find('IdGroup', NULL, NULL, MONO);
		
		$node = new Node($idNode);
		$groupState = array();
		if (is_array($groupList) && !empty($groupList)) {
			foreach ($groupList as $idGroup) {
				$group = new Group($idGroup);
				$users = $group->GetUserList();
				if (is_array($users) && !empty($users)) {
					foreach ($users as $idUser) {
						$nextState = $node->GetNextAllowedState($idUser, $idGroup);
						if ($nextState > 0) {
							$groupState[$idGroup] = $nextState;
						}
					}
				}
			}
		}
		
		foreach ($groupState as $idGroup => $idState) {
			$group = new Node($idGroup);
			$workflow = new WorkFlow($idNode, $idState);
			$idS = $workflow->pipeStatus->get('id');
			$idG = $group->get('Name');
			$sN = $workflow->pipeStatus->get('Name');

			$groupStateInfo[] = array(
				'IdGroup' => $group->get('IdNode'),
				'groupName' => $idG,
				'IdState' => $idS,
				'stateName' => $sN
			);
		}
		return $groupStateInfo;
	}
}
?>
