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
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Permissions_ORM.class.php';

/// Clase Permission

class Permission extends Permissions_ORM 
	{
	var $permissionID;
	var $dbObj;
	var $numErr;				// Error code
	var $msgErr;				// Error message
	var $errorList= array(		// Class error list
		1 => 'Database connection error',
		2 => 'Permit does not exist',
		3 => 'Not initialized object'
		);	
	
   	var $_fieldsToTraduce = array('Description');
	
	//Constructor
	function Permission($_params = null)  {
		$this->errorList[1]=_('Database connection error');
		$this->errorList[2]=_('Permit does not exist');
		$this->errorList[3]=_('Not initialized object');

		parent::__construct($_params);
	}
		
	function GetAllPermissions()
		{
		$this->ClearError();

		$sql = "SELECT IdPermission FROM Permissions";
		$dbObj = new DB();
		$dbObj->Query($sql);
		if(!$dbObj->numErr) {
			while(!$dbObj->EOF) {
				$salida[] = $dbObj->GetValue("IdPermission");
				$dbObj->Next();
			}
			return $salida;
		}
		else
			$this->SetError(1);
		}
		
		
 	// Returns the idPermission of an object
 	function GetID()
	{
		return $this->get('IdPermission');
	}
    
	// Allows as to change the object idPermission. This avoid the have to destroy and re-create
	function SetID($id = null)
	{
		$this->ClearError();
		parent::GenericData($id);
		if (!($this->get('IdPermission') > 0)) {
			$this->SetError(2);
			return null;
		}
		return $this->get('IdPermission');
	}
		
	function SetByName($name)	
	{
		$this->ClearError();
		$dbObj = new DB();
 		$dbObj->Query(sprintf("SELECT IdPermission FROM Permissions WHERE Name = %s", $dbObj->sqlEscapeString($name)));
		if($dbObj->numRows)	{
			$this->SetID($dbObj->GetValue("IdPermission"));
		} else {
			$this->permissionID = null;
			$this->SetError(2);
		}
	}
    
	// Returns the user name associated to an idPermission
	function GetName()
	{
		return $this->get("Name");
	}
		
		
	// UPdates the database with the new permit name
	function SetName($name)
	{
		$this->ClearError();
		if (!($this->get('IdPermission') > 0)) {
			$this->SetError(2);
			return false;
		}
		
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}
		

	function GetDescription()
	{
		return $this->get("Description");
	}
	
		
	function SetDescription($description)
	{
		$this->ClearError();
		if (!($this->get('IdPermission') > 0)) {
			$this->SetError(2);
			return false;
		}
		
		$result = $this->set('Description', $description);
		if ($result) {
			return $this->update();
		}
		return false;
	}
		
		
	function add() {
		$this->CreateNewPermission($this->get('Name'));
	}
	
	// Creates a new permit if this not exist in the database and loads its idPermission
	function CreateNewPermission($name, $pID=NULL)
	{
		$dbObj = new DB();
		$dbObj->Query("SELECT IdPermission FROM Permissions WHERE Name = %s", $dbObj->sqlEscapeString($name));
		if (!$dbObj->numRows) {
			$this->set('Name', $name);
			if (!empty($pID)) {
				$this->set('IdPermission', $pID);
			}
			parent::add();
			return $this->get('IdPermission');
		}
		return false;
  	}

  	function delete() {
  		$this->DeletePermission();
  	}
	
	// Deletes the current permit
	function DeletePermission()
	{
		$this->ClearError();
		if($this->get('IdPermission') > 0) {
			parent::delete();
		} else {
			$this->SetError(3);
		}
	}
	
	function AddRole($rID)
	{
		$myrole = new Role($rID);
		$myrole->AddPermission($this->get('IdPermission'));
	}
		
	function DeleteRole($rID)
	{
		$myrole = new Role($rID);
		$myrole->DeletePermission($this->get('IdPermission'));
	}
	
		
	/// Cleans the class errors
	function ClearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}
		
	/// Loads the class error
	function SetError($code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}
        

	// Returns true if the class had an error
	function HasError()
	{
		return ($this->numErr != null);
	}
}
