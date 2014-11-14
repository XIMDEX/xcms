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



ModulesManager::file('/inc/model/orm/XimNewsColector_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelColectorList.php', 'ximNEWS');


class XimNewsColector extends XimNewsColector_ORM {

	/**
	*  Gets all rows from XimNewsColector.
	*  @return array
	*/

	function getAllColectors() {
		$dbObj = new DB();
		$query = sprintf("SELECT IdColector, Name FROM XimNewsColector");
		$dbObj->Query($query);

		$result = array();
		while (!$dbObj->EOF) {
			$result[$dbObj->GetValue('IdColector')] = $dbObj->GetValue('Name');
			$dbObj->Next();
		}
		return $result;
	}

	/**
	*  Gets the rows from XimNewsColector which matching the values of IdSection and Group.
	*  @param int idSection
	*  @param array groupList
	*  @return array|null
	*/

	function getColectors($idSection, $groupList) {

	    if(count($groupList) == 0){
			return NULL;
	    }

	    $nodesInGroup = array();
	    settype($groupList,'array');

	    foreach($groupList as $groupID){
			$group = new Group($groupID);
			$nodesInGroup = array_merge($nodesInGroup,$group->GetNodeList());
	    }

	    $nodesInGroup = array_unique($nodesInGroup);
	    $dbObj = new DB();

	    $query = sprintf("SELECT IdColector, Global, Name, IdSection FROM XimNewsColector WHERE IdSection = %s OR (Global = 1 AND IdSection != %s) ORDER BY Name ASC",$dbObj->sqlEscapeString($idSection),$dbObj->sqlEscapeString($idSection));
	    $dbObj->Query($query);

	    $result = array();
	    while (!$dbObj->EOF) {
		$id = $dbObj->GetValue('IdColector');
		$global = $dbObj->GetValue('Global');
		$name = $dbObj->GetValue('Name');
		$section = $dbObj->GetValue('IdSection');

		if(($global == 1 && (in_array($id,$nodesInGroup))) || $section == $idSection){
			$result[$id] = $name;

		}

		$dbObj->Next();
	    }

	    return $result;
	}

	/**
	*  Updates the field Locked to 0.
	*  @return bool
	*/

	function Unlock() {
		if (!($this->get('IdColector') > 0)) {
			return false;
		}
		$this->set('Locked', 0);
		return $this->update();
	}

	/**
	*  Updates the field Locked to 1.
	*  @return bool
	*/

	function Lock() {
		if (!($this->get('IdColector') > 0)) {
			return false;
		}
		$this->set('Locked', 1);
		return $this->update();
	}

	/**
	*  Adds a row to XimNewsColector table.
	*  @return int
	*/

	function add() {
		return $this->create($this->get('IdColector'), $this->get('Name'), $this->get('Filter'), $this->get('IdTemplate'),$this->get('IdSection'), $this->get('IdXimlet'),
		$this->get('OrderNewsInBulletins'), $this->get('NewsPerBulletin'), $this->get('TimeToGenerate'),
		$this->get('NewsToGenerate'), $this->get('MailChannel'), $this->get('XslFile'),$this->get('TemplateVersion'),$this->get('Inactive'),$this->get('IdArea'),$this->get('Global'));
	}

	/**
	*  Wrapper for add.
	*  @param int colectorID
	*  @param string name
	*  @param string filter
	*  @param int templateID
	*  @param int sectionID
	*  @param int ximletID
	*  @param string order
	*  @param int newsPerBulletin
	*  @param int timetogenerate
	*  @param int newstogenerate
	*  @param int canalCorreo
	*  @param int xslFile
	*  @param int pvdVersion
	*  @param int inactive
	*  @param int idArea
	*  @param int global
	*/

	function create($colectorID,$name,$filter,$templateID,$sectionID,$ximletID,$order,$newsPerBulletin,$timetogenerate,$newstogenerate,$canalCorreo,$xslFile,$pvdVersion,$inactive,$idArea,$global)
	{
		$timetogenerate=$timetogenerate*3600;

		$this->set('IdColector', $colectorID);
		$this->set('Name', $name);
		$this->set('Filter', $filter);
		$this->set('IdTemplate', $templateID);
		$this->set('IdSection', $sectionID);
		$this->set('IdXimlet', $ximletID);
		$this->set('OrderNewsInBulletins', $order);
		$this->set('NewsPerBulletin', $newsPerBulletin);
		$this->set('TimeToGenerate', $timetogenerate);
		$this->set('NewsToGenerate', $newstogenerate);
		$this->set('LastGeneration', 0);
		$this->set('MailChannel', $canalCorreo);
		$this->set('XslFile', $xslFile);
		$this->set('TemplateVersion', $pvdVersion);
		$this->set('Inactive', $inactive);
		$this->set('IdArea', $idArea);
		$this->set('Global', $global);

		$id = parent::add();
		if ($id) {
	        return $id;
		}
		return false;
	}

	/**
	*  Gets the xslFile from XimNewsColector which matching the value of IdTemplate.
	*  @param int idPvd
	*  @return string|null
	*/

	function GetXslFile($idPvd){
		$dbObj = new DB();
		$query = sprintf("SELECT XslFile"
			. " FROM XimNewsColector"
			. " WHERE IdTemplate = %s AND XslFile IS NOT NULL", $dbObj->sqlEscapeString($idPvd));

		$dbObj->Query($query);

		if ($dbObj->numRows > 0) {
			return $dbObj->GetValue('XslFile');
		}

		return false;
	}

	/**
	*  Updates the rows from XimNewsColector which matching the values of IdTemplate, XslFile and TemplateVersion.
	*  @param int idTemplate
	*  @param string xslFile
	*  @param int templateVersion
	*  @return string|null
	*/

	function updateByIdTemplate($idTemplate, $xslFile, $templateVersion) {
		$dbObj = new DB();
		$query = sprintf("UPDATE XimNewsColector"
			. " SET XslFile = %s, TemplateVersion = %s"
			. " WHERE IdTemplate = %s",
			$dbObj->sqlEscapeString($xslFile),
			$dbObj->sqlEscapeString($templateVersion),
			$dbObj->sqlEscapeString($idTemplate));

		$dbObj->Execute($query);
		return ($dbObj->numRows > 0) ? true : false;
	}

	/**
	*  Gets the Name field from XimNewsColector which matching the value of IdArea.
	*  @param int idArea
	*  @return array|null
	*/

	function getColectorsByArea($idArea) {
		$params = array( 'IdArea' => $idArea );
		$condition = "IdArea = %s";

		$result = parent::find('Name', $condition, $params, MULTI);

		if (is_null($result)) {
			return null;
		}

		return $result;
	}

	/**
	*  Gets the IdColector field from XimNewsColector whose value of field Locked is 1.
	*  @return array|null
	*/

	function getLockedColectors() {
		$params = array( 'Locked' => 1 );
		$condition = "Locked = %s";

		$result = parent::find('IdColector', $condition, $params, MULTI);

		if (is_null($result)) {
			return null;
		}

		return $result;
	}
}
?>