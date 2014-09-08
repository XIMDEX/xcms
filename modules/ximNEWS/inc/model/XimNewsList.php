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



ModulesManager::file('/inc/model/orm/XimNewsList_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelColectorList.php', 'ximNEWS');

class XimNewsList extends XimNewsList_ORM {

	/**
	*  Gets the rows from XimNewsList join RelColectorList which matching the value of IdColector.
	*  @param int idColector
	*  @return array
	*/

	function getList($idColector) {
		$dbObj = new DB();
		$query = sprintf("SELECT xnl.IdList, xnl.Name"
					. " FROM XimNewsList as xnl"
					. " INNER JOIN RelColectorList ON xnl.IdList=RelColectorList.IdList"
					. " WHERE IdColector = %s",
					$dbObj->sqlEscapeString($idColector));
		
		$dbObj->Query($query);
		
		$result = array();
		while (!$dbObj->EOF) {
			$result[$dbObj->GetValue('IdList')] = $dbObj->GetValue('Name');
			$dbObj->Next();
		}
		return $result;
	}
	
	/**
	*  Gets the field IdList from XimNewsList which matching the value of Name.
	*  @param string name
	*  @return string|false
	*/

	function getListByName($name) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdList FROM XimNewsList WHERE Name = %s", $dbObj->sqlEscapeString($name));
		
	 	$dbObj->Query($query);
	 	
	 	return $dbObj->numRows > 0 ? $dbObj->GetValue('IdList') : false;
	}
	
	/**
	*  Gets all the rows from XimNewsList.
	*  @return array
	*/

	function getAllLists() {
		$dbObj = new DB();
		$query = "SELECT IdList,Name from XimNewsList";
		$dbObj->Query($query);
		
		$result = array();
		while (!$dbObj->EOF) {
			$result[$dbObj->GetValue('IdList')] = $dbObj->GetValue('Name');
			$dbObj->Next();
		}		 
		return $result;
	}
	
	/**
	*  Deletes the rows from RelColectorList which matching the value of IdColector and adds another one.
	*  @param int colectorID
	*  @param array data
	*  @return bool
	*/

	function updateList($colectorID,$data) {
		// Antes de insertar se borran los valores anteriores.
		// Sino no hay forma de eliminar un registro ya insertado.
		$dbObj = new DB();
		$dbObj->Execute("DELETE FROM RelColectorList WHERE IdColector=".$colectorID);
		foreach($data as $name){
			$listID = $this->getListByName($name);

			if(!($listID > 0)){
				$ximNewsList = new XimNewsList();
				$ximNewsList->set('Name',$name);
				$listID = $ximNewsList->add();
			}

			$relColectorList = new RelColectorList();
			$relColectorList->set('IdColector', $colectorID);
			$relColectorList->set('IdList', $listID);

			if(!$relColectorList->add()){			
	        	XMD_Log::info("list $listID");
				return false;
			}
	
		}
		return true;
	}
}
?>