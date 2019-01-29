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

use Ximdex\Models\ORM\GroupsOrm;
use Ximdex\Runtime\App;

class Group extends GroupsOrm
{
	public $groupID;
	
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
	 * Class error list
	 * 
	 * @var array
	 */
	public $errorList = array();

	public function __construct(int $id = null)
	{
		$this->errorList[1] = _('Group does not exist');
		$this->errorList[2] = _('A group with this name already exists');
		$this->errorList[3] = _('Arguments missing');
		$this->errorList[4] = _('Some of the node links could not be deleted');
		$this->errorList[5] = _('Database connection error');
		$this->errorList[6] = _('General group could not be obtained');
		$this->errorList[7] = _('Some of the node links could not be deleted');
		$this->errorList[8] = _('Specified relation already exists');
		$this->errorList[9] = _('General group cant be removed');
		parent::__construct($id);
		if ($this->get('IdGroup')) {
			$this->groupID = $this->get('IdGroup');
		}
	}

	/**
	 * Returns the "General" group
	 * 
	 * @return string
	 */
	public function getGeneralGroup()
	{
		$generalGroup = App::getValue('GeneralGroup', null);
		if (is_null($generalGroup)) {
			$this->setError(6);
		}
		return $generalGroup;
	}

	/**
	 * Loads the General group
	 * 
	 * @return boolean|string
	 */
	public function setGeneralGroup()
	{
		$generalGroup = $this->GetGeneralGroup();
		if ($generalGroup) {
			parent::__construct($generalGroup);
			return $this->get('IdGroup');
		}
	}

