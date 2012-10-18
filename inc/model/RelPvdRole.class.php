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

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/RelPvdRole_ORM.class.php';

class RelPvdRole extends RelPvdRole_ORM {

	function __construct($idRol = NULL) {

		parent::__construct($idRol);

		if (!$this->get('IdRel') > 0) {
			return NULL;
		}
	}

	function getListTemplates() {

		$db = new DB();
		
		$sql = "SELECT RelPvdRole.IdTemplate, Nodes.Name FROM RelPvdRole INNER JOIN Nodes
			ON RelPvdRole.IdTemplate = Nodes.IdNode WHERE RelPvdRole.IdRol = " . $this->get('IdRel');
		
		$db->query($sql);

		if (!$db->numRows > 0) {
			return NULL;
		}

		$ret_array = array();
		while (!$db->EOF) {
			$ret_array[$i++] = $db->getValue('IdTemplate');
			$db->next();
		}

		return $ret_array;
	}

	function getList() {
	// Old code for refactor, not using in all the app.
	/*
		$sql = "SELECT RelPvdRole.*, Nodes.Name FROM RelPvdRole LEFT OUTER JOIN Nodes
			ON RelPvdRole.IdTemplate = Nodes.IdNode WHERE RelPvdRole.IdRol = $this->idrol";
		$this->dbObj->Query($sql);
		if(!$this->dbObj->numErr){
			$i=0;
			array($salida);
			while(!$this->dbObj->EOF){
				$salida[$i]['IdRel'] = $this->dbObj->GetValue("IdRel");
				$salida[$i]['IdTemplate'] = $this->dbObj->GetValue("IdTemplate");
				$salida[$i]['IdRol']   = $this->dbObj->GetValue("IdRol");
				$salida[$i]['Name']  = $this->dbObj->GetValue("Name");
				$i++;
				$this->dbObj->Next();
			}
			return $salida;
		} else {
	                $this->SetError(4);
		}
	*/
	}

	function createRel($idTemplate, $idRol) {
	// Old code for refactor, not using in all the app.
        /*
		if (!$this -> IsRelExists($IdRol,$IdTemplate)) {
			$sql = "INSERT INTO RelPvdRole (IdRel, IdTemplate, IdRol) VALUES (null,".DB::sqlEscapeString($IdTemplate).", ".DB::sqlEscapeString($IdRol).")";
			$this->dbObj->Execute($sql);
			if ($this->dbObj->numErr) {
				$this->SetError(4);
				return;
			}
		}
        */
	}

	function deleteRel($idRel) {

		$db = new DB();
		$sql = "DELETE FROM RelPvdRole Where IdRel = $idRel";

		$db->Execute($sql);
	}

	function deleteRelPvds($arrTemplates) {
        // Old code for refactor, not using in all the app.
        /*
		for($i=0;$i<count($arr_templates);$i++) {
			$IdTemplate = $arr_templates[$i];
			$sql = "DELETE FROM RelPvdRole Where IdTemplate = $IdTemplate";
			$this->dbObj->Execute($sql);
			if ($this->dbObj->numErr) {
				$this->SetError(4);
				return;
			}
		}
	*/
	}

	function isRelExists($idRol, $idTemplate) {
        // Old code for refactor, not using in all the app.
        /*
		$sql="SELECT * from RelPvdRole WHERE IdRol = $idRol and IdTemplate = $idTemplate ";
		$this->dbObj->Query($sql);

                if ($this->dbObj->numRows==0) {
                        return 0;
                }
                else {
                        return 1;
                }
	*/
	}

}
?>
