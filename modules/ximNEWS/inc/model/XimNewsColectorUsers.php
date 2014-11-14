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




ModulesManager::file('/inc/model/orm/XimNewsColectorUsers_ORM.class.php', 'ximNEWS');

class XimNewsColectorUsers extends XimNewsColectorUsers_ORM  
{

	/**
	*  Adds a row to XimNewsColectorUsers table.
	*  @param int idColector
	*  @param int idUser
	*  @param string state
	*  @return int|null
	*/

	function add($idColector = null, $idUser = 0, $state = 'Generating') {
		
		if (!ModulesManager::isEnabled('ximPUBLISHtools')) {
			XMD_Log::error(_("Colector-user relation not added. XimPUBLISHtools module is disabled."));
			return null;
		}
		
		if(is_null($idColector)) {
			XMD_Log::error(_("Cannot add colector-user relation. IdColector param is null."));
			return null;
		}
		
		$this->set('IdColector', $idColector);
		$this->set('IdUser', $idUser);
		$this->set('StartGenerationTime', mktime());
		$this->set('Progress', 0);
		$this->set('State', $state);

		$id = parent::add();
		
		XMD_Log::info(_("New relation colector-user added (Id: " . $id . " - IdColector: " . 
						$idColector . " - IdUser: " . $idUser . ")"));
		
		return $id ? $id : null;
	}
	
	/**
	*  Gets the IdColector field from XimNewsColectorUsers which matching the value of IdUser.
	*  @param int idUser
	*  @return array|null
	*/

	function getRelsByUser($idUser = null) {
		
		if(is_null($idUser)) {
			XMD_Log::error(_("Cannot get colector-user relations. IdUser param is null."));
			return null;
		}
		
		$rels = $this->find('Id', 'IdUser = %s', array($idUser), MULTI);

		if (!(count($rels) > 0)) {
			XMD_Log::info("User " . $idUser . " has not colector-user relations.");
		}

		return $rels;
	}
		
	/**
	*  Gets the IdColector field from XimNewsColectorUsers which matching the value of IdColector.
	*  @param int idColector
	*  @return array|null
	*/

	function getRelsByColector($idColector = null) {
		
		if(is_null($idColector)) {
			XMD_Log::error(_("Cannot get colector-user relations. IdColector param is null."));
			return NULL;
		}
		
		$rels = $this->find('Id', 'IdColector = %s ORDER BY Time DESC limit 1', array($idColector), MULTI);

		if (!(count($rels) > 0)) {
			XMD_Log::info(_("Colector " . $idColector . " has not colector-user relations."));
			return NULL;
		}

		return $rels;
	}
	
	/**
	*  Gets the data from XimNewsColectorUsers which matching the value of IdColector.
	*  @param int idColector
	*  @return array|null
	*/

	function getColectorGenerationsData($idColector = null) {
		
		if(is_null($idColector)) {
			XMD_Log::error(_("Cannot get colector generation data. IdColector param is null."));
			return NULL;
		}
		
		$rels = $this->find('Id, Idcolector, IdUser, StartGenerationTime, EndGenerationTime, EndPublicationTime, Progress, State', 'IdColector = %s ORDER BY StartGenerationTime DESC', array($idColector), MULTI);

		if (!(count($rels) > 0)) {
			XMD_Log::info(_("Colector " . $idColector . " has not generation data."));
			return NULL;
		}

		return $rels;
	}
	
	/*function getPendingRelationsByNew($idNew) {
		
		//TODO: What to do with 'removed' and 'publishable' states?
		//		They're post-automatic states but... It's necessary to notify user about them?
		
		if(is_null($idNew) || !($idNew > 0)) {
			XMD_Log::error('Cannot get Pending relations. IdNew is null or not positive integer.');
			return NULL;
		}
		
		$dbObj = new DB();
		$query = "SELECT U.IdUser, U.Time, R.State, R.FechaOut, R.IdColector from RelNewsColector R " .
				 "INNER JOIN RelNewsColectorUsers U " .
				 "ON R.IdRel = U.IdRelNewsColector WHERE R.IdNew = $idNew AND " .
				 "(State = 'pending' OR FechaOut IS NOT NULL) GROUP BY U.IdRelNewsColector " .
				 "ORDER BY Time DESC";

		$dbObj->Query($query);
		$result = array();
		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info('No pending relations found for new ' . $idNew);
			return NULL;
		}

		while (!$dbObj->EOF) {
			$result[] = array(	'IdUser' => $dbObj->GetValue('IdUser'), 
								'Time' => $dbObj->GetValue('Time'),
								'State' => $dbObj->GetValue('State'),
								'FechaOut' => $dbObj->GetValue('FechaOut'),
								'IdColector' => $dbObj->GetValue('IdColector'));
			$dbObj->Next();
		}
		return $result;
	}
	
	function getPendingRelations($idNew = null, $idColector = null) {
		
		//TODO: What to do with 'removed' and 'publishable' states?
		//		They're post-automatic states but... It's necessary to notify user about them?
		
		$extraWhere = '';
		if(!is_null($idNew) && $idNew > 0) {
			$extraWhere .= ' AND R.IdNew = ' . $idNew;
		}
		if(!is_null($idColector) && $idColector > 0) {
			$extraWhere .= ' AND R.IdColector = ' . $idColector;
		}
		
		$dbObj = new DB();
		$query = "SELECT U.IdUser, U.Time, R.State, R.FechaOut, R.IdNew, R.IdColector FROM RelNewsColector R " .
				 "INNER JOIN RelNewsColectorUsers U " .
				 "ON R.IdRel = U.IdRelNewsColector WHERE " .
				 "(State = 'pending' OR FechaOut IS NOT NULL) GROUP BY U.IdRelNewsColector " .
				 $extraWhere .
				 " ORDER BY Time DESC";

		$dbObj->Query($query);
		$result = array();
		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info('No pending relations found');
			return NULL;
		}

		while (!$dbObj->EOF) {
			$result[] = array(	'IdUser' => $dbObj->GetValue('IdUser'), 
								'Time' => $dbObj->GetValue('Time'),
								'State' => $dbObj->GetValue('State'),
								'FechaOut' => $dbObj->GetValue('FechaOut'),
								'IdNew' => $dbObj->GetValue('IdNew'),
								'IdColector' => $dbObj->GetValue('IdColector'));
			$dbObj->Next();
		}
		return $result;
	}*/
}
?>