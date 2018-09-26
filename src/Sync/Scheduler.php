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

namespace Ximdex\Sync;

use Ximdex\Helpers\ServerConfig;
use Ximdex\Models\Batch;
use Ximdex\Models\PortalFrames;
use Ximdex\Models\Pumper;
use Ximdex\Models\Server;
use Ximdex\Models\Channel;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\Node;
use Ximdex\Models\User;
use Ximdex\Runtime\App;
use Ximdex\Logger;
use Ximdex\Utils\Date;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

class Scheduler
{
    public static function start($global_execution = true)
    {
        global $argv;
        $synchro_pid = posix_getpid();
        $startStamp = 0;
        $testTime = NULL;
        if (isset ($argv [1])) {
            $testTime = $argv [1];
        }
        $pumperManager = new PumperManager();
        $nodeFrameManager = new NodeFrameManager();
        $batchManager = new BatchManager();
        $serverError = new ServerErrorManager();
        $ximdexServerConfig = new ServerConfig();
        
        // Checking pcntl_fork function is not disabled
        if ($ximdexServerConfig->hasDisabledFunctions()) {
            Logger::error("Closing scheduler. Disabled pcntl_fork and pcntl_waitpid functions are required. Please, check php.ini file." 
                . "\r\n");
        }
        Logger::info('Starting Scheduler ' . $synchro_pid);
        $mutex = new Mutex(XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/scheduler.lck");
        if (!$mutex->acquire()) {
            Logger::info('Lock file existing');
            die();
        }
        Logger::info('Getting lock...');
        $voidCycles = 0;
        $cycles = 0;

        // Main loop
        $batchManager->checkFramesIntegrity();
        do {
            
            // STOPPER
            $stopper_file_path = XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/scheduler.stop";
            if (file_exists($stopper_file_path)) {
                $mutex->release();
                Logger::warning('STOP: Detected file ' . $stopper_file_path 
                    . '. You need to delete this file in order to restart Scheduler successfully');
                @unlink(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/scheduler.lck');
                die();
            }
            $batchManager->setBatchsActiveOrEnded($testTime);
            $activeAndEnabledServers = $serverError->getServersForPumping();
            Logger::debug('Active and enabled servers: ' . print_r($activeAndEnabledServers, true));
            $batchProcess = $batchManager->getBatchToProcess();
            if (!$activeAndEnabledServers || count($activeAndEnabledServers) == 0) {
                
                // There aren't Active & Enable servers...
                Logger::error('No active server');

                // This is a void cycle...
                $voidCycles++;

                // Sleeping...
                Logger::info('Sleeping...');
                sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);

            } elseif (!$batchProcess) {

                // No processable Batchs found...
                // Calling Pumpers...
                $pumperManager->callingPumpers($activeAndEnabledServers);
                Logger::info('No processable batchs found');

                // This is a void cycle...
                $voidCycles++;

                // Sleeping...
                Logger::info('Sleeping...');
                sleep(SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE);

            } else {

                // Some processable Batchs found...
                $startStamp = time();
                Logger::info("[Id: $startStamp] STARTING BATCH PROCESSING");
                while ($batchProcess) {

                    // This a full cycle...
                    Logger::debug('Cycle num ' . $cycles);
                    if ($cycles >= MAX_NUM_CICLOS_SCHEDULER) {
                        
                        // Exceding max. cycles...
                        Logger::info(sprintf('Max. cycles exceeded (%d > %d). Exiting scheduler', $cycles, MAX_NUM_CICLOS_SCHEDULER));
                        Logger::info("[Id: $startStamp] STOPPING BATCH PROCESSING");
                        $mutex->release();
                        die();
                    }

                    // ---------------------------------------------------------
                    // 1) Solving NodeFrames activity
                    // ---------------------------------------------------------
                    $batchId = $batchProcess['id'];
                    $batchType = $batchProcess['type'];
                    Logger::debug(sprintf("Processing batch %s type %s", $batchId, $batchType) . ", true");
                    $schedulerChunk = (SCHEDULER_CHUNK > MAX_NUM_NODES_PER_BATCH) ? SCHEDULER_CHUNK : MAX_NUM_NODES_PER_BATCH;
                    $nodeFrames = $nodeFrameManager->getNotProcessNodeFrames($batchId, $schedulerChunk, $batchType);
                    if ($nodeFrames !== false) {
                        foreach ($nodeFrames as $nodeFrameData) {
                            $nodeId = $nodeFrameData ['nodeId'];
                            $nodeFrameId = $nodeFrameData ['nodeFrId'];
                            $timeUp = $nodeFrameData ['up'];
                            $timeDown = $nodeFrameData ['down'];
                            Logger::info(sprintf('Checking activity, nodeframe %s for batch %s', $nodeFrameId, $batchId));
                            $nodeFrameManager->checkActivity($nodeFrameId, $nodeId, $timeUp, $timeDown, $batchType, $testTime);
                        }
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
                    Logger::debug(print_r($activeAndEnabledServers, true));
                    $batchProcess = $batchManager->getBatchToProcess();
                    
                    // Show publication status stats
                    if ($cycles % CYCLES_BETWEEN_SHOW_STATS == 0) {
                        try {
                            self::log_status();
                        } catch (\Exception $e) {
                            Logger::error($e->getMessage());
                        }
                    }
                    $cycles++;
                }
                if ($startStamp > 0) {
                    Logger::info("[Id: $startStamp] STOPPING BATCH PROCESSING");
                }
            }
            if ($global_execution) {
                if ($voidCycles > MAX_NUM_CICLOS_VACIOS_SCHEDULER) {
                    Logger::info(sprintf("max. cycles exceeded (%d > %d). Exit scheduler ", $voidCycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
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
            
            // Show publication status stats
            try {
                self::log_status();
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
            }
        } while (true);
        Logger::info(sprintf("max. cycles exceeded (%d > %d). Exit scheduler ", $cycles, MAX_NUM_CICLOS_VACIOS_SCHEDULER));
        $mutex->release();
    }
    
    /**
     * Print to log the publication stats
     */
    private static function log_status() : void
    {
        // General resume stats to log
        Logger::generate('PUBLICATION', 'publication');
        Logger::setActiveLog('publication');
        self::general_stats();
        
        // Portal frames stats to log
        Logger::setActiveLog();
        self::portal_frames_stats();
        
        // Switch to scheduler log file
        Logger::setActiveLog('scheduler');
    }
    
    private static function general_stats() : void
    {
        $pumpersTotal = 0;
        $framesPendingTotal = 0;
        $framesActiveTotal = 0;
        $batchsInTime = Batch::countBatchsInProcess();
        $batchsClosing = Batch::countBatchsInProcess(Batch::CLOSING);
        Logger::info('SCHEDULER STATS [' . $batchsInTime . ' batchs in time, ' . $batchsClosing . ' batchs closing]', false, 'white');
        
        // Obtain a list of servers with server frames active
        $servers = ServerFrame::serversInActiveServerFrames();
        foreach ($servers as $serverId) {
            
            // Stats information for each server
            $server = new Server($serverId);
            $serverFramesPending = 0;
            $serverFramesActive = 0;
            Logger::info('Server ' . $server->get('Description'));
            
            // Stats information for each channel in the current server
            foreach ($server->getChannels() as $channelId) {
                $channel = new Channel($channelId);
                $serverFramesPending += $framesPending = ServerFrame::countServerFrames([ServerFrame::PENDING, ServerFrame::DUE2OUT], [], true,
                    $serverId, $channelId);
                $serverFramesActive += $framesActive = ServerFrame::countServerFrames([],
                    array_merge(ServerFrame::FINAL_STATUS, [ServerFrame::PENDING]), true, $serverId, $channelId);
                if ($framesPending or $framesActive) {
                    Logger::info(' - Channel ' . $channel->GetName() . ': ' . $framesPending . ' frames pending, '
                        . $framesActive . ' frames active');
                }
            }
            
            // Stats without channel (ChannelId = null), sending a zero value
            $serverFramesPending += $framesPending = ServerFrame::countServerFrames([ServerFrame::PENDING, ServerFrame::DUE2OUT], [], true, 
                $serverId, 0);
            $serverFramesActive += $framesActive = ServerFrame::countServerFrames([],
                array_merge(ServerFrame::FINAL_STATUS, [ServerFrame::PENDING]), true, $serverId, 0);
            if ($framesPending or $framesActive) {
                Logger::info(' - No channel: ' . $framesPending . ' frames pending, ' . $framesActive .' frames active');
            }
            
            // Stats information for server
            $serverPumpers = Pumper::countPumpers(true, $serverId);
            Logger::info(' Server totals: ' . $serverFramesPending . ' frames pending, ' . $serverFramesActive .' frames active, '
                . $serverPumpers . ' pumpers');
            
            // Sum totals
            $framesPendingTotal += $serverFramesPending;
            $framesActiveTotal += $serverFramesActive;
            $pumpersTotal += $serverPumpers;
        }
        
        // Log for total resume
        Logger::info('Total: ' . $framesPendingTotal . ' frames pending, ' . $framesActiveTotal .' frames active, '
            . $pumpersTotal . ' pumpers');
    }
    
    private static function portal_frames_stats() : void
    {
        $portals = PortalFrames::getByState(PortalFrames::STATUS_ACTIVE);
        if ($portals) {
            Logger::info('ACTIVE PORTAL FRAMES', false, 'white');
            foreach ($portals as $portal) {
                self::log_portal_frame($portal);
            }
        }
        $portals = PortalFrames::getByState(PortalFrames::STATUS_ENDED, 3600);
        if ($portals) {
            Logger::info('ENDED PORTAL FRAMES (Last hour)', false, 'white');
            foreach ($portals as $portal) {
                self::log_portal_frame($portal);
            }
        }
        Logger::info('PORTAL FRAMES SUMMARY', false, 'white');
        $resume = PortalFrames::resume();
        Logger::info('Total by status: ' . $resume['states'][PortalFrames::STATUS_CREATED] . ' created, '
            . $resume['states'][PortalFrames::STATUS_ACTIVE] . ' active, '
            . $resume['states'][PortalFrames::STATUS_ENDED] . ' ended');
        Logger::info('Total by type: ' . $resume['types'][PortalFrames::TYPE_UP] . ' type Up, '
            . $resume['types'][PortalFrames::TYPE_DOWN] . ' type down');
    }
    
    private static function log_portal_frame(PortalFrames $portal) : void
    {
        if (!$portal->get('IdNodeGenerator')) {
            Logger::warning('Portal frame with ID: ' . $portal->get('id') . ' has not a node generator');
            return;
        }
        $node = new Node($portal->get('IdNodeGenerator'));
        if (!$node->GetID()) {
            Logger::error('Cannot load in portal frames stats the node generator ' . $portal->get('IdNodeGenerator'));
            return;
        }
        if (!$portal->get('CreatedBy')) {
            Logger::warning('Portal frame with ID: ' . $portal->get('id') . ' has not an user');
            return;
        }
        $user = new User($portal->get('CreatedBy'));
        if (!$user->get('IdUser')) {
            Logger::error('Cannot load in portal frames stats the user ' . $portal->get('CreatedBy'));
            return;
        }
        Logger::info('Portal frame ' . $portal->get('id') . ': Generator node ' . $portal->get('IdNodeGenerator')
            . ' (' . $node->GetNodeName() . '), version ' . $portal->get('Version') . ', type ' . $portal->get('PublishingType')
            . ', user ' . $portal->get('CreatedBy') . ' (' . $user->getLogin() . ')');
        Logger::info('  - Start time: ' . Date::formatTime($portal->get('StartTime')));
        if ($portal->get('EndTime')) {
            Logger::info('  - End time: ' . Date::formatTime($portal->get('EndTime')));
        } else {
            Logger::info('  - Status time: ' . Date::formatTime($portal->get('StatusTime')));
        }
        $info = ' - Server frames: ' . $portal->get('SFtotal') . ' total, ' . $portal->get('SFpending') . ' pending, ' 
            . $portal->get('SFactive') . ' active, ' . $portal->get('SFsuccess') . ' success, ' . $portal->get('SFerrored') . ' errored';
        if ($portal->get('SFerrored')) {
            Logger::error($info);
        } else {
            Logger::info(' ' . $info);
        }
    }
}
