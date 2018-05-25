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

namespace Ximdex\Sync;

use Batch;
use ChannelFrame;
use NodeFrame;
use NodeFrameManager;
use Ximdex\Models\Server;
use ServerFrame;
use Ximdex\Models\Node;
use Ximdex\Logger;
use Ximdex\Runtime\Session;
use Ximdex\Models\PortalVersions;

if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
    \Ximdex\Modules\Manager::file('/inc/model/Batch.class.php', 'ximSYNC');
    \Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
    \Ximdex\Modules\Manager::file('/inc/manager/NodeFrameManager.class.php', 'ximSYNC');
    \Ximdex\Modules\Manager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
}

class SynchroFacade
{
    /**
     * Return the target server to publicate a specified node in one given channel
     * 
     * @param $idTargetNode
     * @param $idTargetChannel
     * @param $idServer
     * @return NULL|int
     */
    public function getServer($idTargetNode, $idTargetChannel, $idServer)
    {
        $targetNode = new Node(($idTargetNode));
        if (! ($targetNode->get('IdNode') > 0)) {
            Logger::error(_('No correct node received'));
            return NULL;
        }
        $server = new Server($idServer);
        if (! ($server->get('IdServer') > 0)) {
            Logger::error(_('No correct server received'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            
            // Looking for a possible frame for the destiny channel
            $targetFrame = new ServerFrame();
            $frameID = $targetFrame->getCurrent($idTargetNode, $idTargetChannel); // esto es un idSync
            if (! ($frameID > 0)) {
                Logger::warning(_("No target frame available") . " FACADE target node: $idTargetNode target channel: " 
                    . (is_null($idTargetChannel) ? 'NULL' : $idTargetChannel) . " server: $idServer");
                return NULL;
            }
            
            // Calculating physical origin and destiny servers
            $physicalTargetServers = $targetFrame->getCompleteServerList($idTargetNode, $idTargetChannel);
            if (count($physicalTargetServers) == 0) {
                Logger::error(_("No physical target server available"));
                return NULL;
            }
            
            // Gets only enabled servers
            if (in_array($idServer, $physicalTargetServers)) {
                return $idServer;
            }
            return $physicalTargetServers[rand(0, count($physicalTargetServers) - 1)];
        }
        $syncro = new Synchronizer($idTargetNode);
        $idFrame = $syncro->GetCurrentFrame($idTargetChannel);
        if (! ($idFrame > 0)) {
            Logger::error(_("Not target frame available") . " FACADE (2)");
            return NULL;
        }
        $physicalTargetServers = $syncro->GetServerListOnFrame($idFrame, $idTargetChannel);
        if (count($physicalTargetServers) == 0) {
            Logger::info(_("No physical target server available"));
            return NULL;
        }
        if (in_array($idServer, $physicalTargetServers)) {
            return $idServer;
        }
        return $physicalTargetServers[rand(0, count($physicalTargetServers) - 1)];
    }

    /**
     * Delete frames belongs to unexisting physical server
     *
     * @param $physicalServerID
     */
    function removeFromUnexistingServer($physicalServerID)
    {
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $table = 'ServerFrames';
        } else {
            $table = 'Synchronizer';
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "DELETE FROM $table WHERE IdServer = $physicalServerID";
        $dbObj->Execute($sql);
        if ($dbObj->numRows > 0) {
            Logger::info(sprinf(_("Deleting frames in table %s - server %s"), $table, $physicalServerID));
        } else {
            Logger::info(sprinft(_("No deletion in table %s - server %s"), $table, $physicalServerID));
        }
    }

    /**
     * Return pending tasks for a given node
     *
     * @param $nodeID
     */
    function getPendingTasksByNode($nodeID)
    {
        if (is_null($nodeID)) {
            Logger::info("Void node");
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $nodeFramesMng = new NodeFrameManager();
            $pendingTasks = $nodeFramesMng->getPendingNodeFrames($nodeID);
        } else {
            $sync = new Synchronizer();
            $pendingTasks = $sync->getPendingFrames($nodeID);
        }
        return $pendingTasks;
    }

    /**
     * Return if node is published
     *
     * @param $nodeID
     * @return boolean
     */
    function isNodePublished($nodeID)
    {
        if (is_null($nodeID)) {
            Logger::info("Void node");
            return false;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $nodeFrame = new NodeFrame();
            $result = $nodeFrame->getPublishedId($nodeID);
        } else {
            $sync = new Synchronizer($nodeID);
            $result = $sync->IsPublished();
        }
        return (boolean) $result;
    }
    
    /**
     * Delete all tasks by node
     *
     * @param $nodeID
     * @param boolean $unPublish --> don't delete task, set it to Due2Out state
     */
    function deleteAllTasksByNode($nodeID, $unPublish = false)
    {
        if (is_null($nodeID)) {
            Logger::info(_("No existing node with id $nodeID"));
            return NULL;
        }
        if (! is_null($unPublish)) {
            Logger::info(_("Unpublish documents before deleting node"));
        } else {
            Logger::info(_("Delete node and keep documents published"));
        }
        $deleteIDs = self::getAllTaskByNode($nodeID);
        foreach ($deleteIDs as $id) {
            if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
                $nodeFrameMng = new NodeFrameManager();
                $tasks = $nodeFrameMng->getByNode($id);
                if (sizeof($tasks) > 0) {
                    foreach ($tasks as $dataFrames) {
                        $idNodeFrame = $dataFrames[0];
                        $nodeFrameMng->delete($idNodeFrame, $unPublish);
                    }
                }
            } else {
                $sync = new Synchronizer();
                $sync->deleteByColumn($id, 'IdNode', $unPublish);
            }
        }
        return true;
    }

    /**
     * @param $nodeID
     */
    function getAllTaskByNode($nodeID)
    {
        if (is_null($nodeID)) {
            Logger::info(_("No existing node with id $nodeID"));
            return array();
        }
        $node = new Node($nodeID);
        $pendingTasks = array();
        $publishedTasks = array();
        
        // First node will be the root node
        $childList = [
            $nodeID
        ];
        $workFlowSlaves = $node->GetWorkFlowSlaves();
        $workFlowSlaves = count($workFlowSlaves) > 0 ? $workFlowSlaves : array();
        if ($childList) {
            foreach ($childList as $child) {
                $childNode = new Node($child);
                $childList = array_merge($childList, $childNode->TraverseTree(), $workFlowSlaves);
            }
            if (sizeof($childList) > 0) {
                foreach ($childList as $nodeID) {
                    $pendingTasks = array_merge($pendingTasks, self::getPendingTasksByNode($nodeID));
                    if ($result = self::isNodePublished($nodeID)) {
                        $publishedTasks[] = $nodeID;
                    }
                    if ($result == true) {
                        $isPublished = true;
                    }
                }
            }
        }
        $deleteIDs = array_merge($pendingTasks, $publishedTasks);
        $deleteIDs = array_unique($deleteIDs);
        return $deleteIDs;
    }

    /**
     * Gets the State field of the frame
     *
     * @param int idFrame
     * @return int|NULL
     */
    function getFrameState($idFrame)
    {
        if (is_null($idFrame)) {
            Logger::error(_('Void param idFrame'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            
            $serverFrame = new ServerFrame($idFrame);
            $state = $serverFrame->get('State');
        } else {
            
            $sync = new Synchronizer();
            $state = $sync->GetStateOfFrame($idFrame);
        }
        return $state;
    }

    /**
     * Gets the RemotePath field of the frame
     *
     * @param int idFrame
     * @return int|NULL
     */
    function getFramePath($idFrame)
    {
        if (is_null($idFrame)) {
            Logger::error(_('Void param idFrame'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $serverFrame = new ServerFrame($idFrame);
            $path = $serverFrame->get('RemotePath');
        } else {
            $sync = new Synchronizer();
            $path = $sync->GetRemotePathOnFrame($idFrame);
        }
        return $path;
    }

    /**
     * Gets the FileName field of the frame
     *
     * @param int idFrame
     * @return int|NULL
     */
    function getFrameName($idFrame)
    {
        if (is_null($idFrame)) {
            Logger::error(_('Void param idFrame'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $serverFrame = new ServerFrame($idFrame);
            $name = $serverFrame->get('FileName');
        } else {
            $sync = new Synchronizer();
            $name = $sync->GetFileNameOnFrame($idFrame);
        }
        return $name;
    }

    /**
     * Gets the IdServer field of the frame
     *
     * @param int idFrame
     * @return int|NULL
     */
    function getFrameServer($idFrame)
    {
        if (is_null($idFrame)) {
            Logger::error(_('Void param idFrame'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $serverFrame = new ServerFrame($idFrame);
            $server = $serverFrame->get('IdServer');
        } else {
            $sync = new Synchronizer();
            $server = $sync->GetServerOnFrame($idFrame);
        }
        return $server;
    }

    /**
     * Gets the Channel associated to frame
     *
     * @param int idFrame
     * @return int|NULL
     */
    function getFrameChannel($idFrame)
    {
        if (is_null($idFrame)) {
            Logger::error(_('Void param idFrame'));
            return NULL;
        }
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $serverFrame = new ServerFrame($idFrame);
            $channelFrameId = $serverFrame->get('IdChannelFrame');
            $channelFrame = new ChannelFrame($channelFrameId);
            $channel = $channelFrame->get('ChannelId');
        } else {
            $sync = new Synchronizer();
            $channel = $sync->GetChannelOnFrame($idFrame);
        }
        return $channel;
    }

    /**
     * Return last Url
     * 
     * @param $nodeID
     * @param $serverid
     * @param $channel
     * @return NULL|string
     */
    function getLastPublishedNews($nodeID, $serverid, $channel)
    {
        if (is_null($nodeID)) {
            Logger::info(_("Void node"));
            return NULL;
        }
        $channelID = null;
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $nodeFrame = new NodeFrame();
            $id = $nodeFrame->getPublishedId($nodeID);
            $channelFrame = new ChannelFrame();
            $channelFrameId = $channelFrame->getLast($nodeID, $channelID);
            if ($id && $channelFrameId[0]) {
                $serverFrame = new ServerFrame();
                $url = $serverFrame->getUrlLastPublicatedNews($id, $channelFrameId[0], $serverid);
                if ($url != NULL) {
                    $server = new Server($serverid);
                    $url_server = $server->get('Url');
                    if ($url_server)
                        return $url_server . $url;
                }
            }
        } else {
            $sync = new Synchronizer($nodeID);
            $result = $sync->IsPublished();
            if ($result) {
                $url = $sync->getUrlLastPublicatedNews($nodeID, $channel, $serverid);
                if ($url != NULL) {
                    $server = new Server($serverid);
                    $url_server = $server->get('Url');
                    if ($url_server)
                        return $url_server . $url;
                }
            }
        }
        return null;
    }

    /**
     * @param $idNode
     * @return array|number|NULL
     */
    function getGaps($idNode)
    {
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $nodefr = new NodeFrame();
            $gaps = $nodefr->getGaps($idNode);
        } else {
            $synchronizer = new Synchronizer($idNode);
            $lastFrame = $synchronizer->GetLastFrameBulletin();
            $lastTime = $synchronizer->GetDateDownOnFrame($lastFrame);
            if ($lastFrame && ! $lastTime) {
                $gaps = $synchronizer->GetGapsBetweenDates(time(), $synchronizer->GetDateUpOnFrame($lastFrame));
            } else {
                $gaps = $synchronizer->GetGapsBetweenDates(time(), $lastTime);
                if ($lastTime) {
                    $lastGap = array(
                        $lastTime,
                        null,
                        null
                    );
                } else {
                    $lastGap = array(
                        time(),
                        null,
                        null
                    );
                }
                $gaps[] = $lastGap;
            }
        }
        return $gaps;
    }

    /**
     * PushDocInPublishingPool
     *
     * @param int $idNode
     * @param $upDate --> date for publication document (timestamp type)
     * @param $downDate --> date for unpublish the document (timestamp type)
     * @param array $flagsArray
     *            forcePublication:
     *            --> if true --> publish the document although it is in the last version
     *            --> if false --> only publish the document if there is a new mayor version no publish
     *            forceDependencies: --> if true --> publish the dependencies although they are in the last version
     * @param bool $recurrence
     * @return array|bool
     */
    function pushDocInPublishingPool($idNode, $upDate, $downDate = NULL, $flagsArray = NULL, $recurrence = false)
    {
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC')) {
            $syncMngr = new \SyncManager();
            $node = new Node($idNode);
            
            // Default values
            $syncMngr->setFlag('recurrence', false);
            if (! isset($flagsArray['force'])) {
                $syncMngr->setFlag('force', false);
            } else {
                $syncMngr->setFlag('force', $flagsArray['force']);
            }
            if ($flagsArray) {
                foreach ($flagsArray as $key => $value) {
                    if ($key == 'force')
                        continue;
                    $syncMngr->setFlag($key, $value);
                }
            }
            Logger::info("Stablishing PUSH");
            $syncMngr->setFlag('deleteOld', true);
            $result = $syncMngr->pushDocInPublishingPool($idNode, $upDate, $downDate);
            return $result;
        }
        $syncMngr = new SyncManager();
        
        // Default values
        $syncMngr->setFlag('workflow', true);
        $syncMngr->setFlag('recurrence', $recurrence);
        
        // It's needs markend and linked
        if (($flagsArray != null) && (is_array($flagsArray))) {
            foreach ($flagsArray as $key => $value) {
                $syncMngr->setFlag($key, $value);
            }
        }
        $workflow = isset($workflow) ? $workflow : true;
        $forcePublication = isset($flagsArray['force']) && $flagsArray['force'] == 1 ? true : false;
        $syncMngr->setFlag('deleteOld', true);
        $syncMngr->setFlag('workflow', $workflow);
        $node = new Node($idNode);
        $result = array(
            'ok' => array(),
            'notok' => array(),
            'unchanged' => array()
        );
        $nodeList = $node->class->getPublishabledDeps(array(
            'recurrence' => $recurrence
        ));
        foreach ($nodeList as $nodeID) {
            
            // Push document in publishing pool
            $syncMngr->pushDocInPublishingPool($nodeID, $upDate, $downDate, $forcePublication);
            $publishedNode = new Node($nodeID);
            if ($syncMngr->error()) {
                $result['notok']["#" . $nodeID][0][0] = true;
            } else {
                $result['ok']["#" . $nodeID][0][0] = true;
            }
        }
        return $result;
    }

    /**
     * @param $idNode
     * @return boolean
     */
    public static function HasUnlimitedLifeTime($idNode)
    {
        // Both calls are equivalent
        $synchronizer = new Synchronizer($idNode);
        return $synchronizer->HasUnlimitedLifeTime();
    }
    
    public function expire(Node $node, int $down, array $flagsExpiration) : bool
    {
        // Get portal version
        $portal = new PortalVersions();
        $idPortalVersion = $portal->upPortalVersion($node->getServer());
        if (!$idPortalVersion) {
            Logger::error('Cannot create the portal version for server: ' . $node->getServer());
            return false;
        }
        
        // Get the implicated nodes to will be expire
        $syncMngr = new \SyncManager();
        $syncMngr->setFlags($flagsExpiration);
        $nodes2expire = $syncMngr->getPublishableDocs($node, $down, $down);
        $batch = new Batch();
        $serverFrame = new ServerFrame();
        $createBatch = true;
        foreach ($nodes2expire as $id) {
            
            // Create batch for down process per max nodes
            if ($createBatch) {
                $batchId = $batch->create($down, Batch::TYPE_DOWN, $node->GetID(), 0.9, null, $idPortalVersion, Session::get('userID'));
                if (!$batchId) {
                    Logger::error('Cannot create the down batch process');
                    return false;
                }
                $createBatch = false;
                $numFrames = 0;
            }
            
            // Obtain the server frames related to the nodes to expire
            $frames = $serverFrame->getFramesOnDate($id, $down);
            
            // Set this the date to expire in these frames
            foreach ($frames as $frame) {
                $serverFrame->loader($frame['IdSync']);
                $serverFrame->set('DateDown', $down);
                $serverFrame->set('IdBatchDown', $batchId);
                $serverFrame->update();
                $numFrames++;
                if ($numFrames == MAX_NUM_NODES_PER_BATCH) {
                    $batch->set('ServerFramesTotal', $numFrames);
                    $batch->update();
                    $createBatch = true;
                }
            }
            
            // Obtain the frames to be cancelled
            $frames = $serverFrame->getFutureFramesForDate($id, $down);
            
            // Set this the date to expire in these frames
            foreach ($frames as $frame) {
                $serverFrame->loader($frame['IdSync']);
                $serverFrame->set('State', ServerFrame::CANCELLED);
                $serverFrame->update();
            }
        }
        if (isset($batch)) {
            
            // Update the batch with the last generated frames 
            if ($numFrames and $numFrames < MAX_NUM_NODES_PER_BATCH) {
                $batch->set('ServerFramesTotal', $numFrames);
                $batch->update();
            }
            else {
                
                // The batch has no frames, so it will be removed
                $batch->delete();
            }
        }
        return true;
    }
}