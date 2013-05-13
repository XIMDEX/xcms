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




 


ModulesManager::file('/inc/db/db.inc');
ModulesManager::file('/inc/helper/Messages.class.php');
ModulesManager::file('/inc/model/orm/XimNewsAreas_ORM.class.php', 'ximNEWS');


class XimNewsAreas extends XimNewsAreas_ORM {
/*	var $errorList = array(	// Lista de errores de la clase.
			1 => 'Error de conexion con la base de datos.',
			2 => 'No existe la categoría.',
			3 => 'Error al crear la categoría.',
			4 => 'Error al actualizar la categoría.',
			5 => 'La noticia no está asociada a ninguna categoría.',
			6 => 'No existen categorías para asociar la noticia.'
		);
*/
	function XimNewsAreas($IdArea = NULL) {
		if (!is_null($IdArea)) {
			$this->set('IdArea', $IdArea);
		}
		parent::GenericData($IdArea);
	}
	
	/**
	*  A wrapper for CreateArea
	*/

	function add() {
		return $this->CreateArea($this->get('Name'), $this->get('Description'));
	}
	
	/**
	*  Adds a row to XimNewsAreas table.
	*  @param string name
	*  @param string description
	*  @return bool
	*/

	function CreateArea($name,$description) {
		if (empty($name)) {
			$this->messages->add(_('A name should be introduced to create a category'), MSG_TYPE_ERROR);
			return false;
		}
		if (empty($description)) {
			$this->messages->add(_('A category description was not introduced'), MSG_TYPE_WARNING);
		}
		
		$dbConn = new DB();
		$query = sprintf("SELECT IdArea FROM XimNewsAreas WHERE Name = %s", $dbConn->sqlEscapeString($name));
		$dbConn->Query($query);
		if ($dbConn->numRows > 0) {
			$this->messages->add(sprintf(_('A category with name %s already exists'), $name), MSG_TYPE_ERROR);
			return false;
		}
		unset($dbConn);

		$this->set('Name', $name);
		$this->set('Description', $description);
		$result = parent::add();
		if ($result > 0) {
			$this->messages->add(sprintf(_('A category with name %s has been inserted'), $name), MSG_TYPE_NOTICE);
		}
		return $result;
	}
	
	/**
	*  Gets all rows from XimNewsAreas 
	*  @return array
	*/

	function GetAllAreas() {
		$dbConn = new DB();
		$query = "SELECT IdArea,Name,Description FROM XimNewsAreas ORDER BY Name, Description ASC";
		
		$dbConn->Query($query);
		if(!($dbConn->numRows > 0)) {
			$this->messages->add(_('No categories found'), MSG_TYPE_WARNING);
		}
		
		$areas = array();
		while (!$dbConn->EOF) {
			$element = array();
			$element['IdArea'] = $dbConn->GetValue('IdArea');
			$element['Name'] = $dbConn->GetValue('Name');
			$element['Description'] = $dbConn->GetValue('Description');
			$areas[$dbConn->GetValue('IdArea')] = $element;
			$dbConn->Next();
		}
		return $areas;
	}
	
	/**
	*  Gets the field Name from a row of XimNewsAreas.
	*  @return string
	*/

	function GetName() {
		return $this->get('Name');
	}

	/**
	*  Gets the field Description from a row of XimNewsAreas.
	*  @return string
	*/

	function GetDescription() {
		return $this->get('Description');
	}

	/**
	*  A wrapper for delete.
	*  @return int
	*/

	function DeleteArea() {
		return parent::delete();
	}

	/**
	*  Change the entity of XimNewsAreas.
	*/

	function SetID($IdArea) {
		if ((int)$IdArea > 0) {
			parent::GenericData($IdArea);
		}
	}

	/**
	*  Deletes the rows from RelNewsArea which matching the values of IdNew and IdArea.
	*  @param int IdNews
	*  @param int IdArea
	*  @return bool
	*/

	function DeleteRelNewsArea($IdArea='',$IdNews='') {
		
		$dbConn = new DB();
		
		$query = "DELETE FROM RelNewsArea WHERE ";

		if (!empty($IdArea)) {
			$query .= sprintf('IdArea = %s', $dbConn->sqlEscapeString($IdArea));
		}

		if (!empty($IdNews)) {
			$query .= sprintf('AND IdNew = %s', $dbConn->sqlEscapeString($IdNews));
		}
		
		$dbConn->execute($query);
		
		return ($dbConn->numRows > 0) ? true : false;
	}
	
}
