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



ModulesManager::file('/inc/model/orm/Pumpers_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/ServerErrorByPumper.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/ServerErrorManager.class.php', 'ximSYNC');
ModulesManager::file('/conf/synchro.conf', 'ximSYNC');


/**
*	@brief Handles operations with Pumpers.
*
*	A Pumper is an instance of the dexPumper script, wich is responsible for sending the ServerFrames to Server (via ftp, ssh, etc).
*	This class includes the methods that interact with the Database.
*/

class Pumper extends Pumpers_ORM {

    var $syncStatObj;

	/**
	*  Sets the value of any variable.
	*  @param string key
	*  @param unknown value
	*/

    function setFlag($key, $value) {
        $this->$key = $value;
    }

	/**
	*  Gets the value of any variable.
	*  @param string key
	*  @return unknown
	*/

    function getFlag($key) {
		return $this->$key;
    }

	/**
	*  Adds a row to Pumpers table.
	*  @param int idServer
	*  @return int|null
	*/

    function create($idServer) {
		$this->set('IdServer',$idServer);
		$this->set('State','New');
		$this->set('StartTime', time());
		$this->set('CheckTime', time());
		$this->set('ProcessId','xxxx');

		parent::add();
		$pumperID = $this->get('PumperId');

		if ($pumperID > 0) {
			// Se crea el registro para anotar posibles errores en el servidor
			$serverError = new ServerErrorByPumper();
			$serverError->create($pumperID, $idServer);

			return $pumperID;
		}

		$this->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
			__LINE__, "ERROR", 8, "ERROR Inserting pumper");

		return null;
    }

	/**
	*  Gets the Pumpers whose state is different to Ended.
	*  @return array|null
	*/

    function getPumpersInRegistry() {
    	$dbObj = new DB();
		$pumpers = array();
		$sql = "SELECT PumperId FROM Pumpers WHERE State != 'Ended'";
		$dbObj->Query($sql);

		if($dbObj->numRows == 0){
			return null;
		}

		while(!$dbObj->EOF) {
			$pumpers[] = $dbObj->GetValue("PumperId");
			$dbObj->Next();
		}

		return $pumpers;
    }

	/**
	*  Calls to command for start a Pumper.
	*  @param int pumperId
	*  @param string modo
	*  @return bool
	*/

	function startPumper ($pumperId, $modo = "pl") {

		$separador = ($modo == "pl") ? " " : "=";

		$dbObj = new DB();

		$startCommand = (($modo == "pl") ? PUMPER_PATH : PUMPERPHP_PATH) . "/dexpumper." . $modo .
		" --pumperid" . $separador . "$pumperId --sleeptime" . $separador . "2 --maxvoidcycles" .
		$separador . "10 --localbasepath" . $separador . SERVERFRAMES_SYNC_PATH." > /dev/null 2>&1 &";

		$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
			__LINE__, "INFO", 8, "Pumper call: $startCommand");

		$out = array();
		system($startCommand, $var);

		$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
			__LINE__, "INFO", 8, $startCommand, true);
		// exec($startCommand,$out);
		/**sleep(1);
			echo "ESPERANDO ";
		while(count($out) == 0){
			echo "..........";
		}*/

		//0: O.k, 200: problema de conexion, 255:servidor no existe, 127:command not found
		if ($var == 0){
			$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "INFO", 8, "Pumper $pumperId started succefully", true);

			return true;
		} else if ($var == 200) {
			$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, "ERROR In server connection starting pumper $pumperId");

			$serverMng = new ServerErrorManager();
			$serverMng->disableServerByPumper($pumperId);

			return false;
		} else if ($var == 400) {
			$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, "ERROR registering pumper $pumperId.");

			return false;
		} else {
			$this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, "ERROR Code $var starting pumper $pumperId");
			// set serverframes State from Due2In/Out to Due2In/OutWithError ???
			// $sql = "UPDATE ServerFrames SET State = CONCAT(State,'WithError') WHERE State IN ('Due2In','Due2Out')";
			return false;
		}
	}

	/**
	*  Logs the activity of the Pumper.
	*  @param int batchId
	*  @param int nodeFrameId
	*  @param int channelFrameId
	*  @param int serverFrameId
	*  @param int pumperId
	*  @param string class
	*  @param string method
	*  @param string file
	*  @param int line
	*  @param string type
	*  @param int level
	*  @param string comment
	*  @param int doInsertSql
	*/

    function PumperToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
					 $class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false) {

    	if(!isset($this->syncStatObj)) {

    		$this->syncStatObj = new SynchronizerStat();
    	}

    	$this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
					 $class, $method, $file, $line, $type, $level, $comment, $doInsertSql);

    }
}
?>
