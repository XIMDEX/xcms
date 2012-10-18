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



class NodeDependencies {

	var $dbObj;

	function __construct() {
		$this->dbObj = new DB();
	}

	/**
	 * Inserts a row
	 * @param const $rel
	 * @param int $idSource
	 * @param int $idTarget
	 * @param int $idChannel
	 * @return true / false
	 */
	function set($idSource, $idTarget, $idChannel) {

		return $this->dbObj->Execute("INSERT INTO NodeDependencies VALUES ('$idSource', '$idTarget', '$idChannel')");

	}


	/**
	 * From a given target node returns its source nodes
	 * @param const $rel
	 * @param int $idTarget
	 * @return array
	 */
	function getByTarget($idTarget) {

		$this->dbObj->Query("SELECT DISTINCT IdNode FROM NodeDependencies WHERE IdResource = $idTarget");

		$deps = array();

		while(!$this->dbObj->EOF) {
			$deps[] = $this->dbObj->GetValue("IdNode");
			$this->dbObj->Next();
		}

		return $deps;
	}

	/**
	 * Deletes all relations for a target node
	 * @param const $rel
	 * @param int $idTarget
	 * @return true / false
	 */
	function deleteByTarget($idTarget) {

		return $this->dbObj->Execute("DELETE FROM NodeDependencies WHERE IdResource = $idTarget");
	}

	/**
	 * Deletes all relations for a source node
	 * @param const $rel
	 * @param int $idSource
	 * @return true / false
	 */
	function deleteBySource($idSource) {

		return $this->dbObj->Execute("DELETE FROM NodeDependencies WHERE IdNode = $idSource");
	}

}
?>
