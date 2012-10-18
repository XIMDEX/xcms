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
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Actions_ORM.class.php';

class Action extends Actions_ORM {

	/**
	 * ID of the current action.
	 * @var unknown_type
	 */
	var $ID;
	/**
	 * DB object used in methods.
	 * @var unknown_type
	 */
	var $dbObj;
	/**
	 * Shows if there was an error.
	 * @var unknown_type
	 */
	var $flagErr;
	/**
	 * Error code
	 * @var unknown_type
	 */
	var $numErr;
	/**
	 * Error message
	 * @var unknown_type
	 */
	var $msgErr;
	/**
	 * Class error list.
	 * @var unknown_type
	 */
	var $errorList= array(
	1 => 'Database connection error',
	2 => 'Action does not exist'
	);
	var $_fieldsToTraduce = array('Name', 'Description');

	/**
	 *
	 * @param $actionID
	 * @return unknown_type
	 */
	function Action($actionID = null)
	{
		$this->flagErr = FALSE;
		$this->autoCleanErr = TRUE;
		$errorlist[1] = _('Database connection error');
		$errorlist[2] = _('Action does not exist');

		parent::GenericData($actionID);
	}

	/**
	 * Returns an arry with the ids of all the system actions.
	 * @return array of ActionID
	 */
	function GetAllActions() {
		$sql = "SELECT IdAction FROM Actions";
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0)
		{
			$this->SetError(1);
			return null;
		}
		while (!$dbObj->EOF) {
			$salida[] = $dbObj->GetValue("IdAction");
			$dbObj->Next();
		}
		return $salida;
	}

	/**
	 * Returns an array with the ids of all the action of a nodetype.
	 * @param $nodeType
	 * @return array of ActionID
	 */
	function GetActionListOnNodeType($nodeType = NULL) {
		$dbObj = new DB();
		$sql = sprintf("SELECT IdAction FROM Actions WHERE idNodeType = %d AND Sort > 0", $nodeType);
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
			$this->SetError(1);
			return null;
		}
		$salida = null;
		while (!$dbObj->EOF) {
			$salida[] = $dbObj->GetValue("IdAction");
			$dbObj->Next();
		}
		return $salida ? $salida : NULL;
	}

	/**
	 * Returns the nodetype id assocaited with the current action.
	 * @return NodeType
	 */
	function GetNodeType() {
		return $this->get('IdNodeType');
	}

	/**
	 * Returns the current action id.
	 * @return actionID
	 */
	function GetID() {
		return $this->get('IdAction');
	}

	/**
	 * Changes the current action id.
	 * @param $actionID
	 * @return int (status)
	 */
	function SetID($actionID) {
		parent::GenericData($actionID);
		if (!($this->get('IdAction') > 0)) {
			$this->SetError(2);
			return null;
		}
		return $this->get('IdAction');
	}

	/**
	 *  Returns the current action name.
	 * @return string(name)
	 */
	function GetName() {
		return $this->get('Name');
	}

	/**
	 * Changes the current action name.
	 * @param $name
	 * @return int (status)
	 */
	function SetName($name) {
		if (!($this->get('IdAction') > 0)) {
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
	 *  Returns the current action description.
	 * @return string (description)
	 */
	function GetDescription () {
		return $this->get('Description');
	}

	/**
	 * Change the current action description.
	 * @param $description
	 * @return int (status)
	 */
	function SetDescription($description) {
		if (!($this->get('IdAction') > 0)) {
			$this->SetError(2, _('Action does not exist'));
			return false;
		}

		$result = $this->set('Description', $description);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the current action command.
	 * @return string (command)
	 */
	function GetCommand() {
		return $this->get('Command');
	}

	/**
	 * Changes the current action command. 
	 * @param $command
	 * @return  int (status)
	 */
	function SetCommand($command) {
		if (!($this->get('IdAction') > 0)) {
			$this->SetError(2, _('Action does not exist'));
			return false;
		}

		$result = $this->set('Command', $command);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the current action order.
	 * @return string (command)
	 */
	function GetSort() {
		return $this->get('Sort');
	}

	/**
	 * Changes the current action order. 
	 * @param $sort
	 * @return int (status)
	 */
	function SetSort($sort) {
		if (!($this->get('IdAction') > 0)) {
			$this->SetError(2, _('Action does not exist'));
			return false;
		}

		$result = $this->set('Sort', $sort);
		if ($result) {
			return $this->update();
		}
		return false;
	}
	
	/**
	* Returns the current action icon.
	* @return string (icon)
	*/
	function GetIcon() {
		return $this->get('Icon');
	}

	/**
	 * Changes the current action icon. 
	 * @param $icon
	 * @return int (status)
	 */
	function SetIcon($icon){
		if (!($this->get('IdAction') > 0)) {
			$this->SetError(2, _('Action does not exist'));
			return false;
		}

		$result = $this->set('Icon', $icon);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns if the given user can execute the action in a given node.
	 * @param $userID
	 * @param $nodeID
	 * @return  boolean (HasAccess)
	 */
	function CheckAccessPermissionOnNode($userID, $nodeID){
		;
	}

	/**
	 * Creates a new action and load its id in the class actionID.
	 * @param $actionID
	 * @param $nodeType
	 * @param $name
	 * @param $command
	 * @param $icon
	 * @param $description
	 * @return ActionID - loaded as a attribute
	 */
	function CreateNewAction($actionID, $nodeType, $name, $command, $icon, $description) {
		$this->set('IdAction', $actionID);
		$this->set('IdNodeType', $nodeType);
		$this->set('Name', $name);
		$this->set('Command', $command);
		$this->set('Icon', $icon);
		$this->set('Description', $description);
		$this->ID = $this->add();
		return $this->ID;
	}

	/**
	 * Delete current action.
	 * @return int (status)
	 */
	function DeleteAction() {
		$dbObj = new DB();
		$query = sprintf("DELETE FROM RelRolesActions WHERE IdAction= %d", $this->ID);
		$dbObj->Execute($query);
		if ($dbObj->numErr != 0)
		$this->SetError(1);

		$this->delete();
		$this->ID = null;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetModule() {
		return $this->get('Module');
	}

	/**
	 * Cleans last error.
	 * @return unknown_type
	 */
	function ClearError() {
		$this->flagErr = FALSE;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function SetAutoCleanOn() {
		$this->autoCleanErr = TRUE;
	}
	/**
	 *
	 * @return unknown_type
	 */
	function SetAutoCleanOff() {
		$this->autoCleanErr = FALSE;
	}
	/**
	 * Loads an errorin the class.
	 * @param $code
	 * @return unknown_type
	 */
	function SetError($code) {
		$this->flagErr = TRUE;
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 * Returns true if there was an error in the class.
	 * @return unknown_type
	 */
	function HasError() {
		$aux = $this->flagErr;
		if ($this->autoCleanErr)
		$this->ClearError();
		return $aux;
	}

	/**
	 *
	 * @param $name
	 * @param $idNodeType
	 * @return unknown_type
	 */
	function setByCommand($name, $idNodeType) {
		$result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s',
		array($name, $idNodeType), MONO);
		if (count($result) != 1) {
			return false;
		}
		$this->Action($result[0]);
		return $this->get('IdAction');
	}

	/**
	 *
	 * @param $name
	 * @param $idNode
	 * @param $module
	 * @return unknown_type
	 */
	function setByCommandAndModule($name, $idNode,  $module = null) {
		$node = new Node($idNode);
		$idNodeType = $node->GetNodeType();

		if($module == NULL ) {
			return $this->setByCommand($name, $idNodeType);
		}else {
			$result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s AND Module = %s',
			array($name, $idNodeType, $module), MONO);
			if (count($result) != 1) {
				return 0;
			}
			$this->Action($result[0]);
			return $this->get('IdAction');
		}

	}
}

?>
