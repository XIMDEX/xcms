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



if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));

include_once(XIMDEX_ROOT_PATH."/modules/ximSYNC/inc/model/Pumper.class.php");
include_once(XIMDEX_ROOT_PATH."/modules/ximSYNC/conf/synchro.conf");

/**
*	@brief Handles the activity of Pumpers.
*
*	A Pumper is an instance of the dexPumper script, wich is responsible for sending the ServerFrames to Server (via ftp, ssh, etc).
*/

class PumperManager {

	/**
	*	Checks what Pumpers are running and decides whether start or stop them.
	* 	@param array pumpersWithTasks
	*	@param string modo
	*	@return bool
	*/

    function checkAllPumpers($pumpersWithTasks, $modo = "pl") {
		$pumper = new Pumper();

		if (is_null($pumpersWithTasks) || count($pumpersWithTasks) == 0) {
			$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, _("Not pumpers available") );

			return false;
		}

		$dbObj = new DB();

		$pumpersWithError = 0;

		foreach($pumpersWithTasks as $pumperId) {
			$pumper = new Pumper($pumperId);

			if (!($pumper->get('PumperId') > 0)) {
				$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
					__LINE__, "ERROR", 8, _("Non-existing pumper")." $pumperId");
				continue;
			}

			$pumperState = $pumper->get('State');
			$pumperCheckTime = $pumper->get('CheckTime');
			$pumperStartTime = $pumper->get('StartTime');

			$pumper->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "INFO", 8, sprintf(_("Pumper %s at state %s"), $pumperId, $pumperState) );

			switch($pumperState) {
				case 'Started':
					// Checking if pumper is alive
					$now = time();
					if ($now - $pumperCheckTime > MAX_CHECK_TIME_FOR_PUMPER) {
						$pumper->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
							__LINE__, "INFO", 8, _("No checking time for Pumper")." $pumperId", true);
						// Restart pumper
						$processId = $pumper->get('ProcessId');
						if(!empty($processId) ) {
							system("kill -9 $processId",$var);
						}

						$pumper->set('State','New');
						$pumper->update();
						$result = $pumper->startPumper($pumperId, $modo);

						if ($result == false) {
							$pumpersWithError++;
						}
					}
				break;

				case 'New':
					$result = $pumper->startPumper($pumperId, $modo);

					if ($result == false) {
						$pumpersWithError++;
					}
				break;

				case 'Ended':
					// Pumper ended without finish all tasks
					$pumper->set('State','New');
					$pumper->update();

					$result = $pumper->startPumper($pumperId, $modo);

					if ($result == false) {
						$pumpersWithError++;
					}
				break;

				default:
					$pumper->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
						__LINE__, "INFO", 8, "default: $pumperId - $pumperState");
				break;
			}
		}

		$pumpersInRegistry = $pumper->getPumpersInRegistry();

		if ($pumpersWithError == sizeof($pumpersInRegistry)) {
			$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "INFO", 8, _("Problems in all pumpers") );
			return false;
		}

		return true;
    }

	/**
	*  For each Pumper gets the number of ServerFrames needed to complete the chunk and makes them available.
	*  @param array activeAndEnabledServers
	*/

	function callingPumpers($activeAndEnabledServers) {

		$pumper = new Pumper();
		$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
			__LINE__, "INFO", 8, _("Calling pumpers") );

		$serverFrameManager = new ServerFrameManager();
		$pumpers = $serverFrameManager->getPumpersWithTasks($activeAndEnabledServers);

		if (!is_null($pumpers) && count($pumpers) > 0) {
			$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "INFO", 8, _("There are tasks for pumping") );
			$serverFrameManager->setTasksForPumping($pumpers,SCHEDULER_CHUNK,$activeAndEnabledServers);
			$result = $this->checkAllPumpers($pumpers, PUMPER_SCRIPT_MODE);

			if ($result == false) {
				$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
					__LINE__, "INFO", 8, _("All pumpers with errors"));
			}

		}

		$pumper->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
			__LINE__, "INFO", 8, _("No pumpers to be called") );

	}

}
?>
