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



ModulesManager::file('/inc/model/orm/RelColectorList_ORM.class.php', 'ximNEWS');


class RelColectorList extends RelColectorList_ORM   {
	
	/**
	*  Deletes the rows from RelColectorList which matching the value of IdColector.
	*  @param int idColector
	*  @return unknown
	*/

	function deleteByColector($idColector) {
	    $dbObj = new DB();
	    $query = sprintf("DELETE FROM RelColectorList WHERE IdColector = %s",
	    		$dbObj->sqlEscapeString($idColector));
		$dbObj->Execute($query);
	}

	/**
	*  Gets the rows from RelColectorList which matching the value of IdColector.
	*  @param int idColector
	*  @return array
	*/

	function getByColector($idColector) {
		return $this->find('IdRel', 'IdColector = %s', array($idColector), MONO);
	}
}
?>