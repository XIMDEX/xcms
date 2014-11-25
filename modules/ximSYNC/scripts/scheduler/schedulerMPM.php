#!/usr/bin/php -q
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


if (!defined('XIMDEX_ROOT_PATH'))
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));


//

ModulesManager::file('/inc/utils.php');
ModulesManager::file('/inc/manager/NodeFrameManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/ServerFrameManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/PumperManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/BatchManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/ServerErrorManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/Batch.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/SynchronizerStat.class.php', 'ximSYNC');
ModulesManager::file('/conf/synchro.conf', 'ximSYNC');
ModulesManager::file('/inc/sync/Mutex.class.php');
ModulesManager::file('/inc/Profiler.class.php', 'ximPROFILER');
ModulesManager::file('/inc/MPM/MPMManager.class.php');
ModulesManager::file('/inc/MPM/MPMProcess.class.php');

if (!ModulesManager::isEnabled('XIMSYNC')) {
    die(_("ximSYNC module is not active, you must run syncronizer module") . "\n");
}

GLOBAL $synchro_pid;
$synchro_pid = posix_getpid();


// MAIN BUCLE
mainLoop();

function mainLoop()
{

    $batchManager = new BatchManager();
    $serverError = new ServerErrorManager();
    $syncStatObj = new SynchronizerStat();
    $startStamp = 0;
    $voidCycles = 0;
    $testTime = NULL;
    if (isset($argv[1])) {
        $testTime = $argv[1];
    }

    $syncStatObj = new SynchronizerStat();

    //Init
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, _("Starting Scheduler") . " $synchro_pid");
    //Adquire the mutex
    $mutex = new Mutex(Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/scheduler.lck");
    if (!$mutex->acquire()) {
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, _("Lock file existing"));
        die();
    }
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, _("Getting lock..."), true);


    do {
        // STOPPER
        $stopper_file_path = Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/scheduler.stop";
        if (file_exists($stopper_file_path)) {
            $mutex->release();
            die(_("STOP: Detected file") . " $stopper_file_path " . _("You need to delete this file in order to restart Scheduler successfully"));
        }

        $activeAndEnabledServers = $serverError->getServersForPumping();
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, print_r($activeAndEnabledServers, true));

        $batchManager->setBatchsActiveOrEnded($testTime);
        //Get all bachs to be process
        $batchsToProcess = $batchManager->getAllBatchToProcess();

        //Switch each case
        if (!$activeAndEnabledServers || sizeof($activeAndEnabledServers) == 0) {
            // There aren't Active & Enable servers...
            noActiveAndEnabledServers($syncStatObj);
            $voidCycles++;
        } elseif (!$batchsToProcess) {
            // No processable Batchs found...
            noBatchsToProcess($syncStatObj);
            $voidCycles++;
        } else {
            //There are processable Batchs
            processAllBachs($batchsToProcess);
            processTaskForPumping();
            //Set the state for the batchs ended
            $batchManager->setBatchsActiveOrEnded($testTime);
        }
    } while ($voidCycles < MAX_NUM_CICLOS_VACIOS_SCHEDULER);

    //kill the scheduler, so many void cycles
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, sprintf(_("Exceding max. cycles (%d > %d). Exiting scheduler"), $voidCycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
    $mutex->release();
    die();
}


//AUXILIARY FUNCTIONS

/**
 * Enter description here...
 *
 */
function noBatchsToProcess($syncStatObj)
{
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, _("No proccessable batchs found"));

    // Sleeping...
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, "Sleeping...");
    sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);
}

/**
 * Enter description here...
 *
 */
function noActiveAndEnabledServers($syncStatObj)
{
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "ERROR", 8, _("No active server"));

    // Sleeping...
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, _("Sleeping..."));
    sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);
}

/**
 * Enter description here...
 *
 */
function processAllBachs($batchsToProcess)
{

    $batchManager = new BatchManager();
    $callback = "processBatch";
    $mpm = new MPMManager($callback, $batchsToProcess, MPMProcess::MPM_PROCESS_OUT_BOOL, 4, 3);
    $mpm->run();
}

/**
 * Enter description here...
 *
 */
function processBatch($batchProcess)
{

    $syncStatObj = new SynchronizerStat();
    $batchManager = new BatchManager();
    $nodeFrameManager = new NodeFrameManager();
    $startStamp = mktime();

    // ---------------------------------------------------------
    // 1) Solving NodeFrames activity
    // ---------------------------------------------------------
    $batchId = $batchProcess['id'];
    $batchType = $batchProcess['type'];
    $batchNodeGenerator = $batchProcess['nodegenerator'];
    $minorCycle = $batchProcess['minorcycle'];
    $majorCycle = $batchProcess['majorcycle'];
    $totalServerFrames = $batchProcess['totalserverframes'];

    //Trazas
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "[CACTI]SCHEDULER-INFO", 8, "[Id: $startStamp] " . _("STARTING BATCH PROCESSING") . " $batchId");
    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, sprintf(_("Processing batch %s type %s"), $batchId, $batchType) . ", true");

    $nodeFrames = array();
    $nodeFrames = $nodeFrameManager->getNotProcessNodeFrames($batchId, SCHEDULER_CHUNK, $batchType);

    foreach ($nodeFrames as $nodeFrameData) {
        $nodeId = $nodeFrameData['nodeId'];
        $nodeFrameId = $nodeFrameData['nodeFrId'];
        $version = $nodeFrameData['version'];
        $timeUp = $nodeFrameData['up'];
        $timeDown = $nodeFrameData['down'];

        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, sprintf(_("Checking activity, nodeframe %s for batch %s"), $nodeFrameId, $batchId));

        $result = $nodeFrameManager->checkActivity($nodeFrameId, $nodeId, $timeUp, $timeDown,
            $batchType, $testTime);
    }
    // ---------------------------------------------------------
    // 3) Updating batch data
    // ---------------------------------------------------------
    $batchManager->setCyclesAndPriority($batchId);

    //$activeAndEnabledServers = $serverError->getServersForPumping();
    //	$syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
    //	__LINE__, "INFO", 8, print_r($activeAndEnabledServers, true));

    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "[CACTI]SCHEDULER-INFO", 8, "[Id: $startStamp] " . _("STOPPING BATCH PROCESSING") . " $batchId");
}

/**
 * Enter description here...
 *
 */
function processTaskForPumping()
{
    // ---------------------------------------------------------
    // 2) Pumping
    // ---------------------------------------------------------
    $syncStatObj = new SynchronizerStat();
    $pumperManager = new PumperManager();
    $serverFrameManager = new ServerFrameManager();
    $serverError = new ServerErrorManager();
    $activeAndEnabledServers = $serverError->getServersForPumping();

    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
        __LINE__, "INFO", 8, _("Calling pumpers"));

    $pumpers = $serverFrameManager->getPumpersWithTasks($activeAndEnabledServers);

    if (!is_null($pumpers) && count($pumpers) > 0) {
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, _("There are tasks for pumping"));
        //Change to DueToIn_ to DueToIn
        $serverFrameManager->setTasksForPumping($pumpers, SCHEDULER_CHUNK, $activeAndEnabledServers);

        $result = $pumperManager->checkAllPumpers($pumpers, PUMPER_SCRIPT_MODE);

        if ($result == false) {
            $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, _("All pumpers with errors"));
            break;
        }
    }
}