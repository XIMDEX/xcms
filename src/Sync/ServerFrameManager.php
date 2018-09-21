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

use Ximdex\Logger;
use Ximdex\Models\Pumper;
use Ximdex\MPM\MPMManager;
use Ximdex\MPM\MPMProcess;
use Ximdex\Models\Batch;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\ChannelFrame;
use Ximdex\Models\NodeFrame;

/**
 * @brief Handles the life cycle of a ServerFrame
 *
 *    A ServerFrame is the representation of a ChannelFrame in a Server
 *    It can have these status:
 *    - Pending: not yet processed
 *    - Due2In / Due2In_: ready to send to Server
 *    - Pumped: the serverFrame have been sended to the Server but it stays hidden, waiting the sending of the rest of ServerFrame of the same Batch
 *    - In: all ServerFrames of the same Batch are in the Server. Publication completed.
 *    - Replaced: the ServerFrame has been replaced by another ServerFrame (with the same name) in the Server
 *    - Removed: the ServerFrame has been replaced by another ServerFrame (with different name) in the Server
 *    - Canceled: the ServerFrame nerver was sended to Server and it's replaced by another ServerFrame
 *    - Due2Out / Due2Out_: ready to removed from server
 *    - Out: removed from server
 */
class ServerFrameManager
{
    const useMPMManager = false;
    
    /**
     * Change the ServerFrame's state
     * 
     * @param int serverFrameId
     * @param string operation
     * @param int nodeId
     * @param int delayed
     * @return bool
     */
    public function changeState($serverFrameId, $operation, $nodeId, $delayed = NULL)
    {
        $serverFrame = new ServerFrame($serverFrameId);
        $initialState = $serverFrame->get('State');
        $server = $serverFrame->get('IdServer');
        $channelFrameId = $serverFrame->get('IdChannelFrame');
        $idNodeFrame = $serverFrame->get('IdNodeFrame');
        $chFr = new ChannelFrame($channelFrameId);
        $channel = $chFr->get('ChannelId');
        if (!$operation || !$serverFrameId) {
            Logger::error('ERROR Params needed');
            return false;
        }
        $republishAncestors = false;
        if ($operation == Batch::TYPE_UP) {
            if ($initialState == ServerFrame::PENDING) {
                
                // Set down overlaped serverFrames
                $overlapeds = $this->getOverlaped($serverFrameId, $server, $nodeId, $channel);
                if (is_array($overlapeds) and sizeof($overlapeds) > 0) {
                    foreach ($overlapeds as $n => $overlapedData) {
                        $id = $overlapedData['id'];
                        $overlapedFrame = new ServerFrame($id);
                        $overlapedInitialState = $overlapedData['state'];
                        if ($overlapedInitialState == ServerFrame::IN || $overlapedInitialState == ServerFrame::PUMPED) {
                            if (!is_null($delayed)) {
                                
                                // Para que resucite
                                $overlapedFinalState = ServerFrame::DELAYED;
                            } else {
                                $overlapedName = $overlapedFrame->get('FileName');
                                $name = $serverFrame->get('FileName');
                                if ($overlapedName != $name) {
                                    
                                    // If name's changed must delete remote file
                                    $overlapedFinalState = ServerFrame::DUE2OUT;
                                    $overlapedFrame->set('DateDown', mktime());
                                } else {
                                    $overlapedFinalState = ServerFrame::REPLACED;
                                }
                                $overlapedFrame->deleteSyncFile();
                            }
                        } else {
                            $overlapedFinalState = ServerFrame::REMOVED;
                            if ($overlapedInitialState != ServerFrame::CANCELLED and $overlapedInitialState != ServerFrame::DUE2IN_) {
                                $overlapedFrame->deleteSyncFile();
                            }
                        }
                        Logger::info("Setting $id as overlaped from $overlapedInitialState to $overlapedFinalState");
                        $overlapedFrame->set('State', $overlapedFinalState);
                        $overlapedFrame->update();
                    }
                }
                $finalState = ServerFrame::DUE2IN_;

                // If is the first publication must republish the ancestors
                $nodeFrame = new NodeFrame();
                if (is_null($nodeFrame->getPrevious($nodeId, $idNodeFrame))) {
                    $republishAncestors = true;
                }
            } else {
                Logger::info("Nothing to do with $serverFrameId starter $initialState");
                return true;
            }
        } elseif ($operation == Batch::TYPE_DOWN) {
            $states = array(ServerFrame::PENDING, ServerFrame::DUE2IN, ServerFrame::DUE2IN_, ServerFrame::DUE2INWITHERROR);
            if (in_array($initialState, $states)) {
                $finalState = ServerFrame::CANCELLED;
                $serverFrame->deleteSyncFile();
            } elseif ($initialState == ServerFrame::IN || $initialState == ServerFrame::PUMPED) {
                $finalState = ServerFrame::REPLACED;
                $delayedId = $this->getDelayed($serverFrameId, $server, $nodeId, $channel);
                if (!is_null($delayedId)) {
                    $canceledFrame = new ServerFrame($delayedId);
                    $canceledFrame->set('State', ServerFrame::DUE2IN_);
                    $canceledFrame->update();
                    Logger::info("Setting frame from Delayed $delayedId to Due2In_");
                } else {
                    $finalState = ServerFrame::DUE2OUT_;
                    $serverFrame->deleteSyncFile();
                    $republishAncestors = true;
                }
            } else {
                Logger::info("Nothing to do for state $initialState");
            }
        } else {
            Logger::error("Incorrect operation $operation");
        }
        $pumperId = $this->calcPumper($serverFrameId);
        $serverFrame->set('State', (isset($finalState)) ? $finalState : $initialState);
        $serverFrame->set('PumperId', $pumperId);
        $result = $serverFrame->update();
        if (!isset($finalState)) {
            $finalState = 'unknown final state';
        }
        if ($result === false) {
            Logger::error("Changing frame $serverFrameId to $finalState");
            return false;
        }
        if ($result) {
            Logger::info("Setting frame $serverFrameId from $initialState to $finalState");
        }
        return true;
    }

