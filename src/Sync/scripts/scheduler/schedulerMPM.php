#!/usr/bin/env php
<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Runtime\App;
use Ximdex\Sync\Mutex;
use Ximdex\Sync\BatchManager;
use Ximdex\Sync\ServerErrorManager;
use Ximdex\Sync\NodeFrameManager;
use Ximdex\Sync\PumperManager;
use Ximdex\Sync\ServerFrameManager;
use Ximdex\MPM\MPMManager;
use Ximdex\MPM\MPMProcess;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

GLOBAL $synchro_pid;
$synchro_pid = posix_getpid();

// MAIN BUCLE
mainLoop();

function mainLoop()
{
    GLOBAL $synchro_pid;
    $batchManager = new BatchManager();
    $serverError = new ServerErrorManager();
    $voidCycles = 0;
    $testTime = NULL;
    if (isset($argv[1])) {
        $testTime = $argv[1];
    }

    //Init
    Logger::info("Starting Scheduler " . $synchro_pid);
    
    // Adquire the mutex
    $mutex = new Mutex(XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/scheduler.lck");
    if (!$mutex->acquire()) {
        Logger::info('Lock file existing');
        die();
    }
    Logger::info('Getting lock...');
    do {
        
        // STOPPER
        $stopper_file_path =  XIMDEX_ROOT_PATH .  App::getValue("TempRoot") . "/scheduler.stop";
        if (file_exists($stopper_file_path)) {
            $mutex->release();
            die(_("STOP: Detected file") . " $stopper_file_path " 
                . _("You need to delete this file in order to restart Scheduler successfully"));
        }
        $activeAndEnabledServers = $serverError->getServersForPumping();
        Logger::info(print_r($activeAndEnabledServers, true));
        $batchManager->setBatchsActiveOrEnded($testTime);
        
        // Get all bachs to be process
        $batchsToProcess = $batchManager->getAllBatchToProcess();

        //Switch each case
        if (!empty($activeAndEnabledServers) ) {
            
            // There aren't Active & Enable servers...
            noActiveAndEnabledServers();
            $voidCycles++;
        } elseif (!$batchsToProcess) {
            
            // No processable Batchs found...
            noBatchsToProcess();
            $voidCycles++;
        } else {
            
            // There are processable Batchs
            processAllBachs($batchsToProcess);
            processTaskForPumping();
            
            // Set the state for the batchs ended
            $batchManager->setBatchsActiveOrEnded($testTime);
        }
    } while ($voidCycles < MAX_NUM_CICLOS_VACIOS_SCHEDULER);

    // Kill the scheduler, so many void cycles
    Logger::info(sprintf('Exceding max. cycles (%d > %d). Exiting scheduler', $voidCycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
    $mutex->release();
    die();
}

//AUXILIARY FUNCTIONS

function noBatchsToProcess()
{
    Logger::info('No processable batchs found');

    // Sleeping...
    Logger::info('Sleeping...');
    sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);
}

function noActiveAndEnabledServers()
{
    Logger::error('No active server');

    // Sleeping...
    Logger::info('Sleeping...');
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

function processBatch($batchProcess)
{
    $batchManager = new BatchManager();
    $nodeFrameManager = new NodeFrameManager();
    $startStamp = time();

    // ---------------------------------------------------------
    // 1) Solving NodeFrames activity
    // ---------------------------------------------------------
    $batchId = $batchProcess['id'];
    $batchType = $batchProcess['type'];

    //Trazas
    Logger::info("[Id: $startStamp] STARTING BATCH PROCESSING $batchId");
    Logger::info(sprintf(_("Processing batch %s type %s"), $batchId, $batchType));
    $nodeFrames = $nodeFrameManager->getNotProcessNodeFrames($batchId, SCHEDULER_CHUNK, $batchType);
    foreach ($nodeFrames as $nodeFrameData) {
        $nodeId = $nodeFrameData['nodeId'];
        $nodeFrameId = $nodeFrameData['nodeFrId'];
        $timeUp = $nodeFrameData['up'];
        $timeDown = $nodeFrameData['down'];
        Logger::info(sprintf('Checking activity, nodeframe %s for batch %s', $nodeFrameId, $batchId));
        $result = $nodeFrameManager->checkActivity($nodeFrameId, $nodeId, $timeUp, $timeDown,
            $batchType  );
    }
    
    // ---------------------------------------------------------
    // 3) Updating batch data
    // ---------------------------------------------------------
    $batchManager->setCyclesAndPriority($batchId);
    Logger::info("[Id: $startStamp] STOPPING BATCH PROCESSING $batchId");
}

function processTaskForPumping()
{
    // ---------------------------------------------------------
    // 2) Pumping
    // ---------------------------------------------------------
    $pumperManager = new PumperManager();
    $serverFrameManager = new ServerFrameManager();
    $serverError = new ServerErrorManager();
    $activeAndEnabledServers = $serverError->getServersForPumping();
    Logger::info('Calling pumpers');
    $pumpers = $serverFrameManager->getPumpersWithTasks($activeAndEnabledServers);

    if (!is_null($pumpers) && count($pumpers) > 0) {
        Logger::info('There are tasks for pumping');
        
        //Change to DueToIn_ to DueToIn
        $serverFrameManager->setTasksForPumping($pumpers, SCHEDULER_CHUNK, $activeAndEnabledServers);
        $result = $pumperManager->checkAllPumpers($pumpers, PUMPER_SCRIPT_MODE);
        if ($result == false) {
            Logger::info('All pumpers with errors');
            return ;
        }
    }
}
