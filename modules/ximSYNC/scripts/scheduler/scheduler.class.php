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

use Ximdex\Helpers\ServerConfig;
use Ximdex\Runtime\App;
use Ximdex\Sync\Mutex;
use Ximdex\Logger;

\Ximdex\Modules\Manager::file('/inc/manager/NodeFrameManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/manager/ServerFrameManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/manager/PumperManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/manager/BatchManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/manager/ServerErrorManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/Batch.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/SynchronizerStat.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/conf/synchro_conf.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');

if (!\Ximdex\Modules\Manager::isEnabled('XIMSYNC')) {
    Logger::error(_("ximSYNC module is not active, you must run syncronizer module") . "\n");
    die();
}
$synchro_pid = null;

class Scheduler
{
    public static function start($global_execution = true)
    {
        global $synchro_pid, $argv;
        $synchro_pid = posix_getpid();
        $startStamp = 0;
        $testTime = NULL;
        if (isset ($argv [1])) {
            $testTime = $argv [1];
        }
        $syncStatObj = new SynchronizerStat ();
        $pumperManager = new PumperManager ();
        $nodeFrameManager = new NodeFrameManager ();
        $serverFrameManager = new ServerFrameManager ();
        $batchManager = new BatchManager ();
        $serverError = new ServerErrorManager ();
        $ximdexServerConfig = new ServerConfig();
        
        // Checking pcntl_fork function is not disabled
        if ($ximdexServerConfig->hasDisabledFunctions()) {
            Logger::error(_("Closing scheduler. Disabled pcntl_fork and pcntl_waitpid functions are required. Please, check php.ini file.") . "\r\n");
        }
        $msg = _("Starting Scheduler") . " $synchro_pid";
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
        Logger::info($msg);
        $mutex = new Mutex (XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/scheduler.lck");
        if (!$mutex->acquire()) {
            $msg = _("Lock file existing");
            $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
            Logger::info($msg);
            die ();
        }
        $msg = _("Getting lock...");
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg, true);
        Logger::info($msg);
        $voidCycles = 0;
        $cycles = 0;

        // Main loop
        $batchManager->checkFramesIntegrity();
        do {
            
            // STOPPER
            $stopper_file_path = XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/scheduler.stop";
            if (file_exists($stopper_file_path)) {
                $mutex->release();
                $msg = _("STOP: Detected file") . " $stopper_file_path " . _("You need to delete this file in order to restart Scheduler successfully");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                Logger::warning($msg);
                @unlink(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/scheduler.lck');
                die();
            }
            $batchManager->setBatchsActiveOrEnded($testTime);
            $activeAndEnabledServers = $serverError->getServersForPumping();
            $msg = 'Active and enabled servers: ' . print_r($activeAndEnabledServers, true);
            $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
            Logger::info($msg);
            $batchProcess = $batchManager->getBatchToProcess();
            if (!$activeAndEnabledServers || count($activeAndEnabledServers) == 0) {
                
                // There aren't Active & Enable servers...
                $msg = _("No active server");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, $msg);
                Logger::error($msg);

                // This is a void cycle...
                $voidCycles++;

                // Sleeping...
                $msg = _("Sleeping...");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                Logger::info($msg);
                sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);

            } elseif (!$batchProcess) {

                // No processable Batchs found...
                // Calling Pumpers...
                $pumperManager->callingPumpers($activeAndEnabledServers);
                $msg = _("No processable batchs found");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                Logger::info($msg);

                // This is a void cycle...
                $voidCycles++;

                // Sleeping...
                $msg = _("Sleeping...");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                Logger::info($msg);
                sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);

            } else {

                // Some processable Batchs found...
                $startStamp = time();
                $msg = "[Id: $startStamp]" . _("STARTING BATCH PROCESSING");
                $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, $msg);
                Logger::info($msg);
                while ($batchProcess) {

                    // This a full cycle...
                    $msg = _("Cycle num") . " $cycles";
                    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                    Logger::info($msg);
                    if ($cycles >= MAX_NUM_CICLOS_SCHEDULER) {
                        
                        // Exceding max. cycles...
                        $msg = sprintf(_("Exceding max. cycles (%d > %d). Exiting scheduler"), $cycles, MAX_NUM_CICLOS_SCHEDULER);
                        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                        Logger::info($msg);
                        $msg = "[Id: $startStamp] " . _("STOPPING BATCH PROCESSING");
                        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, $msg);
                        Logger::info($msg);
                        $mutex->release();
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
                    $msg = sprintf(_("Processing batch %s type %s"), $batchId, $batchType) . ", true";
                    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                    Logger::info($msg);
                    $nodeFrames = array();
                    $schedulerChunk = (SCHEDULER_CHUNK > MAX_NUM_NODES_PER_BATCH) ? SCHEDULER_CHUNK : MAX_NUM_NODES_PER_BATCH;
                    $nodeFrames = $nodeFrameManager->getNotProcessNodeFrames($batchId, $schedulerChunk, $batchType);
                    foreach ($nodeFrames as $nodeFrameData) {
                        $nodeId = $nodeFrameData ['nodeId'];
                        $nodeFrameId = $nodeFrameData ['nodeFrId'];
                        $version = $nodeFrameData ['version'];
                        $timeUp = $nodeFrameData ['up'];
                        $timeDown = $nodeFrameData ['down'];
                        $msg = sprintf(_("Checking activity, nodeframe %s for batch %s"), $nodeFrameId, $batchId);
                        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                        Logger::info($msg);
                        $result = $nodeFrameManager->checkActivity($nodeFrameId, $nodeId, $timeUp, $timeDown, $batchType, $testTime);
                    }

                    // ---------------------------------------------------------
                    // 2) Pumping
                    // ---------------------------------------------------------
                    $pumperManager->callingPumpers($activeAndEnabledServers);

                    // ---------------------------------------------------------
                    // 3) Updating batch data
                    // ---------------------------------------------------------
                    $batchManager->setCyclesAndPriority($batchId);

                    // ---------------------------------------------------------
                    // 4) Again
                    // ---------------------------------------------------------
                    $batchManager->setBatchsActiveOrEnded($testTime);
                    $activeAndEnabledServers = $serverError->getServersForPumping();
                    $msg = print_r($activeAndEnabledServers, true);
                    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
                    Logger::info($msg);
                    $batchProcess = $batchManager->getBatchToProcess();
                    $cycles++;
                }
                if ($startStamp > 0) {
                    $msg = "[Id: $startStamp] " . _("STOPPING BATCH PROCESSING");
                    $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "[CACTI]SCHEDULER-INFO", 8, $msg);
                    Logger::info($msg);
                }
            }
            if ($global_execution) {
                if ($voidCycles > MAX_NUM_CICLOS_VACIOS_SCHEDULER) {
                    Logger::info(sprintf(_("Exceding max. cycles (%d > %d). Exit scheduler"), $voidCycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
                    break;
                }
            } else {
                if (ServerFrameManager::isSchedulerEnded()) {
                    return true;
                }
                
                // Just for testing purpouses
                if ($voidCycles < 5) {
                    return false;
                }
            }

        } while (true);
        $msg = sprintf(_("Exceding max. cycles (%d > %d). Exit scheduler"), $cycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER);
        $syncStatObj->create(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8, $msg);
        Logger::info($msg);
        $mutex->release();
    }
}