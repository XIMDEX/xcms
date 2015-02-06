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
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Roles_ORM.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/model/RelRolesPermission.class.php';

class Role extends Roles_ORM {

    var $ID;  // Current role id
    var $flagErr;    // Shows if there was an error
    var $numErr;    // Error code
    var $msgErr;    // Error message
    //    var $dbObj;		// DB object used in methods
    var $errorList = array(// Class error list
        1 => 'Database connection error',
        2 => 'Role does not exist'
    );

    function Role($roleID = null) {
        parent::GenericData($roleID);
        $this->flagErr = FALSE;
        $this->autoCleanErr = TRUE;
        $this->errorList[1] = _('Database connection error');
        $this->errorList[2] = _('Role does not exist');
    }

    function GetByName($name) {
        $dbObj = new DB();
        $name = $dbObj->sqlEscapeString($name);

        if (empty($name))
            return 0;


        $sql = sprintf("SELECT IdRole FROM Roles Where Name = %s LIMIT 1", $name);
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            return (int) $dbObj->GetValue('IdRole');
        } else {
            return 0;
        }
    }

    // Returns an array with the id of all the system roles
    // return array of idRole
    function GetAllRoles() {
        $sql = "SELECT IdRole FROM Roles ORDER BY Name";
        $dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->row["IdRole"];
            $dbObj->Next();
        }
        return $salida ? $salida : NULL;
    }

    // Returns the current role id
    function GetID() {
        return $this->get('IdRole');
    }

    // Changes the current role id
    //  return int (status)
    function SetID($roleID) {
        parent::GenericData($roleID);
        return $this->get('IdRole');
    }

    // Returns the current role name
    // return string(name)
    function GetName() {
        return $this->get("Name");
    }

    //Changes the current role name
    // return int (status)
    function SetName($name) {
        if (!($this->get('IdRole') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Returns the current role description
    // return string (description)
    function GetDescription() {
        return $this->get("Description");
    }

    // Changes the current role description
    // return int (status)
    function SetDescription($description) {
        if (!($this->get('IdRole') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    // Returns the current role icon
    // return string (icon)
    function GetIcon() {
        return $this->get("Icon");
    }

    // Changes the current role icon
    // return int (status)
    function SetIcon($icon) {
        if (!($this->get('IdRole') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('Icon', $icon);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    function add() {
        return $this->CreateNewRole($this->get('Name'), $this->get('Icon'), $this->get('Description'), $this->get('IdRole'));
    }

    // Creates a new role and loads its roleID
    // return roleID - loaded as attribute
    function CreateNewRole($name, $icon, $description, $roleID) {
        $this->set('Name', $name);
        $this->set('Icon', $icon);
        $this->set('Description', $description);
        if ((int) $roleID > 0) {
            $this->set('IdRole', $roleID);
        }
        return parent::add();
    }

    function delete() {
        $this->DeleteRole();
    }

    // Deletes current role
    // return int (status)
    function DeleteRole() {
        $dbObj = new DB();
        $sql = sprintf("DELETE FROM RelRolesActions WHERE idRol = %d", $this->get('IdRole'));
        $dbObj->Execute($sql);
        if ($dbObj->numErr) {
            $this->SetError(1);
        }

        $this->DeleteAllStates();
        $this->DeleteAllPermissions();


        $dbObj = new DB();
        $sql = sprintf("DELETE FROM RelUsersGroups WHERE idRol = %d", $this->get('IdRole'));
        $dbObj->Execute($sql);
        if ($dbObj->numErr) {
            $this->SetError(1);
        }

        $dbObj = new DB();
        $sql = sprintf("DELETE FROM RelGroupsNodes WHERE idRol = %d", $this->get('IdRole'));
        $dbObj->Execute($sql);
        if ($dbObj->numErr) {
            $this->SetError(1);
        }


        parent::delete();

        $this->ID = null;
    }

    /**
     * Returns if a given action belongs to the current role
     *
     * @param int $actionID
     * @param int $idState
     * @param int $idPipeline
     * @return boolean (hasPermission)
     */
    function HasAction($actionID, $idState = null, $idPipeline = null) {
        $dbObj = new DB();
        $query = sprintf("SELECT IdAction FROM RelRolesActions WHERE IdRol = %d"
                . " AND IdAction = %d ", $this->get('IdRole'), $actionID);

        if ((int) $idState > 0) {
            $query .= sprintf(" AND IdState = %d", $idState);
        } else {
            $query .= " AND ((IdState IS NULL) OR IdState = 0) ";
        }

        if ((int) $idPipeline > 0) {
            $query .= sprintf(" AND IdPipeline = %d", $idPipeline);
        }


        $dbObj->Query($query);
        if ($dbObj->numErr)
            $this->SetError(1);

        return ($dbObj->numRows > 0);
    }

    // Adds an action to current role
    // return int (status)
    function AddAction($actionID, $stateID = null, $idPipeline) {
        $dbObj = new DB();
        $sql = sprintf("INSERT INTO RelRolesActions (IdRol,IdAction,IdState, IdPipeline)"
                . " VALUES (%s, %s, %s, %s)", $dbObj->sqlEscapeString($this->get('IdRole')), $dbObj->sqlEscapeString($actionID), $dbObj->sqlEscapeString($stateID), $dbObj->sqlEscapeString($idPipeline)
        );

        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Returns an array with the associations related to the current role
    // return array of ID (actionID)
    function GetActionsList($stateID = null) {
        $sql = sprintf("SELECT IdAction FROM RelRolesActions"
                . " WHERE IdRol = %d", $this->get('IdRole'));
        if ($stateID > 0)
            $sql .= sprintf(" AND IdState = %d", $stateID);
        else
            $sql .= " AND ((IdState IS NULL) OR IdState = 0) ";

        $dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = array();
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->GetValue("IdAction");
            $dbObj->Next();
        }
        // print_r($salida);
        return $salida;
    }

    /**
     * Obtains the list of available actions of a node.
     * @param int $nodeID
     * @return array
     */
    function GetActionsOnNode($nodeID) {
        $node = new Node($nodeID);
        if ($node->get('IdNode') > 0) {
            $nodeType = $node->get('IdNodeType');
            $stateID = $node->get('IdState');

            if ($nodeType) {
                $result = array();
                $action = new Action();
                $actions1 = $action->GetActionListOnNodeType($nodeType);
                $actions2 = $this->GetActionsList($stateID);

                if ($actions1 && $actions2) {
                    $result = array_intersect($actions1, $actions2);
                }
                return $result;
            }
        }
        return false;
    }

    // Returns if the given permit belongs to the current role
    // return boolean (hasPermission)
    function HasPermission($permissionID) {
        $relRolesPermission = new RelRolesPermission();
        return count($relRolesPermission->find('IdRel', 'IdRole = %s AND IdPermission = %s', array($this->get('IdRole'), $permissionID), MONO)) > 0;
    }

    // Add a new permit to the current role
    // return int (status)
    function AddPermission($permissionID) {
        $relRolesPermission = new RelRolesPermission();
        $relRolesPermission->set('IdRole', $this->get('IdRole'));
        $relRolesPermission->set('IdPermission', $permissionID);
        return $relRolesPermission->add();
    }

    // Deletes a permit of the current role
    // return int (status)
    function DeletePermission($permissionID) {
        $sql = sprintf("DELETE FROM RelRolesPermissions"
                . " WHERE IdRole = %d"
                . " AND IdPermission = %d", $this->get('IdRole'), $permissionID);
        $dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Deletes all the permits of the current role
    // return int (status)
    function DeleteAllPermissions() {
        $sql = sprintf("DELETE FROM RelRolesPermissions" .
                " WHERE IdRole = %d", $this->get('IdRole'));

        $dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    function deleteAllRolesActions($idPipeline) {
        $sql = sprintf("DELETE FROM RelRolesActions" .
                " WHERE IdRol = %d AND IdPipeline = %d", $this->get('IdRole'), $idPipeline
        );


        $dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Returns an array with the permits of the current role
    // return array of ID (actionID)
    function GetPermissionsList() {
        $relRolesPermission = new RelRolesPermission();
        return $relRolesPermission->find('IdPermission', 'IdRole = %s', array($this->get('IdRole')), MONO);
    }

    // Returns if the given transition belongs to the current role
    // return boolean (hasPermission)
    function HasState($transitionID) {
        $dbObj = new DB();
        $query = sprintf("SELECT IdRel FROM RelRolesStates WHERE IdRole = %d AND  IdState = %d", $this->get('IdRole'), $transitionID);
        $dbObj->Query($query);
        if ($dbObj->numErr)
            $this->SetError(1);

        return $dbObj->numRows;
    }

    // Add a transition to the current role
    // return int (status)
    function AddState($transitionID) {
        $dbObj = new DB();
        $sql = sprintf("INSERT INTO RelRolesStates (IdRole,IdState)" .
                " VALUES (%d, %d)", $this->get('IdRole'), $transitionID);
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Deletes a transition of the current role
    // return int (status)
    function DeleteState($transitionID) {
        $sql = sprintf("DELETE FROM RelRolesStates"
                . " WHERE IdRole = %d"
                . " AND IdState = %d", $this->get('IdRole'), $transitionID);
        $dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Deletes all the permits of the current role
    // return int (status)
    function DeleteAllStates() {
        $sql = sprintf("DELETE FROM RelRolesStates" .
                " WHERE IdRole = %d", $this->get('IdRole'));
        $dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    // Returns an array with to current role transitions
    // return array of ID (actionID)
    function GetAllStates() {
        $salida = NULL;
        $sql = sprintf("SELECT IdState FROM RelRolesStates"
                . " WHERE IdRole = %d", $this->get('IdRole'));
        $dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->row["IdState"];
            $dbObj->Next();
        }
        // print_r($salida);
        return $salida;
    }

    function getAllRolesForStatus($idStatus) {
        $db = new db();
        $query = sprintf("SELECT IdRole FROM RelRolesStates WHERE IdState = %d", $idStatus);
        $db->Query($query);

        $foundRoles = array();
        while (!$db->EOF) {
            $foundRoles[] = $db->getValue('IdRole');
            $db->Next();
        }
        return $foundRoles;
    }

    //  Cleans last error
    function ClearError() {
        $this->flagErr = FALSE;
    }

    function SetAutoCleanOn() {
        $this->autoCleanErr = TRUE;
    }

    function SetAutoCleanOff() {
        $this->autoCleanErr = FALSE;
    }

    /// Loads a class error
    function SetError($code) {
        $this->flagErr = TRUE;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    // returns true if there was an error in the class
    function HasError() {
        $aux = $this->flagErr;
        if ($this->autoCleanErr)
            $this->ClearError();
        return $aux;
    }

    /**
     * This function replaces  workflow->getAllowedStates,
     * It is considered the the approppriate place is in roles
     *
     * @return array
     */
    function GetAllowedStates() {
        if (!($this->get('IdRole') > 0)) {
            return NULL;
        }

        $dbObj = new DB();
        $query = sprintf("SELECT IdState FROM RelRolesStates WHERE IdRole = %s AND IdState > 0", $dbObj->sqlEscapeString($this->get('IdRole')));

        $dbObj->Query($query);
        $result = NULL;
        while (!$dbObj->EOF) {
            $result[] = $dbObj->GetValue("IdState");
            $dbObj->Next();
        }
        return $result;
    }

    /** Function which returns the IdNode for demo user role (defined at the beggining of the file)
     *  @return IdNode - Idnode for node named as param
     */
    function getDemoRoleFromName($roleName) {
        $sql = "SELECT IdRole FROM Roles where Name like \"" . $roleName . "\"";
        $dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        return $dbObj->row["IdRole"];
    }

}
