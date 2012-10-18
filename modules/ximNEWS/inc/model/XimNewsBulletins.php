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




ModulesManager::file('/inc/model/orm/XimNewsBulletins_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/manager/BatchManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/Batch.class.php', 'ximSYNC');

class XimNewsBulletin extends XimNewsBulletins_ORM {

	/**
	*  Adds a row to XimNewsBulletin table.
	*  @param int bulletinID
	*  @param int containerID
	*  @param int colectorID
	*  @param int loteID
	*  @param string fecha
	*  @param string set
	*  @return bool
	*/

	function InsertBulletinLanguage($bulletinID,$containerID,$colectorID,$loteID=0,$fecha,$set) {
		$this->set('IdBulletin', $bulletinID);
		$this->set('IdContainer', $containerID);
		$this->set('IdColector', $colectorID);
		$this->set('IdLote', $loteID);
		$this->set('Fecha', $fecha);
		$this->set('SetAsoc', $set);

		if(parent::add()){
			return true;
		}

		XMD_Log::info("InsertBulletinLanguage");
		return false;
	}

	/**
	*  Gets the rows from XimNewsBulletins which matching the value of IdColector.
	*  @param int idColector
	*  @return array
	*/

	function getAllByColector($idColector) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdBulletin FROM XimNewsBulletins WHERE IdColector = %s",
					$dbObj->sqlEscapeString($idColector));
		$dbObj->Query($query);
		$result = array();
		while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('IdBulletin');
			$dbObj->Next();
		}
		return $result;
	}

	/**
	*  Gets the rows from XimNewsBulletins which matching the value of IdContainer.
	*  @param int containerID
	*  @return array
	*/

	function getBulletinsByContainer($containerID) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdBulletin FROM XimNewsBulletins WHERE IdContainer = %s",
					$dbObj->sqlEscapeString($containerID));
		$dbObj->Query($query);

		$result = array();
		while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('IdBulletin');
			$dbObj->Next();
		}

		return $result;
	}

	/**
	*  Gets the field IdLote from XimNewsBulletins which matching the value of IdContainer.
	*  @param int containerID
	*  @return int|false
	*/

	function getLote($containerID) {
		if (is_null($containerID)) {
			return false;
		}

		$dbObj = new DB();
		$dbObj->Query(sprintf("SELECT IdLote FROM XimNewsBulletins WHERE IdContainer= %s",
				$dbObj->sqlEscapeString($containerID)));
		$idLote = $dbObj->GetValue('IdLote');

		if($idLote > 0){
			return $loteID;
		}

		return false;
	}

	/**
	*  Deletes the rows from XimNewsFrameBulletin which matching the value of idBulletin.
	*  @param idBulletin
	*  @return int
	*/

	function deleteFramesBulletin($idBulletin){
		$dbObj = new DB();
		$query = sprintf("DELETE FROM XimNewsFrameBulletin WHERE BulletinID = %s", $dbObj->sqlEscapeString($idBulletin));
	    $dbObj->Execute($query);
	    return ($dbObj->numRows > 0);
	}

	/**
	*  Gets the rows from RelNewsBulletins join XimNewsBulletins which matching the values of IdColector and IdNew.
	*  @param int idColector
	*  @param int idNew
	*  @return int|false
	*/

	function getBulletinWithNew($idColector,$idNew) {
		$dbObj = new DB();
		$query = sprintf("SELECT RelNewsBulletins.IdBulletin AS IdBulletin"
					. " FROM RelNewsBulletins"
					. " INNER JOIN XimNewsBulletins ON RelNewsBulletins.IdBulletin = XimNewsBulletins.IdBulletin"
					. " WHERE RelNewsBulletins.IdNew = %s AND XimNewsBulletins.IdColector = %s",
					$dbObj->sqlEscapeString($idNew),
					$dbObj->sqlEscapeString($idColector));

	    $dbObj->Query($query);

	    return ($dbObj->numRows > 0) ? $dbObj->GetValue('IdBulletin') : false;
	}

	/**
	*  Gets all distinct IdContainer values from XimNewsBulletins which matching the values of IdColector.
	*  @param int idColector
	*  @return array
	*/

	function getBulletinContainers($idColector) {

		$dbObj = new DB();
		$query = sprintf("SELECT DISTINCT(IdContainer) AS Container"
					. " FROM XimNewsBulletins WHERE IdColector = %s"
					. " ORDER BY IdContainer ASC",
					$dbObj->sqlEscapeString($idColector));

		$dbObj->Query($query);

		$result = array();
		while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('Container');
			$dbObj->Next();
		}

		return $result;
	}

	/**
	*  Gets all distinct IdContainer values from XimNewsBulletins which matching the values of IdColector.
	*  @param int idColector
	*  @return array
	*/

	function getContainersBySet($idColector,$set) {
		$dbObj = new DB();
		$query = sprintf("SELECT DISTINCT(XimNewsBulletins.IdContainer) As Id"
				. " FROM XimNewsBulletins"
				. " INNER JOIN RelNewsColector ON RelNewsColector.SetAsoc = XimNewsBulletins.SetAsoc"
				. " WHERE XimNewsBulletins.IdColector= %s"
				. " AND XimNewsBulletins.IdColector = RelNewsColector.IdColector"
				. " AND RelNewsColector.SetAsoc = %s ORDER BY Id ASC",
				$dbObj->sqlEscapeString($idColector),
				$dbObj->sqlEscapeString($set));

		$dbObj->Query($query);
		$resultado = array();
		while (!$dbObj->EOF) {
			$resultado[] = $dbObj->GetValue('Id');
			$dbObj->Next();
		}
		return $resultado;
	}

	/**
	*  Gets all IdBulletin from XimNewsBulletins, RelNewsColector which matching the values of IdColector, set and lang.
	*  @param int colectorID
	*  @param int set
	*  @param int langID
	*  @return array
	*/

	function getBulletinsByLang($colectorID,$set,$langID) {
		$dbObj = new DB();
		$query = sprintf("SELECT DISTINCT(XimNewsBulletins.IdBulletin) As Id"
				. " FROM XimNewsBulletins"
				. " INNER JOIN RelNewsColector ON XimNewsBulletins.IdColector=RelNewsColector.IdColector"
				. " AND RelNewsColector.SetAsoc=XimNewsBulletins.SetAsoc"
				. " WHERE XimNewsBulletins.IdColector = %s"
				. " AND RelNewsColector.SetAsoc = %s"
				. " AND RelNewsColector.LangId = %s"
				. " ORDER BY Id ASC",
				$dbObj->sqlEscapeString($colectorID),
				$dbObj->sqlEscapeString($set),
				$dbObj->sqlEscapeString($langID));

		$dbObj->Query($query);
		$resultado = array();

		while (!$dbObj->EOF) {

			$strDoc = new StructuredDocument($dbObj->GetValue('Id'));

			if ($strDoc->get('IdLanguage') == $langID) 	$resultado[] = $dbObj->GetValue('Id');

			$dbObj->Next();
		}

		return $resultado;
	}

	/**
	*  Gets all IdBulletin from XimNewsBulletins that can be published.
	*  @param int idColector
	*  @return array|null
	*/

	function getPublishableBulletins($idColector) {
		$params = array('IdColector' => $idColector);
		$condition = "Idcolector = %s AND State = 'generated'";

		$result = parent::find('IdBulletin', $condition, $params, MONO);

		if (is_null($result)) {
			return null;
		}

		return $result;
	}

	/**
	*  Gets the ximlet associated to the XimNewsBulletin.
	*  @return int|null
	*/

	function getBulletinXimlet() {

		$doc = new StructuredDocument($this->get('IdBulletin'));
		$bulletinLangID = $doc->get('IdLanguage');

		$ximnewsColector = new XimNewsColector($this->get('IdColector'));

		$ximletID = $ximnewsColector->get('IdXimlet');
		$node = new Node($ximletID);
		$childs = $node->GetChildren();
		foreach($childs as $idChild){
			$doc = new StructuredDocument($idChild);
			$langID = $doc->get('IdLanguage');
			if ($langID ==  $bulletinLangID) return $idChild;
		}

		return NULL;
	}

}
?>
