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




ModulesManager::file('/inc/model/orm/RelColectorUsersBatchs_ORM.class.php', 'ximNEWS');


class RelColectorUsersBatchs extends RelColectorUsersBatchs_ORM  
{

	/**
	*  Adds a row to RelColectorUsersBatchs table.
	*  @param int idColectorUsers
	*  @param int idBatch
	*  @return int|null
	*/

	function add($idColectorUsers = null, $idBatch = null) {
		
		if (!ModulesManager::isEnabled('ximPUBLISHtools')) {
			XMD_Log::error(_("Colector-user-batch relation not added. XimPUBLISHtools module is disabled."));
			return null;
		}
		
		if(is_null($idColectorUsers)) {
			XMD_Log::error(_("Colector-user-batch relation could NOT be added. IdColectorUsers param is null."));
			return null;
		}
		
		if(is_null($idBatch)) {
			XMD_Log::error(_("Colector-user-batch relation could NOT be added. IdBatch param is null."));
			return null;
		}
		
		$this->set('IdColectorUser', $idColectorUsers);
		$this->set('IdBatch', $idBatch);

		$id = parent::add();
		
		XMD_Log::info(sprintf(_("New relation colector-user-batch added (Id: %s - IdColectorUser: %s - IdBatch: %s )"), $id, $idColectorUsers, $idBatch));
		
		return $id ? $id : null;
	}
	
	/**
	*  Gets the rows from RelColectorUsersBatchs which matching the value of IdColectorUsers.
	*  @param int idColectorUsers
	*  @return array|null
	*/

	function getPublicationProgress($idColectorUsers = null) {
		if(is_null($idColectorUsers)) {
			XMD_Log::error(_("Cannot get colector-user-batch publication progress. IdColectorUsers param is null."));
			return null;
		}

		$dbObj = new DB();
		$query = "SELECT b.State, count(*) AS total_batchs, SUM(b.ServerFramesTotal) AS total_serverframes " .
				"FROM Batchs b, RelColectorUsersBatchs r " .
				"WHERE b.IdBatch = r.IdBatch AND r.IdColectoruser = " . $idColectorUsers . 
				" GROUP BY b.State";

		$result = array(
			'ended' => 0,
			'pending' => 0
		);

		$dbObj->Query($query);
		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info(_('No colector-user-batchs relations found for colector-user id: ') . $idColectorUsers);
			return NULL;
		}

		while (!$dbObj->EOF) {
			if($dbObj->GetValue('State') == 'Ended')
				$result['ended'] = $dbObj->GetValue('total_serverframes');
			else
				$result['pending'] = $dbObj->GetValue('total_serverframes');
			$dbObj->Next();
		}
		
		XMD_Log::info(sprintf(_('Colector-user-batch progress: TOTAL: %d - ENDED: %d - PENDING: %d'), ($result['ended'] + $result['pending']), $result['ended'], $result['pending']));
		
		return $result;
	}
}
?>