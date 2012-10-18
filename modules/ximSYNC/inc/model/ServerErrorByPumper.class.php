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



ModulesManager::file('/inc/model/orm/ServerErrorByPumper_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/Server.class.php');

/**
*	@brief Manages the errors found during the sending of ServerFrames to Server.
*
*	This class includes the methods that interact with the Database.
*/

class ServerErrorByPumper extends ServerErrorByPumper_ORM {

	/**
	*  Gets the field ErrorId from ServerErrorByPumper table which matching the value of pumperId.
	*  @param int pumperId
	*  @return unknown
	*/

    function loadByPumper($pumperId = null){
		if($pumperId){
			$dbObj = new DB();
			$sql = "SELECT ErrorId FROM ServerErrorByPumper WHERE PumperId = $pumperId";
			$dbObj->Query($sql);

			if($dbObj->numRows == 0){
				XMD_Log::info(sprintf(_("Pumper %s does not exist"), $pumperId));
				die();
			}

			$errorId = $dbObj->GetValue(ErrorId);
		 }
		parent::GenericData($errorId);
    }

	/**
	*  Adds a row to ServerErrorByPumper table.
	*  @param int pumperId
	*  @param int serverId
	*  @return int|null
	*/

    function create($pumperId,$serverId) {
		$this->set('PumperId',$pumperId);
		$this->set('ServerId',$serverId);
		$this->set('WithError',0);
		$this->set('UnactivityCycles',0);

		parent::add();
		$errorId = $this->get('ErrorId');

		if ($errorId > 0) {
			return $errorId;
		}

		XMD_Log::info(_("Creating serverError"));
		return NULL;
    }

	/**
	*	Increases the inactivity cycles and updates ServerErrorByPumper table.
	*/

    function sumUnactivityCycles() {
		$counter = $this->get('UnactivityCycles');
		$idServer = $this->get('ServerId');

		XMD_Log::info(sprintf(_("Server %d has %d cycles inactive"), $idServer, $counter));

		$this->set('UnactivityCycles',$counter + 1);
		$this->update();
    }

}
?>
