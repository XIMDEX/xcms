<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\Pumper;
use Ximdex\NodeTypes\ServerNode;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * @brief Handles the activity of Pumpers
 *
 * A Pumper is an instance of the dexPumper script, wich is responsible for sending the ServerFrames to Server (via ftp, ssh, etc)
 */
class PumperManager
{
    /**
     * Checks what Pumpers are running and decides whether start or stop them
     * 
     * @param array pumpersWithTasks
     * @param string modo
     * @return bool
     */
    public function checkAllPumpers(array $pumpersWithTasks = null, string $modo = 'pl') : bool
    {
        if (is_null($pumpersWithTasks) || count($pumpersWithTasks) == 0) {
            $pumper = new Pumper();
            Logger::error('Not pumpers available');
            return false;
        }
        $pumpersWithError = 0;
        foreach ($pumpersWithTasks as $pumperId) {
            $pumper = new Pumper($pumperId);
            if (! $pumper->get('PumperId')) {
                Logger::error('Non-existing pumper ' . $pumperId);
                continue;
            }
            $pumperState = $pumper->get('State');
            $pumperCheckTime = $pumper->get('CheckTime');
            Logger::debug(sprintf('Pumper %s at state %s', $pumperId, $pumperState));
            Logger::debug('Pumper with ID: ' . $pumperId . ' has state: ' . $pumperState);
            switch ($pumperState) {
                case Pumper::STARTED:
                    
                    // Checking if pumper is alive
                    $now = time();
                    if (!Pumper::isAlive($pumper) or ($now - $pumperCheckTime > MAX_CHECK_TIME_FOR_PUMPER)) {
                        Logger::debug('No checking time for pumper ' . $pumperId);
                        
                        // Restart pumper
                        Logger::warning('Pumper with ID: ' . $pumperId . ' will be restarted');
                        $pumper->set('State', Pumper::NEW);
                        $pumper->update();
                        $result = $pumper->startPumper($pumperId, $modo);
                        if ($result == false) {
                            $pumpersWithError++;
                        }
                    }
                    break;
                case Pumper::NEW:
                    $result = $pumper->startPumper($pumperId, $modo);
                    if ($result == false) {
                        $pumpersWithError++;
                    } else {
                        Logger::info('Pumper with ID: ' . $pumperId . ' has been started');
                    }
                    break;

                case Pumper::ENDED:
                    
                    // Pumper ended but new tasks have been included
                    $pumper->set('State', Pumper::NEW);
                    $pumper->update();
                    $result = $pumper->startPumper($pumperId, $modo);
                    if ($result == false) {
                        $pumpersWithError++;
                    }
                    else {
                        Logger::info('Pumper with ID: ' . $pumperId . ' has been started from ended state');
                    }
                    break;
                    
                case Pumper::STARTING:
                    
                    // Pumper is starting...
                    if (time() - $pumper->get('StartTime') > MAX_STARTING_TIME_FOR_PUMPER) {
                        $result = $pumper->startPumper($pumperId, $modo);
                    }
                    else {
                        Logger::warning('Pumper with ID: ' . $pumperId . ' is starting. Aborting creation');
                        usleep(100000);
                    }
                    break;
                default:
                    Logger::debug("Default: $pumperId - $pumperState");
                    break;
            }
        }
        $pumpersInRegistry = $pumper->getPumpersInRegistry();
        if ($pumpersWithError == count($pumpersInRegistry)) {
            $pumper = New Pumper();
            Logger::error('Problems in all pumpers');
            return false;
        }
        return true;
    }

    /**
     * For each Pumper gets the number of ServerFrames needed to complete the chunk and makes them available
     * 
     * @param array $activeAndEnabledServers
     * @return int|NULL
     */
    public function callingPumpers(array $activeAndEnabledServers = null) : ?int
    {
        if (! $activeAndEnabledServers) {
            $activeAndEnabledServers = ServerNode::getServersForPumping();
        }
        if ($activeAndEnabledServers === false) {
            return null;
        }
        if (! $activeAndEnabledServers) {
            return 0;
        }
        Logger::debug('Calling pumpers');
        $serverFrameManager = new ServerFrameManager();
        $pumpers = $serverFrameManager->getPumpersWithTasks($activeAndEnabledServers);
        if (!is_null($pumpers) && count($pumpers) > 0) {
            Logger::debug('There are tasks for pumping');
            $tasks = $serverFrameManager->setTasksForPumping($pumpers, SCHEDULER_CHUNK, $activeAndEnabledServers);
            $result = $this->checkAllPumpers($pumpers, PUMPER_SCRIPT_MODE);
            if ($result == false) {
                Logger::error('All pumpers with errors');
            }
        } else {
            Logger::debug('No pumpers to be called');
            $tasks = 0;
        }
        return $tasks;
    }
}
