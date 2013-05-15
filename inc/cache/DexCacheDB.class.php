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




require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');

class DexCacheDB {

	/**
	 * 
	 * @var unknown_type
	 */
	var $id;
	/**
	 * 
	 * @var unknown_type
	 */
	var $idNode;
	/**
	 * 
	 * @var unknown_type
	 */
	var $idSync;
	/**
	 * 
	 * @var unknown_type
	 */
	var $idVersion;
	/**
	 * 
	 * @var unknown_type
	 */
	var $metadata = array( );
	
	/**
	 * 
	 * @param $idNode
	 * @param $idSync
	 * @param $idVersion
	 * @return unknown_type
	 */
	function DexCacheDB($idNode = NULL, $idSync = NULL, $idVersion = NULL) {

		$this->idNode = $idNode;
		$this->idSync = $idSync;
		$this->idVersion = $idVersion;
	}

	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function read($key, $value) {

		$db = new DB();
		$sql = "SELECT id,idNode,idSync,idVersion FROM DexCache WHERE $key=$value";

		$db->Query($sql);

		$ret = array();
		$i = 0;
		while (!$db->EOF) {

			$ret['id'] = $db->GetValue("id");
			$ret['idNode'] = $db->GetValue("idNode");
			// multi
			$ret['idSync'][] = $db->GetValue("idSync");
			$ret['idVersion'] = $db->GetValue("idVersion");

			$i++;
			$db->Next();
		}

		return $ret;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function commit() {

		$db = new DB();
	
		if (!is_null($this->id)) {

			$sql = "SELECT id FROM DexCache WHERE id=" . $this->id;
			$db->Query($sql);

			if ($db->numRows > 0) {
				$action = "UPDATE";
			} else {
				$action = "INSERT";
			}

		} else {
			$action = "INSERT";
		}

		switch ($action) {

			case "INSERT":
					$sql = "INSERT INTO DexCache (idNode, idSync, idVersion) VALUES (" . 
					$this->idNode . ", " .
					$this->idSync . ", " .
					$this->idVersion . ")";

					break;

			case "UPDATE":
					$sql = "UPDATE DexCache SET 
					idNode=" . $this->idNode . ", 
					idSync='" . $this->idSync . "', 
					idVersion =" . $this->idVersion . " 
					WHERE id = " . $this->id;

					break;
		}

		echo "Executing $sql<br/>\n";
		$db->Execute($sql);

	}

	/**
	 * 
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	function delete($key, $value) {
		
		$db = new DB();
		$sql = "DELETE FROM DexCache WHERE $key=" . $value;

		return $db->Execute($sql);
	}


}

?>
