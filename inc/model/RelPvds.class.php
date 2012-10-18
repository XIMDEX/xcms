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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/RelPvds_ORM.class.php';

class RelPvds extends RelPvds_ORM {

	function createRel($idPvd_a, $idPvd_b) {

		$this->set('IdRel', NULL);
		$this->set('IdNodePVD1', $idPvd_a);
		$this->set('IdNodePVD2', $idPvd_b);

		if (parent::add()) {
			return $this->get('IdRel');
		} else {
			return NULL;
		}
	}

	function deleteReal($idRel) {

		$sql = "DELETE FROM RelPvds Where IdRel=$idRel";

		$db = new DB();
		$db->execute($sql);
	}

	// Refactor this!
	function getList($idPvd) {

		$db = new DB();

		$sql = "SELECT RelPvds.*, Nodes.Name FROM
			RelPvds LEFT OUTER JOIN Nodes
			ON RelPvds.IdNodePVD2=Nodes.IdNode
			WHERE RelPvds.IdNodePVD1=$idPvd";
		
		$db->query($sql);

		$salida1 = array();
		$i = 0;
		while (!$db->EOF) {
			$salida1[$i]['IdRel'] = $db->GetValue("IdRel");
			$salida1[$i]['IdNodePVD1'] = $db->GetValue("IdNodePVD1");
			$salida1[$i]['IdNodePVD2'] = $db->GetValue("IdNodePVD2");
			$salida1[$i]['Name'] = $db->GetValue("Name");

			$i++;
			$db->next();
		}


		$sql = "SELECT RelPvds.*, Nodes.Name FROM
			RelPvds LEFT OUTER JOIN Nodes
			ON RelPvds.IdNodePVD1=Nodes.IdNode
			WHERE RelPvds.IdNodePVD2=$idPvd";

		$db->query($sql);
		
		$salida2 = array();
		$i = 0;
		while (!$db->EOF) {
			$salida2[$i]['IdRel'] = $db->GetValue("IdRel");
			$salida2[$i]['IdNodePVD1'] = $db->GetValue("IdNodePVD1");
			$salida2[$i]['IdNodePVD2'] = $db->GetValue("IdNodePVD2");
			$salida2[$i]['Name'] = $db->GetValue("Name");

			$i++;
			$db->next();
		}


                $ret_array = array_merge($salida1,$salida2);

                return $ret_array;
	}

	function isCompatible($idPvd_a, $idPvd_b) {

		if ($idPvd_a == $idPvd_b) {
			return true;
		}

		$sql= "SELECT IdRel"
		. " FROM RelPvds"
		. " WHERE (IdNodePVD1=$idPvd_a AND IdNodePVD2=$idPvd_b)"
		. " OR (IdNodePVD1=$idPvd_b AND IdNodePVD2=$idPvd_a)";

		$db = new DB();
		$db->Execute($sql);

		return ($db->numRows > 0) ? true : false;
	}

}
?>
