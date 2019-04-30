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

use Ximdex\Models\ORM\RolesOrm;

class Role extends RolesOrm
{
    const ADMINISTRATOR = 201;
    
    /**
     * Current role id
     * 
     * @var int
     */
    public $ID;
    
    /**
     * Shows if there was an error
     * 
     * @var bool
     */
    public $flagErr = false;
    
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
    
    public $autoCleanErr = true;

    /**
     * Role constructor
     * 
     * @param $roleID
     */
    public function __construct(int $roleID = null)
    {
        parent::__construct($roleID);
        $this->flagErr = false;
        $this->autoCleanErr = true;
        $this->errorList[1] = _('Database connection error');
        $this->errorList[2] = _('Role does not exist');
    }

    public static function getByName(string $name = null) : int
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $name = $dbObj->sqlEscapeString($name);
        if (empty($name)) {
            return 0;
        }
        $sql = sprintf('SELECT IdRole FROM Roles Where Name = %s LIMIT 1', $name);
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            return (int) $dbObj->GetValue('IdRole');
        } else {
            return 0;
        }
    }

    /**
     * Returns an array with the id of all the system roles
     * 
     * @return NULL|array
     */
    public function getAllRoles()
    {
        $sql = 'SELECT IdRole FROM Roles ORDER BY Name';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = array();
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->row['IdRole'];
            $dbObj->Next();
        }
        return $salida ? $salida : null;
    }

    /**
     * Returns the current role id
     * 
     * @return boolean|string
     */
    public function getID()
    {
        return $this->get('IdRole');
    }

    /**
     * Changes the current role id
     * 
     * @param $roleID
     * @return boolean|string
     */
    public function setID($roleID)
    {
        parent::__construct($roleID);
        return $this->get('IdRole');
    }

    /**
     * Returns the current role name
     * 
     * @return boolean|string
     */
    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Changes the current role name
     * 
     * @param string $name
     * @return boolean|number|NULL|string
     */
    public function setName(string $name)
    {
        if (! $this->get('IdRole')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current role description
     * 
     * @return boolean|string
     */
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Changes the current role description
     * 
     * @param string $description
     * @return boolean|number|NULL|string
     */
    public function setDescription(string $description = null)
    {
        if (! $this->get('IdRole')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current role icon
     * 
     * @return boolean|string|null
     */
    public function getIcon()
    {
        return $this->get('Icon');
    }

    /**
     * Changes the current role icon
     * 
     * @param string $icon
     * @return bool|number|NULL|string
     */
    function setIcon(string $icon = null)
    {
        if (! $this->get('IdRole')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Icon', $icon);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /** 
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add(bool $useAutoIncrement = true)
    {
        return $this->createNewRole($this->get('Name'), $this->get('Icon'), $this->get('Description'), $this->get('IdRole'));
    }

    /**
     * Creates a new role and loads its roleID
     * 
     * @param string $name
     * @param string $icon
     * @param string $description
     * @param int $roleID
     * @return boolean|NULL|string
     */
    public function createNewRole(string $name, string $icon = null, string $description = null, int $roleID = null)
    {
        $this->set('Name', $name);
        $this->set('Icon', $icon);
        $this->set('Description', $description);
        if ($roleID) {
            $this->set('IdRole', $roleID);
        }
        return parent::add();
    }

    /**
     * Deletes current role
     * 
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::delete()
     */
    public function delete()
    {
        $generalGroup = new Group(Group::getGeneralGroup());
        if (! $generalGroup->getID()) {
            return false;
        }
        $roles = $generalGroup->getRoles();
        if (in_array($this->IdRole, $roles)) {
            
            // If this role is in use in the general group, deletion is denied
            $this->messages->add(_('Cannot delete a role already in use by an user in the general group'), MSG_TYPE_ERROR);
            return false;
        }
        if (parent::delete() === false) {
            return false;
        }
        $this->ID = null;
        return true;
    }

    /**
     * Returns if a given action belongs to the current role
     *
     * @param int $actionID
     * @param int $idState
     * @param int $workFlowId
     * @return boolean (hasPermission)
     */
    public function hasAction(int $actionID, int $idState = null, int $workFlowId = null) : bool
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdAction FROM RelRolesActions WHERE IdRol = %d AND IdAction = %d', $this->get('IdRole'), $actionID);
        if ($idState) {
            $query .= sprintf(' AND IdState = %d', $idState);
        } else {
            $query .= ' AND IdState IS NULL';
        }
        $dbObj->Query($query);
        if ($dbObj->numErr) {
            $this->SetError(1);
        }
        return ($dbObj->numRows > 0);
    }

    /**
     *  Adds an action to current role
     *  
     * @param int $actionID
     * @param int $stateID
     * @param int $workflowID
     * @return bool
     */
    public function addAction(int $actionID, int $stateID = null, int $workFlowId = null) : bool
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf('INSERT INTO RelRolesActions (IdRol, IdAction, IdState) VALUES (%s, %s, %s)'
            , $this->get('IdRole'), $actionID, ($stateID) ? $stateID : 'NULL');
        $dbObj->execute($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
            return false;
        }
        return true;
    }

    /**
     * Returns an array with the associations related to the current role
     * 
     * @param int $stateID
     * @return NULL|array
     */
    public function getActionsList(int $stateID = null)
    {
        $sql = sprintf('SELECT IdAction FROM RelRolesActions WHERE IdRol = %d', $this->get('IdRole'));
        if ($stateID) {
            $sql .= sprintf(' AND IdState = %d', $stateID);
        } else {
            $sql .= ' AND IdState IS NULL';
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
            return null;
        }
        $salida = array();
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->getValue('IdAction');
            $dbObj->next();
        }
        return $salida;
    }

    /**
     * Obtains the list of available actions of a node
     * 
     * @param int $nodeID
     * @param bool $includeActionsWithNegativeSort
     * @return array|bool
     */
    public function getActionsOnNode(int $nodeID, bool $includeActionsWithNegativeSort = false)
    {
        $node = new Node($nodeID);
        if ($node->get('IdNode')) {
            $nodeType = $node->get('IdNodeType');
            $stateID = $node->get('IdState');
            if ($nodeType) {
                $result = array();
                $action = new Action();
                $actions1 = $action->getActionListOnNodeType($nodeType, $includeActionsWithNegativeSort);
                $actions2 = $this->getActionsList($stateID);
                if ($actions1 && $actions2) {
                    $result = array_intersect($actions1, $actions2);
                }
                return $result;
            }
        }
        return false;
    }
    
    /**
     * Returns if the given permit belongs to the current role
     * 
     * @param int $permissionID
     * @return bool
     */
    public function hasPermission(int $permissionID)
    {
        $relRolesPermission = new RelRolesPermission();
        return count($relRolesPermission->find('IdRel', 'IdRole = %s AND IdPermission = %s', array($this->get('IdRole')
            , $permissionID), MONO)) > 0;
    }

    /**
     * Add a new permit to the current role
     * 
     * @param int $permissionID
     * @return boolean|NULL|string
     */
    public function addPermission(int $permissionID)
    {
        $relRolesPermission = new RelRolesPermission();
        $relRolesPermission->set('IdRole', $this->get('IdRole'));
        $relRolesPermission->set('IdPermission', $permissionID);
        return $relRolesPermission->add();
    }

    /**
     * Deletes a permit of the current role
     * 
     * @param int $permissionID
     */
    public function deletePermission(int $permissionID)
    {
        $sql = sprintf('DELETE FROM RelRolesPermissions WHERE IdRole = %d AND IdPermission = %d', $this->get('IdRole'), $permissionID);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    /**
     * Deletes all the permits of the current role
     * 
     * @return bool
     */
    public function deleteAllPermissions() : bool
    {
        $sql = sprintf('DELETE FROM RelRolesPermissions WHERE IdRole = %d', $this->get('IdRole'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return false;
        }
        return true;
    }

    public function deleteAllRolesActions() : bool
    {
        $sql = sprintf('DELETE FROM RelRolesActions WHERE IdRol = %d', $this->get('IdRole'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return false;
        }
        return true;
    }

    /**
     * Returns an array with the permits of the current role
     * 
     * @return array|boolean
     */
    public function getPermissionsList()
    {
        $relRolesPermission = new RelRolesPermission();
        return $relRolesPermission->find('IdPermission', 'IdRole = %s', array($this->get('IdRole')), MONO);
    }

    /**
     * Returns if the given transition belongs to the current role
     * 
     * @param int $state
     * @return number|boolean
     */
    public function hasState(int $state)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdRel FROM RelRolesStates WHERE IdRole = %d AND IdState = %d', $this->get('IdRole'), $state);
        $dbObj->Query($query);
        if ($dbObj->numErr) {
            $this->SetError(1);
        }
        return $dbObj->numRows;
    }

    /**
     * Add a transition to the current role
     * 
     * @param int $state
     */
    public function addState(int $state)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf('INSERT INTO RelRolesStates (IdRole,IdState) VALUES (%d, %d)', $this->get('IdRole'), $state);
        $dbObj->execute($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
        }
    }

    /**
     * Deletes a transition of the current role
     * 
     * @param int $state
     */
    public function deleteState(int $state)
    {
        $sql = sprintf('DELETE FROM RelRolesStates WHERE IdRole = %d AND IdState = %d', $this->get('IdRole'), $state);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->execute($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
        }
    }

    /**
     * Deletes all the permits of the current role
     */
    public function deleteAllStates()
    {
        $sql = sprintf('DELETE FROM RelRolesStates WHERE IdRole = %d', $this->get('IdRole'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->execute($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
        }
    }

    /**
     * Returns an array with to current role transitions
     * return array of ID (actionID) or NULL (in error)
     * 
     * @return NULL|array
     */
    public function getAllStates()
    {
        $sql = sprintf('SELECT IdState FROM RelRolesStates WHERE IdRole = %d', $this->get('IdRole'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
            return null;
        }
        $salida = [];
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->row['IdState'];
            $dbObj->next();
        }
        return $salida;
    }

    /**
     * @param int $idStatus
     * @return array
     */
    public function getAllRolesForStatus(int $idStatus) : array
    {
        $db = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdRole FROM RelRolesStates WHERE IdState = %d', $idStatus);
        $db->query($query);
        $foundRoles = array();
        while (! $db->EOF) {
            $foundRoles[] = $db->getValue('IdRole');
            $db->next();
        }
        return $foundRoles;
    }

    public function clearError()
    {
        $this->flagErr = false;
    }

    public function setAutoCleanOn()
    {
        $this->autoCleanErr = true;
    }

    public function setAutoCleanOff()
    {
        $this->autoCleanErr = false;
    }

    public function setError(int $code)
    {
        $this->flagErr = true;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    public function hasError() : bool
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr) {
            $this->clearError();
        }
        return $aux;
    }

    /**
     * This function replaces workflow->getAllowedStates, It is considered the apropriate place is in roles
     *
     * @return array
     */
    public function getAllowedStates() : array
    {
        if (! $this->get('IdRole')) {
            return [];
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdState FROM RelRolesStates WHERE IdRole = %s AND IdState > 0', $dbObj->sqlEscapeString($this->get('IdRole')));
        $dbObj->query($query);
        $result = [];
        while (! $dbObj->EOF) {
            $result[] = $dbObj->getValue('IdState');
            $dbObj->next();
        }
        return $result;
    }

    /**
     * Function which returns the IdNode for demo user role (defined at the beggining of the file)
     * 
     * @param string $roleName
     * @return null|string
     */
    public function getDemoRoleFromName(string $roleName)
    {
        $sql = 'SELECT IdRole FROM Roles where Name like "' . $roleName . '"';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($sql);
        if ($dbObj->numErr != 0) {
            $this->setError(1);
            return null;
        }
        return $dbObj->row['IdRole'];
    }
}