    /**
     * Gets the ServerFrames whose State is Delayed
     * 
     * @param int frameId
     * @param int server
     * @param int nodeId
     * @param int channel
     * @return int|null
     */
    public function getDelayed($frameId, $server, $nodeId, $channel)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT ServerFrames.IdSync AS IdSync FROM NodeFrames, ServerFrames, ChannelFrames
				WHERE ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame
				AND ServerFrames.IdChannelFrame = ChannelFrames.idChannelFrame
				AND ServerFrames.IdServer = $server
				AND NodeFrames.NodeId = $nodeId";
        if ($channel) {
            $sql .= " AND ChannelFrames.ChannelId = $channel";
        }
        else {
            $sql .= ' AND ChannelFrames.ChannelId IS NULL';
        }
        $sql .= " AND ServerFrames.IdSync != $frameId AND ServerFrames.State = '" . ServerFrame::DELAYED . "'";
        $dbObj->Query($sql);
        if ($dbObj->numRows) {
            return $dbObj->GetValue('IdSync');
        }
        return NULL;
    }

    /**
     * Gets the ServerFrames of the same Node, Channel and Server, and State in (in, due2in, due2in_, pumped, canceled, due2inwitherror)
     * 
     * @param int frameId
     * @param int server
     * @param int nodeId
     * @param int channel
     * @return int|null
     */
    public function getOverlaped($frameId, $server, $nodeId, $channel)
    {
        if (is_null($channel)) {
            $channelCondition = " IS NULL ";
        } else {
            $channelCondition = " = $channel ";
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT ServerFrames.IdSync AS IdSync, ServerFrames.State AS State,
				ServerFrames.RemotePath AS RemotePath, ServerFrames.FileName AS FileName
				FROM NodeFrames, ServerFrames, ChannelFrames
				WHERE ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame
				AND ServerFrames.IdChannelFrame = ChannelFrames.idChannelFrame
				AND ServerFrames.IdServer = $server
				AND NodeFrames.NodeId = $nodeId
				AND ChannelFrames.ChannelId $channelCondition
				AND ServerFrames.IdSync != $frameId
				AND (ServerFrames.State = '" . ServerFrame::IN . "' OR ServerFrames.State = '" . ServerFrame::DUE2IN . "' 
		        OR ServerFrames.State = '" . ServerFrame::DUE2IN_ . "' OR ServerFrames.State = '" . ServerFrame::CANCELLED . "' 
                OR ServerFrames.State = '" . ServerFrame::PUMPED . "' OR ServerFrames.State = '" . ServerFrame::DUE2INWITHERROR . "')";
        $overlaped = array();
        $i = 0;
        $dbObj->Query($sql);
        if ($dbObj->numRows != 0) {
            while (!$dbObj->EOF) {
                $overlaped[$i]['id'] = $dbObj->GetValue("IdSync");
                $overlaped[$i]['state'] = $dbObj->GetValue("State");
                $overlaped[$i]['file'] = $dbObj->GetValue("RemotePath") . '/' . $dbObj->GetValue("FileName");
                $i++;
                $dbObj->Next();
            }
            return $overlaped;
        }
        return NULL;
    }

    /**
     * Sets a number of ServerFrames to states Due2In or Due2Out
     * 
     * @param array pumpers
     * @param int chunk
     * @param array activeAndEnabledServers
     */
    public function setTasksForPumping($pumpers, $chunk, $activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $serverFrame = new ServerFrame();
        $servers = implode(',', $activeAndEnabledServers);
        foreach ($pumpers as $pumperId) {
            $numPendingTasks = $serverFrame->getUncompletedTasks($pumperId, $activeAndEnabledServers);
            $numTasksForPumping = $chunk - $numPendingTasks;
            if ($numTasksForPumping > 0) {
                $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, Pumpers WHERE RIGHT(ServerFrames.State, 1) = '_'
					AND ServerFrames.PumperId = $pumperId AND Pumpers.IdServer IN ($servers)
					AND ServerFrames.PumperId = Pumpers.PumperId ORDER BY ServerFrames.ErrorLevel ASC,
					ServerFrames.Retry ASC LIMIT $numTasksForPumping";
                $dbObj->Query($sql);
                if ($dbObj->numRows > 0) {
                    $timer = new \Ximdex\Utils\Timer();
                    Logger::debug('Set task for pumping starting');
                    $timer->start();
                    $tasks = array();
                    while (!$dbObj->EOF) {
                        $tasks[] = $dbObj->GetValue("IdSync");
                        $dbObj->Next();
                    }
                    Logger::debug("Setting tasks $numTasksForPumping for pumper $pumperId");
                    if (self::useMPMManager) {
                        
                        // Process All Task with MPMManager
                        $callback = array("/src/Sync/ServerFrameManager", "processTaskForServerFrame");
                        Logger::debug('Starting MPM Manager for Pumper ID: ' . $pumperId . ' -> Task: ' . print_r($tasks, true), true);
                        $mpm = new MPMManager($callback, $tasks, MPMProcess::MPM_PROCESS_OUT_BOOL, 4, 2);
                        $mpm->run();
                    }
                    else {
                        foreach ($tasks as $task) {
                            Logger::debug('Running processTaskForServerFrame with task: ' . $task);
                            $this->processTaskForServerFrame($task);
                        }
                    }
                    $timer->stop();
                    Logger::debug('Set task for pumping ended; time: ' . $timer->display() . ' milliseconds');
                } else {
                    Logger::debug("All tasks pumped for pumper $pumperId");
                }
            } else {
                Logger::debug("Pumper $pumperId full");
            }
        }
    }

    /**
     * Gets the identifier of the Pumper responsible of upload (removes) a ServerFrame
     * 
     * @param int serverFrameId
     * @return int
     */
    public function calcPumper($serverFrameId)
    {
        // At present, serverid is the criteria for pumperid
        $serverFrame = new ServerFrame($serverFrameId);
        if ($serverFrame->get('PumperId')) {
            return $serverFrame->get('PumperId');
        }
        $idServer = $serverFrame->get('IdServer');
        $dbObj = new \Ximdex\Runtime\Db();
        
        // $sql = "SELECT PumperId FROM Pumpers where IdServer = $idServer AND State != '" . Pumper::ENDED . "'";
        $sql = 'SELECT p.PumperId FROM Pumpers p WHERE p.IdServer = ' . $idServer . ' AND p.State != \'' . Pumper::ENDED . '\' ';
        if (MAX_TASKS_PER_PUMPER > 0) {
            $sql .= 'AND (SELECT COUNT(*) FROM ServerFrames sf WHERE sf.PumperId = p.PumperId) < ' . MAX_TASKS_PER_PUMPER . ' ';
        }
        $sql .= 'LIMIT 1';
        $dbObj->Query($sql);
        if ($dbObj->numRows == 0) {
            
            // Insert new pumper in database
            $pumper = new Pumper();
            $pumperId = $pumper->create($idServer);
        } else {
            $pumperId = $dbObj->GetValue('PumperId');
        }
        if ($pumperId > 0) {
            return $pumperId;
        }
        Logger::info('Obtaining pumperId for task ' . $serverFrame->get('IdSync'));
    }

    /**
     * Gets all Pumpers which are currently working
     * 
     * @param array activeAndEnabledServers
     * @return array|null
     */
    public function getPumpersWithTasks($activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $servers = implode(',', $activeAndEnabledServers);
        $query = "SELECT DISTINCT(PumperId) FROM ServerFrames WHERE State IN ('" . ServerFrame::DUE2IN . "', '" . 
            ServerFrame::DUE2OUT . "', '" . ServerFrame::DUE2IN_ . "', '" . ServerFrame::DUE2OUT_ . "', '" . 
            ServerFrame::PUMPED . "') AND IdServer IN ($servers) AND NOT PumperId IS NULL";
        $dbObj->Query($query);
        if ($dbObj->numErr) {
            return NULL;
        }
        $pumpers = array();
        while (!$dbObj->EOF) {
            $pumpers[] = $dbObj->GetValue("PumperId");
            $dbObj->Next();
        }
        return $pumpers;
    }

    /**
     * Gets all ServerFrames associated to a NodeFrame.
     * 
     * @param int nodeFrameId
     * @return array
     */
    public function getByNodeFrame($nodeFrameId)
    {
        $result = array();
        $serverFrame = new ServerFrame();
        $result = $serverFrame->find('IdSync', 'IdNodeFrame = %s', array('IdNodeFrame' => $nodeFrameId), MULTI);
        return $result;
    }

    /**
     *  Sets the ServerFrame ready for upload (remove) to publication Server.
     *  
     * @param int task
     */
    public function processTaskForServerFrame($task)
    {
        $serverFrame = new ServerFrame($task);
        $state = $serverFrame->get('State');
        $newState = substr($state, -strlen($state), strlen($state) - 1);

        // Creates Sync file for pumping and updating state
        if ($newState == ServerFrame::DUE2IN) {
            $fileSize = $serverFrame->createSyncFile($task, $serverFrame->get('cache'));
            if ($fileSize === false) {
                return false;
            }
            if ($fileSize === null) {
                $newState = ServerFrame::REMOVED;
            }
            else {
                $serverFrame->set('FileSize', $fileSize);
            }
        }
        $serverFrame->set('State', $newState);
        $serverFrame->update();
        return true;
    }

    /**
     * Checks whether the Scheduler script is finished.
     * 
     * @return int
     */
    public function isSchedulerEnded()
    {
        $serverFrame = new ServerFrame();
        $notFinalStatus = array_merge($serverFrame->initialStatus, $serverFrame->errorStatus);
        $strStatus = array();
        foreach ($notFinalStatus as $statusString) {
            $strStatus = sprintf("'%s'", $statusString);
        }
        if (is_array($strStatus)) {
            $status = implode(', ', $strStatus);
        } else{
            $status = implode(', ', [$strStatus]);
        }
        $result = $serverFrame->query(sprintf('SELECT IdSync FROM ServerFrames WHERE State IN (%s)', $status));
        return count($result) == 0;
    }
}
