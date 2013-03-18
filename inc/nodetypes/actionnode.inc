<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

include_once XIMDEX_ROOT_PATH . "/inc/model/action.inc";
require_once (XIMDEX_ROOT_PATH . "/inc/nodetypes/root.inc");

/**
*  @brief Handles the Ximdex actions.
*/

class ActionNode extends Root {

	/**
	*  Calls to method for adding a row to Actions table.
	*  @param string name
	*  @param int parentID
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param string command
	*  @param string icon
	*  @param string description
	*  @return unknown
	*/

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $command = null, $icon = null, $description = null) {

		$action = new Action();
		$action->CreateNewAction($this->parent->get('IdNode'), $parentID, $name, $command, $icon, $description, $stateID);
		$this->UpdatePath();
	}

	/**
	*  Does nothing.
	*  @return null
	*/

	function RenderizeNode() {

		return null;
	}

	/**
	*  Calls to method for updating the Name on the database.
	*  @param string name
	*  @return unknown
	*/

	function RenameNode($name = null) {

	 	$action = new Action($this->nodeID);
		$action->SetName($name);
		$this->UpdatePath();
	}

	/**
	*  Calls to method for deleting.
	*  @return unknown
	*/

	function DeleteNode() {

	 	$action = new Action($this->nodeID);
		$action->DeleteAction();
	}
}
?>
