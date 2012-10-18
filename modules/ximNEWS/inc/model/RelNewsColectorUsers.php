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




ModulesManager::file('/inc/model/orm/RelNewsColectorUsers_ORM.class.php', 'ximNEWS');


class RelNewsColectorUsers extends RelNewsColectorUsers_ORM  
{
	/**
	*  Adds a row to RelNewsColectorUsers table.
	*  @param int idRelNewsColector
	*  @param int idUser
	*  @return int|null
	*/

	function add($idRelNewsColector = null, $idUser = null) {
		if (!ModulesManager::isEnabled('ximPUBLISHtools')) {
			XMD_Log::error(_("News-colector-user relation not added. Module ximPUBLISHtools is disabled."));
			return null;
		}
		
		if(is_null($idRelNewsColector)) {
			XMD_Log::error(_("Cannot add News-colector-user relation. Param idRelNewsColector is null."));
			return null;
		}
		
		if(is_null($idUser)) {
			XMD_Log::error(_("Cannot add News-colector-user relation. Param idUser is null."));
			return null;
		}
		
		$this->set('IdRelNewsColector', $idRelNewsColector);
		$this->set('IdUser', $idUser);
		$this->set('Time', mktime());

		$id = parent::add();
		
		XMD_Log::info(sprintf(_("New rel-news-colector-user added (Id: %s - IdRelNew: %s - IdUser: %s )"), $id, $idRelNewsColector, $idUser));
		
		return $id ? $id : null;
	}
	
	/**
	*  Gets the rows from RelNewsColectorUsers which matching the value of IdUser.
	*  @param int idUser
	*  @return array|null
	*/

	function getRelsByUser($idUser = null) {
		
		if(is_null($idUser)) {
			XMD_Log::error(_("Cannot get News-colector-user relations. Param idUser is null."));
			return null;
		}
		
		$rels = $this->find('Id', 'IdUser = %s', array($idUser), MULTI);

		if (!(count($rels) > 0)) {
			XMD_Log::info(sprintf(_("User %s has not News-colector-user relations."), $idUser));
		}

		return $rels;
	}
	
	/**
	*  Gets the rows from RelNewsColectorUsers which matching the value of IdRelNewsColector.
	*  @param int idRelNewsColector
	*  @return array|null
	*/

	function getRelsByNewsColector($idRelNewsColector = null) {
		
		if(is_null($idRelNewsColector)) {
			XMD_Log::error(_("Cannot get news-colector-user relations. Param idRelNewsColector is null."));
			return NULL;
		}
		
		$rels = $this->find('IdRelNewsColector, IdUser, Time', 'IdRelNewsColector = %s ORDER BY Time DESC limit 1', array($idRelNewsColector), MULTI);

		if (!(count($rels) > 0)) {
			XMD_Log::info(sprintf(_("RelNewsColector %s has not news-colector-user relations."), $idRelNewsColector));
			return NULL;
		}

		return $rels[0];
	}
	
	/**
	*  Gets the rows from RelNewsColector join RelNewsColectorUsers which matching the value of IdNew and State Pending.
	*  @param int idNew
	*  @return array|null
	*/

	function getPendingRelationsByNew($idNew) {
		
		//TODO: What to do with 'removed' and 'publishable' states?
		//		They're post-automatic states but... It's necessary to notify user about them?
		
		if(is_null($idNew) || !($idNew > 0)) {
			XMD_Log::error(_('Cannot get Pending relations. IdNew is null or not positive integer.'));
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
			XMD_Log::info(_('No pending relations found for news ') . $idNew);
			return NULL;
		}

		while (!$dbObj->EOF) {
			$result[] = array( 'IdUser' => $dbObj->GetValue('IdUser'), 
					'Time' => $dbObj->GetValue('Time'),
					'State' => $dbObj->GetValue('State'),
					'FechaOut' => $dbObj->GetValue('FechaOut'),
					'IdColector' => $dbObj->GetValue('IdColector'));
			$dbObj->Next();
		}
		return $result;
	}

	/**
	*  Gets the rows from RelNewsColector join RelNewsColectorUsers which matching the values of IdNew and IdColector and State Pending.
	*  @param int idNew
	*  @param int idColector
	*  @return array|null
	*/

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
		$query = "SELECT U.IdUser, U.Time, R.State, R.FechaIn, R.FechaOut, R.IdNew, R.IdColector FROM RelNewsColector R " .
				 "INNER JOIN RelNewsColectorUsers U " .
				 "ON R.IdRel = U.IdRelNewsColector WHERE " .
				 "(State = 'pending' OR FechaOut IS NOT NULL) " .
				 $extraWhere .
				 " GROUP BY U.IdRelNewsColector " .
				 " ORDER BY U.Time DESC";

		$dbObj->Query($query);
		$result = array();
		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info(_('No pending relations found'));
			return NULL;
		}

		while (!$dbObj->EOF) {
			$nodeNew = new Node($dbObj->GetValue('IdNew'));
			$nodeUser = new User($dbObj->GetValue('IdUser'));
			$newName = $nodeNew->get('Name');
			$userName = $nodeUser->get('Name');
			$result[$dbObj->GetValue('IdNew')] = array( 'IdUser' => $dbObj->GetValue('IdUser'), 
								'Time' => $dbObj->GetValue('Time'),
								'State' => $dbObj->GetValue('State'),
								'FechaOut' => $dbObj->GetValue('FechaOut'),
								'FechaIn' => $dbObj->GetValue('FechaIn'),
								'IdNew' => $dbObj->GetValue('IdNew'),
								'IdColector' => $dbObj->GetValue('IdColector'),
								'UserName' => $userName,
								'NewName' => $newName);
			$dbObj->Next();
		}
		return $result;
	}
}
?>