	/**
	 * Returns a list of existing idGroups
	 * 
	 * @return array
	 */
	public function getAllGroups() : array
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT IdGroup FROM Groups';
		$dbObj->Query($sql);
		if (! $dbObj->numErr) {
		    $salida = [];
			while (! $dbObj->EOF) {
				$salida[] = $dbObj->GetValue("IdGroup");
				$dbObj->Next();
			}
			return $salida;
		}
        $this->SetError(5);
	}

	/**
	 * Obtains a list of userId associated with this group
	 * 
	 * @return array
	 */
	public function getUserList()
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		if (! is_null($this->get('IdGroup'))) {
			$sql = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdGroup = %d", $this->get('IdGroup'));
			$dbObj->Query($sql);
			$salida = array();
			if (! $dbObj->numErr) {
				while (!$dbObj->EOF) {
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

	/**
	 * Returns a list of nodes associated to a group
	 * 
	 * @return array
	 */
	public function getNodeList()
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$salida = array();
		if ($this->get('IdGroup') > 0) {
			$sql = sprintf("SELECT IdNode FROM RelGroupsNodes WHERE IdGroup = %d", $this->groupID);
			$dbObj->Query($sql);
			if (! $dbObj->numErr) {
				while (! $dbObj->EOF) {
					$salida[] = $dbObj->GetValue("IdNode");
					$dbObj->Next();
				}
				return $salida;
			}
			$this->SetError(5);
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Returns the groupID (class attribute)
	 * 
	 * @return boolean|string
	 */
	public function getID()
	{
		return $this->get('IdGroup');
	}

	/**
	 * Allows to change the groupID without destroying and re-creating the object
	 * 
	 * @param int $id
	 */
	public function setID(int $id)
	{
		$this->clearError();
		parent::__construct($id);
		if (! $this->get('IdGroup')) {
			$this->groupID = null;
			$this->SetError(1);
		} else {
			$this->groupID = $this->get('IdGroup');
		}
	}

	/**
	 * Devuelve el nombre del grupo correspondiente
	 * 
	 * @return boolean|string
	 */
	public function getGroupName()
	{
		return $this->get('Name');
	}

	/**
	 * Nos permite cambiar el nombre a un grupo
	 * 
	 * @param string $name
	 * @return boolean|int
	 */
	public function setGroupName(string $name)
	{
		if (! $this->get('IdGroup')) {
			$this->SetError(1);
			return false;
		}
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Creates a new group in database and loads the corresponding ID
	 * 
	 * @param string $name
	 * @param int $gID
	 */
	public function createNewGroup(string $name, int $gID = null)
	{
		if (! is_null($gID)) {
			$this->set('IdGroup', $gID);
		}
		$this->set('Name', $name);
		$this->add();
	}

	/**
	 * Deleting a group, deleting first its subscriptions
	 * 
	 * @return void|boolean
	 */
	public function deleteGroup()
	{
		$this->clearError();
		if ($this->GetGeneralGroup() == $this->get('IdGroup')) {
			$this->SetError(9);
			return false;
		}
		if (! is_null($this->get('IdGroup'))) {
		    
			// Deleting subscription of all groups
			$users = $this->GetUserList();
			if (sizeof($users)) {
				foreach ($users as $uid) {
					$this->DeleteUser($uid);
					if ($this->numErr) {
						$this->SetError(7);
						return false;
					}
				}
			}

			// Deleting also group-node relation in DB
			$dbObj = new \Ximdex\Runtime\Db();
			$dbObj->Execute(sprintf("DELETE FROM RelGroupsNodes WHERE IdGroup = %d", $this->get('IdGroup')));
			if ($dbObj->numErr) {
				$this->SetError(5);
				return false;
			}

			// Deleting form DB
			$this->delete();
		} else {
			$this->SetError(1);
			return false;
		}
		return true;
	}

	/**
	 * This function is not deleting an user from Users table, it is disassociate him from the group
	 * It should be called desuscribeUser or something similar
	 * 
	 * @param int $userID
	 * @return bool
	 */
	public function deleteUser(int $userID) : bool
	{
		$this->clearError();
		if ($this->get('IdGroup') > 0) {
			$dbObj = new \Ximdex\Runtime\Db();
			$dbObj->Execute(sprintf("DELETE FROM RelUsersGroups WHERE IdGroup= %d AND IdUser = %d", $this->get('IdGroup'), $userID));
			if ($dbObj->numErr) {
				$this->SetError(5);
				return false;
			}
		} else {
			$this->SetError(1);
			return false;
		}
		return true;
	}

	/**
	 * Associating an existing user to a group with a concrete role
	 * 
	 * @param int $userID
	 * @param int $roleID
	 * @return boolean
	 */
	public function addUserWithRole(int $userID, int $roleID) : bool
	{
		$this->clearError();
		if ($this->get('IdGroup')) {
			$dbObj = new \Ximdex\Runtime\Db();
			$query = sprintf("SELECT IdRel FROM RelUsersGroups WHERE IdUser = %d AND IdGroup = %d AND IdRole = %d",
				$userID, $this->get('IdGroup'), $roleID);
			$dbObj->query($query);
			if ($dbObj->numRows > 0) {
				$this->SetError(8);
				return false;
			}
			$query = sprintf("INSERT INTO RelUsersGroups (IdUser, IdGroup, IdRole) VALUES (%d, %d, %d)",
				$userID, $this->get('IdGroup'), $roleID);
			$dbObj->Execute($query);
			if ($dbObj->numErr) {
				$this->SetError(5);
				return false;
			}
		} else {
			$this->SetError(1);
			return false;
		}
		return true;
	}

	/**
	 * Allows to change the role used by an user in a group
	 * 
	 * @param int $userID
	 * @param int $roleID
	 * @return bool
	 */
	public function changeUserRole(int $userID, int $roleID) : bool
	{
		$this->clearError();
		if ($this->get('IdGroup') > 0) {
			$dbObj = new \Ximdex\Runtime\Db();
			$query = sprintf("UPDATE RelUsersGroups SET IdRole = %d WHERE IdGroup= %d AND IdUser= %d", $roleID, $this->get('IdGroup'), $userID);
			$dbObj->Execute($query);
			if ($dbObj->numErr) {
				$this->SetError(5);
			    return false;
			}
		} else {
			$this->SetError(1);
			return false;
		}
		return true;
	}

	/**
	 * Returns true if the user belongs to a group
	 * 
	 * @param int $userID
	 */
	public function hasUser(int $userID)
	{
		$this->clearError();
		if ($this->get('IdGroup') > 0) {
			$dbObj = new \Ximdex\Runtime\Db();
			$query = sprintf("SELECT IdUser FROM RelUsersGroups WHERE IdGroup = %d AND IdUser = %d", $this->get('IdGroup'), $userID);
			$dbObj->Query($query);
			if (! $dbObj->numErr) {
				if ($dbObj->numRows) {
					return true;
				}
				return false;
			}
			$this->SetError(5);
		} else {
			$this->SetError(1);
		}
	}

	public function getRoleOnNode(int $nodeID)
	{
		$this->clearError();
		if ($this->get('IdGroup') > 0) {
			$node = new Node($nodeID);
			if (! $node->numErr) {
				return $node->GetRoleOfGroup($this->get('IdGroup'));
			}
		} else {
			$this->SetError(1);
		}
	}

	public function isOnNode(int $nodeID)
	{
		$this->clearError();
		if ($this->get('IdGroup') > 0) {
			$node = new Node($nodeID);
			if (! $node->numErr) {
				$groupList = $node->GetGroupList();
				if (in_array($this->GetID(), $groupList)) {
					return true;
				}
				return false;
			}
		} else {
			$this->SetError(1);
		}
	}

	/**
	 * Cleans class errors
	 */
	public function clearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	 * Loads class error
	 * 
	 * @param int $code
	 */
	public function setError(int $code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 * Returns true if there was an error in the class
	 * 
	 * @return boolean
	 */
	public function hasError()
	{
		return ($this->numErr != null);
	}
    
	public function getUserRoleInfo()
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf('SELECT IdRel, IdUser, IdRole FROM RelUsersGroups WHERE IdGroup = %s'
		    , $dbObj->sqlEscapeString($this->IdGroup));
		$dbObj->Query($query);
		if (! $dbObj->numRows) {
			return null;
		}
		$result = array();
		while (! $dbObj->EOF) {
			$result[$dbObj->getValue('IdRel')] = array('IdUser' => $dbObj->getValue('IdUser'), 'IdRole' => $dbObj->getValue('IdRole'));
			$dbObj->next();
		}
		return $result;
	}

	public static function getSelectableGroupsInfo(int $idNode)
	{
		$groupStateInfo = array();
		$group = new Group();
		$groupList = $group->find('IdGroup', NULL, NULL, MONO);
		$node = new Node($idNode);
		$groupState = array();
		if (is_array($groupList) && ! empty($groupList)) {
			foreach ($groupList as $idGroup) {
				$group = new Group($idGroup);
				$users = $group->GetUserList();
				if (is_array($users) && ! empty($users)) {
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
			$workflow = new Workflow($node->nodeType->getWorkflow(), $idState);
			$idS = $workflow->getStatusID();
			$idG = $group->get('Name');
			$sN = $workflow->getStatusName();
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
