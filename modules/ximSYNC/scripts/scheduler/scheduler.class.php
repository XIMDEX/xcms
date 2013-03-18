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



 ModulesManager::file('/inc/utils.inc');
 ModulesManager::file('/inc/manager/NodeFrameManager.class.php', 'ximSYNC');
 ModulesManager::file('/inc/manager/ServerFrameManager.class.php', 'ximSYNC');
 ModulesManager::file('/inc/manager/PumperManager.class.php', 'ximSYNC');
 ModulesManager::file('/inc/manager/BatchManager.class.php', 'ximSYNC');
 ModulesManager::file('/inc/manager/ServerErrorManager.class.php', 'ximSYNC');
 ModulesManager::file('/inc/model/Batch.class.php', 'ximSYNC');
 ModulesManager::file('/inc/model/SynchronizerStat.class.php', 'ximSYNC');
 ModulesManager::file('/conf/synchro.conf', 'ximSYNC');
 ModulesManager::file('/inc/sync/Mutex.class.php');
 ModulesManager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');

if (! ModulesManager::isEnabled ( 'XIMSYNC' )) {
	die ( _("ximSYNC module is not active, you must run syncronizer module")."\n" );
}

$synchro_pid = null;

class Scheduler {

	public static function start($global_execution = true) {

		global $synchro_pid;
		$synchro_pid = posix_getpid ();

		$startStamp = 0;

		$testTime = NULL;

		if (isset ( $argv [1] )) {
			$testTime = $argv [1];
		}

		$syncStatObj = new SynchronizerStat ( );
		$pumperManager = new PumperManager ( );
		$nodeFrameManager = new NodeFrameManager ( );
		$serverFrameManager = new ServerFrameManager ( );
		$batchManager = new BatchManager ( );
		$serverError = new ServerErrorManager ( );

		$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Starting Scheduler")." $synchro_pid" );

		$mutex = new Mutex ( Config::getValue ( "AppRoot" ) . Config::getValue ( "TempRoot" ) . "/scheduler.lck" );

		if (! $mutex->acquire ()) {
			$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Lock file existing") );
			die ();
		}

		$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Getting lock..."), true );

		$voidCycles = 0;
		$cycles = 0;

		// Main loop

		$batchManager->checkFramesIntegrity();

		do {

			// STOPPER
			$stopper_file_path = Config::getValue ( "AppRoot" ) . Config::getValue ( "TempRoot" ) . "/scheduler.stop";
			if (file_exists ( $stopper_file_path )) {
				$mutex->release ();
				$msg = _("STOP: Detected file")." $stopper_file_path "._("You need to delete this file in order to restart Scheduler successfully");
				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8,  $msg);
				die ( $msg );
			}



			$batchManager->setBatchsActiveOrEnded ( $testTime );

			$activeAndEnabledServers = $serverError->getServersForPumping ();

			$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, print_r ( $activeAndEnabledServers, true ) );


			$batchProcess = $batchManager->getBatchToProcess ();

			if (! $activeAndEnabledServers || sizeof ( $activeAndEnabledServers ) == 0) {

				// There aren't Active & Enable servers...


				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, _("No active server") );

				// This is a void cycle...
				$voidCycles ++;

				// Sleeping...
				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Sleeping...") );
				sleep ( SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE );

			} elseif (! $batchProcess) {

				// No processable Batchs found...

				// Calling Pumpers...


				$pumperManager->callingPumpers ( $activeAndEnabledServers );

				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("No proccessable batchs found") );

				// This is a void cycle...
				$voidCycles ++;

				// Sleeping...
				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Sleeping...") );
				sleep ( SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE );

			} else {

				// Some processable Bacths found...

				$startStamp = time ();
				$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, "[Id: $startStamp]"._("STARTING BATCH PROCESSING") );

				while ( $batchProcess ) {

					// This a full cycle...


					$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, _("Cycle num")." $cycles" );

					if ($cycles >= MAX_NUM_CICLOS_SCHEDULER) {

						// Exceding max. cycles...


						$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, sprintf(_("Exceding max. cycles (%d > %d). Exiting scheduler"),$cycles, MAX_NUM_CICLOS_SCHEDULER)  );
						$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, "[Id: $startStamp] "._("STOPPING BATCH PROCESSING") );
						$mutex->release ();
						die ();
					}

					// ---------------------------------------------------------
					// 1) Solving NodeFrames activity
					// ---------------------------------------------------------


					$batchId = $batchProcess ['id'];
					$batchType = $batchProcess ['type'];
					$batchNodeGenerator = $batchProcess ['nodegenerator'];
					$minorCycle = $batchProcess ['minorcycle'];
					$majorCycle = $batchProcess ['majorcycle'];
					$totalServerFrames = $batchProcess ['totalserverframes'];

					$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, sprintf(_("Processing batch %s type %s"),$batchId, $batchType).", true" );

					$nodeFrames = array ();
					$schedulerChunk = (SCHEDULER_CHUNK > MAX_NUM_NODES_PER_BATCH) ? SCHEDULER_CHUNK : MAX_NUM_NODES_PER_BATCH;
					$nodeFrames = $nodeFrameManager->getNotProcessNodeFrames ( $batchId, $schedulerChunk, $batchType );

					foreach ( $nodeFrames as $nodeFrameData ) {

						$nodeId = $nodeFrameData ['nodeId'];
						$nodeFrameId = $nodeFrameData ['nodeFrId'];
						$version = $nodeFrameData ['version'];
						$timeUp = $nodeFrameData ['up'];
						$timeDown = $nodeFrameData ['down'];

						$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, sprintf(_("Checking activity, nodeframe %s for batch %s"),$nodeFrameId,$batchId) );

						$result = $nodeFrameManager->checkActivity ( $nodeFrameId, $nodeId, $timeUp, $timeDown, $batchType, $testTime );
					}

					// ---------------------------------------------------------
					// 2) Pumping
					// ---------------------------------------------------------


					$pumperManager->callingPumpers ( $activeAndEnabledServers );

					// ---------------------------------------------------------
					// 3) Updating batch data
					// ---------------------------------------------------------


					$batchManager->setCyclesAndPriority ( $batchId );

					// ---------------------------------------------------------
					// 4) Again
					// ---------------------------------------------------------


					$batchManager->setBatchsActiveOrEnded ( $testTime );

					$activeAndEnabledServers = $serverError->getServersForPumping ();

					$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, print_r ( $activeAndEnabledServers, true ) );

					$batchProcess = $batchManager->getBatchToProcess ();

					$cycles ++;
				}

				if ($startStamp > 0) {

					$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, "[Id: $startStamp] "._("STOPPING BATCH PROCESSING") );
				}

			}

			if ($global_execution) {
				if ($voidCycles > MAX_NUM_CICLOS_VACIOS_SCHEDULER) {
					XMD_Log::info(sprintf(_("Exceding max. cycles (%d > %d). Exit scheduler"),$voidCycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER) );
					break;
				}
			} else {
				if (ServerFrameManager::isSchedulerEnded ()) {
					return true;
				}
				// Just for testing purpouses
				if ($voidCycles < 5) {
					return false;
				}
			}

		} while ( true );

		$syncStatObj->create ( null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, sprintf(_("Exceding max. cycles (%d > %d). Exit scheduler"),$cycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
		$mutex->release ();
	}
}

?>
