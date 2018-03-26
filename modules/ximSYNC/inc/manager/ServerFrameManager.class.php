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

use Ximdex\Logger;
use Ximdex\Models\Pumper;
use Ximdex\MPM\MPMManager;
use Ximdex\MPM\MPMProcess;

\Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');

/**
 * @brief Handles the life cycle of a ServerFrame.
 *
 *    A ServerFrame is the representation of a ChannelFrame in a Server.
 *    It can have these status:
 *    - Pending: not yet processed
 *    - Due2In / Due2In_: ready to send to Server
 *   - Pumped: the serverFrame have been sended to the Server but it stays hidden, waiting the sending of the rest of ServerFrame of the same Batch
 *   - In: all ServerFrames of the same Batch are in the Server. Publication completed.
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
     * Change the ServerFrame's state.
     * @param int serverFrameId
     * @param string operation
     * @param int nodeId
     * @param int delayed
     * @return bool
     */
    function changeState($serverFrameId, $operation, $nodeId, $delayed = NULL)
    {
        $serverFrame = new ServerFrame($serverFrameId);
        $initialState = $serverFrame->get('State');
        $server = $serverFrame->get('IdServer');
        $channelFrameId = $serverFrame->get('IdChannelFrame');
        $idNodeFrame = $serverFrame->get('IdNodeFrame');
        $chFr = new ChannelFrame($channelFrameId);
        $channel = $chFr->get('ChannelId');
        if (!$operation || !$serverFrameId) {
            $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__,
                __FILE__, __LINE__, "ERROR", 8, _("ERROR Params needed"));
            return false;
        }
        $republishAncestors = false;
        if ($operation == 'Up') {
            if ($initialState == 'Pending') {
                
                // Set down overlaped serverFrames
                $overlapeds = array();
                $overlapeds = $this->getOverlaped($serverFrameId, $server, $nodeId, $channel);
                if (sizeof($overlapeds) > 0) {
                    foreach ($overlapeds as $n => $overlapedData) {
                        $id = $overlapedData['id'];
                        $overlapedFrame = new ServerFrame($id);
                        $overlapedInitialState = $overlapedData['state'];
                        if ($overlapedInitialState == 'In' || $overlapedInitialState == 'Pumped') {
                            if (!is_null($delayed)) {
                                
                                // Para que resucite
                                $overlapedFinalState = 'Delayed';
                            } else {
                                $overlapedName = $overlapedFrame->get('FileName');
                                $overlapedName = $overlapedFrame->get('FileName');
                                $name = $serverFrame->get('FileName');
                                if ($overlapedName != $name) {
                                    
                                    // If name's changed must delete remote file
                                    $overlapedFinalState = 'Due2Out';
                                } else {
                                    $overlapedFinalState = 'Replaced';
                                }
                                $overlapedFrame->deleteSyncFile();
                            }
                        } else {
                            $overlapedFinalState = 'Removed';
                            $overlapedFrame->deleteSyncFile();
                        }
                        $serverFrame->ServerFrameToLog(null, null, null, $id, null, __CLASS__, __FUNCTION__,
                            __FILE__, __LINE__, "INFO", 8, _("Setting $id as overlaped from $overlapedInitialState
								to $overlapedFinalState"), true);
                        $overlapedFrame->set('State', $overlapedFinalState);
                        $overlapedFrame->update();
                    }
                }
                $finalState = 'Due2In_';

                // If is the first publication must republish the ancestors
                $nodeFrame = new NodeFrame();
                if (is_null($nodeFrame->getPrevious($nodeId, $idNodeFrame))) {
                    $republishAncestors = true;
                }
            } else {
                $finalState = 'Due2In_';
                $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__,
                    __FILE__, __LINE__, "INFO", 8, _("Nothing to do with $serverFrameId starter $initialState"), true);
            }
        } elseif ($operation == 'Down') {
            $states = array('Pending', 'Due2In', 'Due2In_');
            if (in_array($initialState, $states)) {
                $finalState = 'Canceled';
                $serverFrame->deleteSyncFile();
            } elseif ($initialState == 'In' || $initialState == 'Pumped') {
                $finalState = 'Replaced';
                $delayedId = $this->getDelayed($serverFrameId, $server, $nodeId, $channel);
                if (!is_null($delayedId)) {
                    $canceledFrame = new ServerFrame($delayedId);
                    $canceledFrame->set('State', 'Due2In_');
                    $canceledFrame->update();
                    $serverFrame->ServerFrameToLog(null, null, null, $id, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__,
                        "INFO", 8, _("Setting frame from Delayed $delayedId to Due2In_"), true);
                } else {
                    $finalState = 'Due2Out_';
                    $serverFrame->deleteSyncFile();
                    $republishAncestors = true;
                }
            } else {
                $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__,
                    __FILE__, __LINE__, "INFO", 8, _("Nothing to do for state $initialState"), true);
            }
        } else {
            $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__,
                __FILE__, __LINE__, "ERROR", 8, _("ERROR: Incorrect operation $operation"));
        }
        $pumperId = $this->calcPumper($serverFrameId);
        $serverFrame->set('State', (isset($finalState)) ? $finalState : $initialState);
        $serverFrame->set('PumperId', $pumperId);
        $result = $serverFrame->update();

        // Republish serverFrames' ancestors

        //TODO ajlucena
        /*
        if ($republishAncestors === true) {

            $depsMngr = new DepsManager();
            $ancestorsLinks = $depsMngr->getByTarget(DepsManager::STRDOC_NODE, $nodeId);
            $ancestorsAssets = $depsMngr->getByTarget(DepsManager::STRDOC_ASSET, $nodeId);

            $ancestorsLinks = !is_array($ancestorsLinks) ? array() : $ancestorsLinks;
            $ancestorsAssets = !is_array($ancestorsAssets) ? array() : $ancestorsAssets;

            $ancestors = array_merge($ancestorsLinks, $ancestorsAssets);

            if (sizeof($ancestors) > 0) {

                foreach ($ancestors as $idAncestor) {
                    $nodeFrame = new NodeFrame();
                    $activeNodeFrame = $nodeFrame->getPublishedId($idAncestor);

                    if (!is_null($activeNodeFrame)) {
                        $publishServerFrames = $this->getByNodeFrame($activeNodeFrame);

                        $batchMng = new BatchManager();
                        foreach ($publishServerFrames as $publishID) {
                            $idA = $publishID[0];
                            $ancestFrame = new ServerFrame($idA);
                            $prevState = $ancestFrame->get('State');
                            $idBatchUp = $ancestFrame->get('IdBatchUp');
                            $ancestFrame->set('State', 'Due2In_');
                            $ancestFrame->update();
                            $batchMng->updateBatchFromRepublishAncestors($idBatchUp, $prevState);
                        }
                    }
                }
            }
        }
        */
        if (!isset($finalState)) {
            $finalState = 'unknown final state';
        }
        if ($result === false) {
            $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "ERROR", 8, _("ERROR Changing frame $serverFrameId to $finalState"));
            return false;
        }
        if ($result) {
            $serverFrame->ServerFrameToLog(null, null, null, $serverFrameId, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__,
                "INFO", 8, _("Setting frame $serverFrameId from $initialState to $finalState"), true);
        }
        return true;
    }

    /**
     * Gets the ServerFrames whose State is Delayed.
     * 
     * @param int frameId
     * @param int server
     * @param int nodeId
     * @param int channel
     * @return int|null
     */
    function getDelayed($frameId, $server, $nodeId, $channel)
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
            $sql .= " AND ChannelFrames.ChannelId is null";
        }
		$sql .= " AND ServerFrames.IdSync != $frameId AND ServerFrames.State = 'Delayed'";
        $dbObj->Query($sql);
        if ($dbObj->numRows != 0) {
            $canceled = $dbObj->GetValue("IdSync");
            return $canceled;
        }
        return NULL;
    }

    /**
     * Gets the ServerFrames of the same Node, Channel and Server, and State in (in,due2in,due2in_,pumped,canceled).
     * @param int frameId
     * @param int server
     * @param int nodeId
     * @param int channel
     * @return int|null
     */
    function getOverlaped($frameId, $server, $nodeId, $channel)
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
				AND (ServerFrames.State = 'In' OR ServerFrames.State = 'Due2In'
					OR ServerFrames.State = 'Due2In_' OR ServerFrames.State = 'Canceled' OR ServerFrames.State = 'Pumped')";
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
     * Sets a number of ServerFrames to states Due2In or Due2Out.
     * @param array pumpers
     * @param int chunk
     * @param array activeAndEnabledServers
     */
    function setTasksForPumping($pumpers, $chunk, $activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $serverFrame = new ServerFrame();
        $servers = implode(',', $activeAndEnabledServers);
        foreach ($pumpers as $pumperId) {
            $numPendingTasks = $serverFrame->getUncompletedTasks($pumperId, $activeAndEnabledServers);
            $numTasksForPumping = $chunk - $numPendingTasks;
            if ($numTasksForPumping > 0) {
                $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, Pumpers WHERE RIGHT(ServerFrames.State,1) = '_'
					AND ServerFrames.PumperId = $pumperId AND Pumpers.IdServer IN ($servers)
					AND ServerFrames.PumperId = Pumpers.PumperId ORDER BY ServerFrames.ErrorLevel ASC,
					ServerFrames.Retry ASC LIMIT $numTasksForPumping";
                $dbObj->Query($sql);
                if ($dbObj->numRows > 0) {
                    $timer = new \Ximdex\Utils\Timer();
                    Logger::info('Set task for pumping starting');
                    $timer->start();
                    $tasks = array();
                    while (!$dbObj->EOF) {
                        $tasks[] = $dbObj->GetValue("IdSync");
                        $dbObj->Next();
                    }
                    $serverFrame->ServerFrameToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                        __LINE__, "INFO", 8, _("Setting tasks $numTasksForPumping for pumper $pumperId"));
                    
                    if (self::useMPMManager) {
                        
                        //Process All Task with MPMManager
                        $callback = array("/modules/ximSYNC/inc/model/ServerFrameManager", "processTaskForServerFrame");
                        Logger::info('Starting MPM Manager for Pumper ID: ' . $pumperId . ' -> Task: ' . print_r($tasks, true), true);
                        $mpm = new MPMManager($callback, $tasks, MPMProcess::MPM_PROCESS_OUT_BOOL, 4, 2);
                        $mpm->run();
                    }
                    else {
                        foreach ($tasks as $task) {
                            Logger::info('Running processTaskForServerFrame with task: ' . $task);
                            $this->processTaskForServerFrame($task);
                        }
                    }
                    $timer->stop();
                    Logger::info('Set task for pumping ended; time: ' . $timer->display() . ' milliseconds', true);
                } else {
                    $serverFrame->ServerFrameToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                        __LINE__, "INFO", 8, _("All tasks pumped for pumper $pumperId"));
                }
            } else {
                $serverFrame->ServerFrameToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                    __LINE__, "INFO", 8, _("Pumper $pumperId full"));
            }
        }
    }

    /**
     * Gets the identifier of the Pumper responsible of upload (removes) a ServerFrame.
     * @param int serverFrameId
     * @return int
     */
    function calcPumper($serverFrameId)
    {
        // At present, serverid is the criteria for pumperid
        $serverFrame = new ServerFrame($serverFrameId);
        $idServer = $serverFrame->get('IdServer');
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT PumperId FROM Pumpers where IdServer = $idServer AND State != 'Ended'";
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
        $serverFrame->ServerFrameToLog($serverFrame->get('IdSync'), null, null, null, null, __CLASS__, __FUNCTION__,
            __FILE__, __LINE__, "INFO", 8, _("Obtaining pumperId for task ") . $serverFrame->get('IdSync') . "");
    }

    /**
     * Gets all Pumpers which are currently working.
     * @param array activeAndEnabledServers
     * @return array|null
     */
    function getPumpersWithTasks($activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $servers = implode(',', $activeAndEnabledServers);
        $query = "SELECT DISTINCT(PumperId) FROM ServerFrames WHERE 
            State IN ('Due2In', 'Due2Out', 'Due2In_', 'Due2Out_', 'Pumped') AND IdServer IN ($servers)";
        $dbObj->Query($query);
        $pumpers = array();
        while (!$dbObj->EOF) {
            $pumpers[] = $dbObj->GetValue("PumperId");
            $dbObj->Next();
        }
        if ($dbObj->numErr) {
            return NULL;
        }
        return $pumpers;
    }

    /**
     * Gets all ServerFrames associated to a NodeFrame.
     * @param int nodeFrameId
     * @return array
     */
    function getByNodeFrame($nodeFrameId)
    {
        $result = array();
        $serverFrame = new ServerFrame();
        $result = $serverFrame->find('IdSync', 'IdNodeFrame = %s', array('IdNodeFrame' => $nodeFrameId), MULTI);
        return $result;
    }

    /**
     *  Sets the ServerFrame ready for upload (remove) to publication Server.
     * @param int task
     */
    function processTaskForServerFrame($task)
    {
        $serverFrame = new ServerFrame($task);
        $state = $serverFrame->get('State');
        $newState = substr($state, -strlen($state), strlen($state) - 1);

        // Creates Sync file for pumping and updating state
        if ($newState == 'Due2In') {
            $fileSize = $serverFrame->createSyncFile($task);
            $serverFrame->set('FileSize', $fileSize);
        }
        $serverFrame->set('State', $newState);
        $serverFrame->update();
    }

    /**
     * Checks whether the Scheduler script is finished.
     * @return int
     */
    function isSchedulerEnded()
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
        $result = $serverFrame->query(sprintf("SELECT IdSync FROM ServerFrames WHERE State IN (%s)", $status));
        return count($result) == 0;
    }

    /**
     * Gets all ServerFrames by cryteria.
     */
    function getFrames($idNodeGenerator = null)
    {
        $frames = array();
        $extraWhereClause = ($idNodeGenerator !== null) ? "AND b.IdNodeGenerator = '" . $idNodeGenerator . "' " : "";
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT n.Name AS NodeName, b.IdPortalVersion, s.IdSync, s.IdServer, s.DateUp, s.DateDown, s.State, s.FileName FROM Batchs b, ServerFrames s, Nodes n WHERE s.IdBatchUp = b.IdBatch AND b.IdNodeGenerator = n.IdNode AND s.State NOT IN ('Replaced', 'Removed') $extraWhereClause ORDER BY b.IdPortalVersion DESC";
        $dbObj->Query($sql);
        while (!$dbObj->EOF) {
            $frame = array();
            $frame["NodeName"] = $dbObj->GetValue("NodeName");
            $frame["IdPortalVersion"] = $dbObj->GetValue("IdPortalVersion");
            $frame["IdSync"] = $dbObj->GetValue("IdSync");
            $frame["IdServer"] = $dbObj->GetValue("IdServer");
            $frame["DateUp"] = $dbObj->GetValue("DateUp");
            $frame["DateDown"] = $dbObj->GetValue("DateDown");
            $frame["State"] = $dbObj->GetValue("State");
            $frame["FileName"] = $dbObj->GetValue("FileName");
            $frames[$frame["IdPortalVersion"]][] = $frame;
            $dbObj->Next();
        }
        return $frames;
    }
}