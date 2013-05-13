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




ModulesManager::file('/inc/model/orm/RelNewsArea_ORM.class.php', 'ximNEWS');

class RelNewsArea extends RelNewsArea_ORM {
	
	/**
	*  Checks if exist a row from RelNewsArea which matching the values of IdArea and IdNew.
	*  @param int IdArea
	*  @param int IdNew
	*  @return bool
	*/

	function hasAreas($IdArea = 0,$IdNew = 0) {
		
		$wheres = array();
		
		if (empty($IdArea) && empty($IdNew)) {
			return false;
		}
		
		$dbConn = new DB();
		if (!empty($IdArea)) {
			$wheres[] = sprintf('IdArea = %s', $dbConn->sqlEscapeString($IdArea));
		}

		if (!empty($IdNew)) {
			$wheres[] = sprintf('IdNew = %s', $dbConn->sqlEscapeString($IdNew));
		}

		$whereString = '';
		if (!empty($wheres)) {
			$whereString = implode(' AND ', $wheres);
		}
		
		$query = sprintf("SELECT IdRel FROM RelNewsArea WHERE %s", $whereString);
		$dbConn->Query($query);
		
		return ($dbConn->numRows > 0) ? true : false;
	}

	/**
	*  Gets the rows from RelNewsArea which matching the value of IdArea.
	*  @param int areaID
	*  @return array
	*/

	function GetNewsByArea($areaID){
		$result = array();
		$dbObj = new DB();
		$query = sprintf("SELECT IdNew FROM RelNewsArea WHERE IdArea = %s", $dbObj->sqlEscapeString($areaID));
		$dbObj->Query($query);
		if ($dbObj->numRows){
			while (!$dbObj->EOF) {
				$result[] = $dbObj->GetValue('IdNew');
				$dbObj->Next();
			}
		}
		return $result;
	}

	/**
	*  Deletes the rows from RelNewsArea which matching the value of idNew.
	*  @param int idNew
	*  @return array
	*/

	function deleteByNew($idNew) {
		$dbObj = new DB();
		$sql = sprintf("DELETE FROM RelNewsArea WHERE IdNew = %s", $dbObj->sqlEscapeString($idNew));
		return $dbObj->Execute($sql);
	}
	
	/**
	*  Gets all areas associated with a news
	*  @param int newsContainer
	*  @return array
	*/

	function getAreasByNew($newsContainer) {

		$node = new Node($newsContainer);
		$news = $node->getChildren();
		$news = implode(',', $news);

		return $this->find('DISTINCT(IdArea)', "IdNew in ($news)", NULL, MONO);
	}
}
?>
