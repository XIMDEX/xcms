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
use Ximdex\Models\NodesToPublish;
use Ximdex\Models\PortalFrames;
use Ximdex\Models\Server;
use Ximdex\Runtime\DataFactory;
use Ximdex\Runtime\Session;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\NodeFrame;
use Ximdex\Models\Batch;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\ChannelFrame;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Runtime\Db;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * @brief Handles operations with Batchs.
 *
 * A Batch is a set of documents which have to be published together for obtain the correct graph of the portal.
 * This class includes the methods involved in the overall Batch life cycle of Batchs.
 */
class BatchManager
{
    public $idBatchUp;
    public $idBatchDown;
    public $syncStatObj;
    private $channels;

    /**
     * Public constructor
     */
    public function __construct()
    {
        $this->setFlag('idBatchUp', null);
        $this->setFlag('idBatchDown', null);
    }

    /**
     * Sets the value of any variable
     * 
     * @param string $key
     * @param $value
     */
    public function setFlag($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Creates the Batchs needed for the publication process
     * - Add a batch type Up for all processes
     * - Add a batch type Down only if publication end date is determined
     * 
     * @param int $idNode
     * @param array $docsToPublish
     * @param array $docsToPublishVersion
     * @param array $docsToPublishSubVersion
     * @param $up
     * @param $down
     * @param $physicalServers
     * @param $force
     * @param $userId
     * @return array|bool
     */
    public function publicate($idNode, $docsToPublish, $docsToPublishVersion, $docsToPublishSubVersion, $up, $down, $physicalServers
        , array $force, $userId = null)
    {
        $timer = new \Ximdex\Utils\Timer();
        $timer->start();
        $node = new Node($idNode);
        if (!$node->GetID()) {
            Logger::error('Cannot load the node with ID: ' . $idNode . ' in order to create the publication batch');
            return false;
        }
        $idServer = $node->GetServer();
        Logger::info('Publication starts for ' . $node->GetPath() . " ($idNode)");
        $unchangedDocs = array();
        $docsToUpVersion = array();
        foreach ($docsToPublish as $idDoc) {
            $versionToPublish = $docsToPublishVersion[$idDoc];
            $subversionToPublish = $docsToPublishSubVersion[$idDoc];
            $docNode = new Node($idDoc);
            if (!$docNode->get('IdNode')) {
                Logger::error('Not existing node ' . $idDoc);
                continue;
            }

            // Updating the content of afected by enriching
            if ($node->nodeType->get('IsEnriching') == '1') {
                $content = $docNode->GetContent();
                $docNode->SetContent($content);
            }
            if (!$this->isPublishable($idDoc, $up, $down, $force[$idDoc])) {
                $docsToPublish = array_diff($docsToPublish, array($idDoc));
                $unchangedDocs[$idDoc][0][0] = 0;
                continue;
            }

            // We up version if the current version to publish it is a draft or if the current version is 0.0 and the node is the generator node
            // Or is a image / binary file
            if ($subversionToPublish != 0 || ($subversionToPublish == 0 && $versionToPublish == 0 && ($idDoc == $idNode 
                or $docNode->GetNodeType() == NodeTypeConstants::IMAGE_FILE or $docNode->GetNodeType() == NodeTypeConstants::BINARY_FILE))) {
                $docsToUpVersion[$idDoc] = $idDoc;
            }
        }

        // Get portal version
        $portal = new PortalFrames();
        $idPortalFrame = $portal->upPortalFrameVersion($idNode, $userId);
        if (!$idPortalFrame) {
            Logger::error('Cannot generate a new portal frame version');
            return false;
        }

        // Build batchs
        $docsChunked = array_chunk($docsToPublish, MAX_NUM_NODES_PER_BATCH, true);
        $docsBatch = array();
        $iCount = 1;
        $iTotal = count($docsChunked);
        $noCache = [];
        foreach ($docsChunked as $chunk) {
            Logger::info(sprintf('[Generator %s]: Creating bach %s / %s', $idNode, $iCount, $iTotal));
            foreach ($chunk as $id) {
                if ($force[$id]) {
                    $noCache[$id] = true;
                }
            }
            $partialDocs = $this->buildBatchs($idNode, $up, $chunk, $docsToUpVersion, $docsToPublishVersion, $docsToPublishSubVersion
                , $idServer, $physicalServers, DEFAULT_BATCH_PRIORITY, $down, $iCount, $iTotal, $idPortalFrame, $userId, $noCache);
            $docsBatch = array_merge($docsBatch, $partialDocs);
            $iCount++;

            // Update 'chunk' nodes state to 'processed' (state == 2)
            NodesToPublish::setProcessed($chunk, $up);
        }
        $timer->stop();
        Logger::info('Publication ended; time for publication = ' . $timer->display('s') . ' seconds');
        return array($docsBatch, $unchangedDocs);
    }

    /**
     * Checks whether the Node can be published
     *
     * @param int nodeId
     * @param int up
     * @param int down
     * @param bool forcePublication
     * @return bool
     */
    private function isPublishable($nodeId, $up, $down, $forcePublication = false)
    {
        $node = new Node($nodeId);
        if ($node->nodeType->get('IsPublishable') == 0) {
            Logger::info(sprintf('Node %s belongs to an unpublished nodetype', $nodeId));
            return false;
        }
        if ($node->nodeType->get('IsFolder') == 1) {
            return false;
        }
        if ($forcePublication == true) {
            return true;
        }
        $nodeFrame = new NodeFrame();
        if ($nodeFrame->existsNodeFrame($nodeId, $up, $down)) {
            Logger::debug(sprintf('Node %s already exists in a NodeFrame', $nodeId));
            return false;
        }
        return true;
    }

    private function _upVersion($docs, $generated)
    {
        // Increment version for documents batch finding if there are any otf docs
        if (!is_array($generated)) {
            $generated = array();
        }
        Logger::info(sprintf('Incrementing version for %d documents', count($docs)));
        $totalDocs = count($docs);
        $mod = (int)($totalDocs / 10);
        $i = 0;
        $versions = [];
        foreach ($docs as $value) {
            if (($totalDocs > 50) && ($i % $mod == 0)) {
                Logger::info((int)($i / $totalDocs * 100) . '% completed', 1);
            }
            $n = new Node($value);
            if ($n->nodeType->get('isGenerator')) {
                $generatedNew = $n->class->generator();
                if (!is_array($generatedNew)) $generatedNew = (array)$generatedNew;
                $generated = array_merge($generatedNew, $generated);
            }
            $dataFactory = new DataFactory($value);
            $versions[] = $dataFactory->AddVersion(true);
            $i++;
        }
        return $versions;
    }

    public function buildBatchs($nodeGenerator, $timeUp, $docsToPublish, $docsToUpVersion, $versions, $subversions, $server, $physicalServers
        , $priority, $timeDown = null, $statStart = 0, $statTotal = 0, $idPortalFrame = null, $userId = null, array $noCache = [])
    {
        /*
        If the server is publishing through a channell in which there is not existing documents
        a batch is created without serverFrames, and it will be deleted at the end of buildFrames method
        */
        $relBatchsServers = array();
        foreach ($physicalServers as $serverId) {
            $batch = new Batch();
            $idBatchDown = null;
            if ($timeDown != 0) {
                $idBatchDown = $batch->create($timeDown, Batch::TYPE_DOWN, $nodeGenerator, $priority, $serverId, null, $idPortalFrame, $userId);
                Logger::info('Creating down batch: ' . $timeDown);
                Logger::info(sprintf('[Generator %s]: Creating down batch with id %s', $nodeGenerator, $idBatchDown));
            }
            $batch = new Batch();
            $relBatchsServers[$serverId] = $batch->create($timeUp, Batch::TYPE_UP, $nodeGenerator, $priority, $serverId, $idBatchDown, 
                $idPortalFrame, $userId, 0);
            Logger::info('Creating up batch: ' . $timeUp);
            Logger::info(sprintf('[Generator %s]: Creating up batch with id %s', $nodeGenerator, $relBatchsServers[$serverId]));
        }
        $frames = $this->buildFrames($timeUp, $timeDown, $docsToPublish, $docsToUpVersion, $versions, $subversions, $server
            , $relBatchsServers, $statStart, $statTotal, $nodeGenerator, $idPortalFrame, $noCache);
        foreach ($relBatchsServers as $id) {
            $batch = new Batch($id);
            if (!$batch->get('IdBatch')) {
                continue;
            }
            $batch->set('Playing', 1);
            $batch->update();
        }
        if (isset($batch) and $batch->get('IdBatch')) {
            
            // Update portal frame information
            try {
                PortalFrames::updatePortalFrames($batch);
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
            }
        }
        if (!$frames) {
            $portal = new PortalFrames($idPortalFrame);
            Logger::warning('Deleting portal frame without related batchs');
            $portal->delete();
        }
        return $frames;
    }

    private function buildFrames($up, $down, $docsToPublish, $docsToUpVersion, $versions, $subversions, $serverID, $relBatchsServers
        , $statStart = 0, $statTotal = 0, $nodeGenerator, int $idPortalFrame, array $noCache = [])
    {
        $docsOk = array();
        $docsNotOk = array();
        $nodeServer = new Node($serverID);
        $totalDocs = count($docsToPublish);
        $mod = (int)($totalDocs / 10);
        $j = 0;

        // Creating the frames for each idNode
        $serverFrame = new ServerFrame();
        $servers = [];
        foreach ($docsToPublish as $idNode) {
            if (!isset($versions[$idNode]) or $versions[$idNode] === null) {
                Logger::error('There is not any version for node: ' . $idNode);
                continue;
            }
            if (($totalDocs > 20) && ($j % $mod == 0))
            {
                Logger::info((int)($j / $totalDocs * 100) . '% completed', 1);
            }
            $j++;
            $node = new Node($idNode);
            $versionZero = (0 == $versions[$idNode] && 0 == $subversions[$idNode]);
            if ($versionZero && $node->nodeType->get('IsStructuredDocument') && $idNode != $nodeGenerator)
            {
                Logger::warning(sprintf('Detected 0.0 version for Linked Structured Document: %s to be published', $idNode));
            }
            $dataFactory = new DataFactory($idNode);
            $idVersion = $dataFactory->getVersionId($versions[$idNode], $subversions[$idNode]);
            if (is_null($idVersion))
            {
                Logger::warning(sprintf('There is no version (%s.%s) publishable for the node %s', $versions[$idNode], $subversions[$idNode]
                        , $idNode));
                continue;
            }

            // Boolean for if any serverframe is created for this nodeframe
            $isServerCreated = false;
            $nodeName = $node->GetNodeName();

            // Blocking node
            $userID = \Ximdex\Runtime\Session::get('userID');
            if (is_null($userID)) {
                $userID = 301;
            }
            $node->Block($userID);

            // Upgrade document and caches version to the published one
            if (isset($docsToUpVersion[$idNode]) and $docsToUpVersion[$idNode])
            {
                if ($version = $this->_upVersion(array($docsToUpVersion[$idNode]), null))
                {
                    // Now $idVersion will be upgraded one 
                    $idVersion = $version[0];
                }
            }
            
            // Creating nodeFrames
            $nf = new NodeFrame();
            $nodeFrames = $nf->find('IdNodeFrame', 'NodeId = %s AND VersionId = %s', array($idNode, $idVersion), MONO);
            if (empty($nodeFrames)) {
                $nodeFrameId = $nf->create($idNode, $nodeName, $idVersion, $up, $down);
            } else {
                $nodeFrameId = $nodeFrames[0];
                $nfr = new NodeFrame($nodeFrameId);
                $nfr->set('IsProcessUp', 0);
                $nfr->set('IsProcessDown', 0);
                $nfr->set('Active', 0);
                $nfr->set('TimeUp', $up);
                $nfr->set('TimeDown', $down);
                $nfr->update();
            }
            if (is_null($nodeFrameId)) {
                $node->unBlock();
                Logger::warning(sprintf('A NodeFrame could not be obtained for node %s', $idNode));
                continue;
            }
            $arrayChannels = array();
            if (method_exists($node->class, 'GetChannels')) {
                $arrayChannels = $node->class->GetChannels();
            }
            if (!(count($arrayChannels)) > 0) {
                $arrayChannels[] = 'NULL';
            }
            foreach ($arrayChannels as $channelId) {
                $numFrames = 0;
                $idFrame = null;

                // Creating channelFrames
                if (!isset($this->channels[$channelId])) {
                    $this->channels[$channelId] = new Channel($channelId);
                }
                $channelFrame = new ChannelFrame();
                $channelFrameId = $channelFrame->create($channelId, $idNode);
                if (is_null($channelFrameId)) {
                    $node->unBlock();
                    
                    // Deleting nodeFrame previously created
                    $nodeFrame = new NodeFrame($nodeFrameId);
                    $nodeFrame->delete();
                    Logger::warning(sprintf('A ChannelFrame could not be obtained for node %s and channel %s', $idNode, $channelId));
                    Logger::warning(sprintf('Deleting Nodeframe for node %s', $idNode));
                    continue;
                }
                foreach ($relBatchsServers as $physicalServer => $idBatch) {
                    $idFrame = null;
                    
                    // Load the physical server for the current batch
                    if (!isset($servers[$physicalServer])) {
                        $servers[$physicalServer] = $server = new Server($physicalServer);
                    }
                    else {
                        $server = $servers[$physicalServer];
                    }
                    $serverChannels = $server->getChannels();
                    
                    // Load the inherited channels for the node to be publish
                    $properties = InheritedPropertiesManager::getValues($idNode, true);
                    if (!isset($properties['Channel'])) {
                        continue;
                    }
                    
                    // Check if inherited document channels are in any of server channels
                    $serverHasChannel = false;
                    foreach ($properties['Channel'] as $PropChannelId => $prop) {
                        if (!isset($serverChannels[$PropChannelId])) {
                            
                            // Server channel not for this document
                            continue;
                        }
                        
                        // If this document is common type (channelId = NULL) and server channel is type INDEX, avoid it
                        if ($channelId == 'NULL') {
                            if (!isset($this->channels[$PropChannelId])) {
                                $this->channels[$PropChannelId] = new Channel($PropChannelId);
                            }
                            if ($this->channels[$PropChannelId]->getRenderType() == Channel::RENDERTYPE_INDEX) {
                                continue;
                            }
                        }
                        
                        // Server has this document inherited channel
                        Logger::debug('Inherited property ' . $prop . ' has given channel ' . $PropChannelId);
                        $serverHasChannel = true;
                        break;
                    }
                    if (!$serverHasChannel) {
                        
                        // This server does not support this channel, server frame will not be created
                        continue;
                    }
                    if ($channelId == 'NULL' or $nodeServer->class->HasChannel($physicalServer, $channelId)) {
                        
                        // Creating serverFrames
                        // Generating cache (only if is structured document)
                        $name = $node->GetPublishedNodeName($channelId, true);
                        $path = $node->GetPublishedPath($channelId);
                        $publishLinked = 1;
                        $idFrame = $serverFrame->create($idNode, $physicalServer, $up, $path, $name, $publishLinked, $nodeFrameId
                            , ($channelId === 'NULL') ? null : $channelId , $channelFrameId, $idBatch, $idPortalFrame, $down, 0
                            , isset($noCache[$idNode]) ? false : true);
                        if (is_null($idFrame)) {
                            Logger::error(sprintf('Creation of ServerFrame could not be done: node %s, channel %s, batch %s', $idNode
                                , $channelId, $physicalServer, $idBatch));
                            $docsNotOk[$idNode][$physicalServer][$channelId] = $idFrame;
                        } else {
                            $isServerCreated = true;
                            $numFrames++;
                            $docsOk[$idNode][$physicalServer][$channelId] = $idFrame;
                        }
                    }
                }
                if ($numFrames <= 0) {
                    Logger::error(sprintf('Creation of ServerFrame could not be done: node %s, channel %s', $idNode, $channelId));
                    Logger::warning(sprintf('ChannelFrame %s will be removed', $channelFrameId));
                    
                    // Deleting the channelFrame previosly created
                    $channelFrame = new ChannelFrame($channelFrameId);
                    $channelFrame->delete();
                }
            }
            if (!$isServerCreated) {
                Logger::error(sprintf('Creation of ServerFrame could not be done: node %s', $idNode));
                Logger::warning(sprintf('NodeFrame %s will be eliminated', $nodeFrameId));
                
                // Deleting nodeFrame previously created
                $nodeFrame = new NodeFrame($nodeFrameId);
                $nodeFrame->delete();
            }
            
            // Unblocking node
            $node->unBlock();
        }

        // Updating num serverFrames in Batchs
        $allBatchs = array_values($relBatchsServers);
        $tt = implode(',', $allBatchs);
        $result = $serverFrame->find('IdBatchUp, count(IdSync)', "IdBatchUp in ($tt) group by IdBatchUp", null, MULTI, false);
        Logger::info(sprintf('The number of frames in %s batchs will be updated', count($result)));
        if (count($result) > 0) {
            $ss = [];
            foreach ($result as $dataFrames) {
                $id = $dataFrames[0];
                $ss[] = $dataFrames[0];
                $numFrames = $dataFrames[1];
                Logger::info(sprintf('Batch %s uploaded, total frames %s', $id, $numFrames));
                $batch = new Batch($id);
                $batch->set('ServerFramesTotal', $numFrames);
                $batch->set('ServerFramesPending', $numFrames);
                $batch->update();
                $idBatchDown = $batch->get('IdBatchDown');
                if ($idBatchDown > 0) {
                    Logger::info(sprintf('Batch %s downloaded, total frames %s', $idBatchDown, $numFrames));
                    $batchDown = new Batch($idBatchDown);
                    $batchDown->set('ServerFramesTotal', $numFrames);
                    $batchDown->set('ServerFramesPending', $numFrames);
                    $batchDown->update();
                }
            }
            $voidBatchs = array_diff($allBatchs, $ss);
        } else {
            $voidBatchs = $allBatchs;
        }

        // Batchs without serverFrames will be deleted
        if (sizeof($voidBatchs) > 0) {
            foreach ($voidBatchs as $idBatch) {
                Logger::info(sprintf('Batchs %s will be removed because they are empty', $idBatch));
                $batch = new Batch($idBatch);
                $batch->delete();
                $idBatchDown = $batch->get('IdBatchDown');
                if ($idBatchDown > 0) {
                    $batchDown = new Batch($idBatchDown);
                    $batchDown->delete();
                }
            }
        }
        return array('ok' => $docsOk, 'notok' => $docsNotOk);
    }

    /**
     * Gets the value of any variable
     * 
     * @param string key
     */
    public function getFlag($key)
    {
        return $this->$key;
    }

    public function checkFramesIntegrity()
    {
        // NOTE: See setBatchsActiveOrEnded and getBatchToProcess
        // Ensure that batchs have frames or getBatchToProcess will return the same batch over and over
        $sql = "update Batchs set State = '" . Batch::NOFRAMES . "' ";
        $sql .= 'where idbatch not in (select distinct IdBatchUp from ServerFrames) and idbatch not in (select distinct IdBatchDown ';
        $sql .= "from ServerFrames) and Batchs.State IN ('" . Batch::INTIME . "', '" . Batch::CLOSING . "')";
        $db = new \Ximdex\Runtime\Db();
        if ($db->execute($sql) === false) {
        	return false;
        }
        if ($db->numRows > 0) {
            Logger::warning(sprintf('Found %s Batchs without Frames, were marked as NoFrames', $db->numRows));
        }
        
        // Remove portal frames without batchs and created time more than 10 minutes
        try {
            $voidPortalFrames = PortalFrames::getVoidPortalFrames(600);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
        }
        foreach ($voidPortalFrames as $idPortalFrame) {
            $portalFrame = new PortalFrames($idPortalFrame);
            $portalFrame->delete();
        }
    }

    /**
     * Starts (Ends) the activity of Batchs
     * 
     * @param int testTime
     * @return bool
     */
    public function setBatchsActiveOrEnded($testTime = null, array $servers = null) : bool
    {
        if (! $servers) {
            
            // Get current active servers for pumping
            $servers = ServerNode::getServersForPumping();
        }
        if ($servers === false) {
            return false;
        }
        if (! $servers) {
            return [];
        }
        $dbObj = new Db();
        
        // Updating batchs type UP
        $sql = "SELECT ServerFrames.IdBatchUp, 
            SUM(IF (ServerFrames.ErrorLevel = " . ServerFrame::ERROR_LEVEL_HARD . ", 1, 0)) AS FatalErrors, 
            SUM(IF (ServerFrames.ErrorLevel = " . ServerFrame::ERROR_LEVEL_SOFT . ", 1, 0)) AS TemporalErrors,
			SUM(IF (ServerFrames.State IN ('" . implode('\', \'', ServerFrame::SUCCESS_STATUS) . "'), 1, 0)) AS Success, 
			SUM(IF (ServerFrames.State IN ('" . ServerFrame::PUMPED . "'), 1, 0)) AS Pumpeds,
			COUNT(ServerFrames.IdSync) AS Total,
            SUM(IF (ServerFrames.State NOT IN ('" . ServerFrame::PENDING . "', '" . implode('\', \'', ServerFrame::FINAL_STATUS) 
            . "'), 1, 0)) AS Active, 
            SUM(IF (ServerFrames.State IN ('" . ServerFrame::PENDING . "'), 1, 0)) AS Pending 
            FROM ServerFrames, Batchs WHERE Batchs.Type = '" . Batch::TYPE_UP . "' 
            AND Batchs.State IN ('" . Batch::INTIME . "', '" . Batch::CLOSING . "') AND Batchs.IdBatch = ServerFrames.IdBatchUp
            AND Batchs.ServerId IN (" . implode(',', $servers) . ") 
            GROUP BY ServerFrames.IdBatchUp 
            HAVING (FatalErrors + Success + Pumpeds + Active) > 0 
            ORDER BY ServerFrames.IdBatchUp";
            // HAVING Total = Errors + Success + Pumpeds";
        if ($dbObj->Query($sql) === false) {
        	return false;
        }
        while (!$dbObj->EOF) {
            $idBatch = $dbObj->GetValue('IdBatchUp');
            $fatalErrors = (int) $dbObj->GetValue('FatalErrors');
            $temporalErrors = (int) $dbObj->GetValue('TemporalErrors');
            $success = (int) $dbObj->GetValue('Success');
            $pumpeds = (int) $dbObj->GetValue('Pumpeds');
            $totals = (int) $dbObj->GetValue('Total');
            $active = (int) $dbObj->GetValue('Active');
            $pending = (int) $dbObj->GetValue('Pending');
            $batch = new Batch($idBatch);
            $batch->set('ServerFramesSuccess', $success);
            $batch->set('ServerFramesFatalError', $fatalErrors);
            $batch->set('ServerFramesTemporalError', $temporalErrors);
            $batch->set('ServerFramesActive', $active);
            $batch->set('ServerFramesPending', $pending);
            if ($totals == $fatalErrors + $success + $pumpeds) {
                if ($pumpeds > 0) {
                    
                    // Do not change to CLOSING state if this is the actual one in the batch
                    if ($batch->get('State') != Batch::CLOSING) {
                        $batch->set('State', Batch::CLOSING);
                        Logger::info(sprintf("Setting 'Closing' state batch %d UP", $idBatch));
                    }
                } else {
                    $batch->set('State', Batch::ENDED);
                    Logger::info('Ending up batch with id ' . $idBatch);
                }
            }
            $batch->update();
            if (! BatchManager::setCyclesAndPriority($idBatch)) {
                return false;
            }
            try {
                PortalFrames::updatePortalFrames($batch);
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
            }
            $dbObj->Next();
        }

        // Updating batchs type DOWN
        $batch = new Batch();
        $batchsDown = $batch->find('IdBatch', "Type = '" . Batch::TYPE_DOWN . "' AND State = '" . Batch::INTIME . "'", null, MONO);
        if (sizeof($batchsDown) > 0) {
            foreach ($batchsDown as $idBatch) {
                
                // Search the batch type Down without type Up
                $sql = "SELECT SUM(IF (ErrorLevel = " .ServerFrame::ERROR_LEVEL_HARD . ", 1, 0)) AS FatalErrors, 
                    SUM(IF (ErrorLevel = " .ServerFrame::ERROR_LEVEL_SOFT . ", 1, 0)) AS TemporalErrors, 
					SUM(IF (State IN ('" . implode('\', \'', ServerFrame::SUCCESS_STATUS) . "'), 1, 0)) AS Success, 
					COUNT(IdSync) AS Total, SUM(IF (State IN ('" . ServerFrame::PENDING . "'), 1, 0)) AS Pending,
                    SUM(IF (State NOT IN ('" . ServerFrame::PENDING . "', '" . implode('\', \'', ServerFrame::FINAL_STATUS) . "'), 1, 0)) AS Active
                    FROM ServerFrames WHERE IdBatchDown = $idBatch AND IdServer IN (" . implode(', ', $servers) . ')';
                if ($dbObj->Query($sql) === false) {
                	return false;
                }
                if ($dbObj->GetValue('Total') == 0) {
                    
                    // Search the batchs type Down with an associated batch type Up
                    $sql = "SELECT SUM(IF(ServerFrames.ErrorLevel = " . ServerFrame::ERROR_LEVEL_HARD . ", 1, 0)) AS FatalErrors, 
                        SUM(IF (ServerFrames.ErrorLevel = " .ServerFrame::ERROR_LEVEL_SOFT . ", 1, 0)) AS TemporalErrors, 
		      			SUM(IF (ServerFrames.State IN ('" . implode('\', \'', ServerFrame::SUCCESS_STATUS) . "'), 1, 0)) AS Success, 
				    	COUNT(ServerFrames.IdSync) AS Total,
                        SUM(IF (ServerFrames.State NOT IN ('" . ServerFrame::PENDING . "', '" . implode('\', \'', ServerFrame::FINAL_STATUS)
                            . "'), 1, 0)) AS Active,
                        SUM(IF (ServerFrames.State IN ('" . ServerFrame::PENDING . "'), 1, 0)) AS Pending
                        FROM ServerFrames, Batchs WHERE ServerFrames.IdBatchUp = Batchs.IdBatch AND Batchs.IdBatchDown = $idBatch 
                        AND Batchs.ServerId IN (" . implode(',', $servers) . ')';
                    if ($dbObj->Query($sql) === false) {
                        return false;
                    }
                }
                if ($dbObj->GetValue('Total') > 0) {
                    
                    // Update the batch with the current server frame states
                    $pending = (int) $dbObj->GetValue('Pending');
                    $active = (int) $dbObj->GetValue('Active');
                    $fatalErrors = (int) $dbObj->GetValue('FatalErrors');
                    $temporalErrors = (int) $dbObj->GetValue('TemporalErrors');
                    $success = (int) $dbObj->GetValue('Success');
                    $totals = (int) $dbObj->GetValue('Total');
                    $batch = new Batch($idBatch);
                    $batch->set('ServerFramesPending', $pending);
                    $batch->set('ServerFramesActive', $active);
                    $batch->set('ServerFramesSuccess', $success);
                    $batch->set('ServerFramesFatalError', $fatalErrors);
                    $batch->set('ServerFramesTemporalError', $temporalErrors);
                    if ($totals == $fatalErrors + $success) {
                        $batch->set('State', Batch::ENDED);
                        Logger::info('Ending batch type Down with ID ' . $idBatch);
                    }
                    $batch->update();
                    if (! BatchManager::setCyclesAndPriority($idBatch)) {
                        return false;
                    }
                    try {
                        PortalFrames::updatePortalFrames($batch);
                    } catch (\Exception $e) {
                        Logger::error($e->getMessage());
                    }
                } else {
                    Logger::warning('No server frames related with batch ' . $idBatch);
                }
            }
        }

        // Batchs to start
        if (!$testTime) {
            $now = time();
        } else {
            $now = $testTime;
        }
        $query = "SELECT IdBatch FROM Batchs WHERE Playing = 1 AND State = '" . Batch::WAITING 
            . "' AND TimeOn < $now AND ServerId IN (" . implode(',', $servers) . ')';
        if ($dbObj->Query($query) === false) {
        	return false;
        }
        while (!$dbObj->EOF) {
            $batch = new Batch($dbObj->GetValue('IdBatch'));
            Logger::info('Starting batch ' . $dbObj->GetValue('IdBatch'));
            $batch->set('State', Batch::INTIME);
            $batch->update();
            try {
                PortalFrames::updatePortalFrames($batch);
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
            }
            $dbObj->Next();
        }
        return true;
    }
    
    /**
     * Gets the Batch that must be processed
     */
    public function getBatchToProcess()
    {
        $serversEnabled = ServerNode::getServersForPumping();
        if ($serversEnabled === false) {
            return false;
        }
        if (! $serversEnabled) {
            return [];
        }
        $dbObj = new Db();
        $sql = 'SELECT IdBatch, Type, IdNodeGenerator, MajorCycle, MinorCycle, ServerFramesTotal FROM Batchs' 
            . ' WHERE Playing = 1 AND State = \'' . Batch::INTIME . '\' AND ServerFramesTotal > 0';
        if ($serversEnabled) {
            $sql .= ' AND ServerId IN (' . implode(', ', $serversEnabled) . ')';
        }
        $sql .= ' ORDER BY Priority DESC, MajorCycle, MinorCycle, Type = \'' . Batch::TYPE_DOWN . '\' DESC, IdBatch LIMIT 1';
        if ($dbObj->Query($sql) === false) {
        	return false;
        }
        if (!$dbObj->numRows) {
            return array();
        }
        $list = array();
        $list['id'] = $dbObj->GetValue('IdBatch');
        $list['type'] = $dbObj->GetValue('Type');
        $list['nodegenerator'] = $dbObj->GetValue('IdNodeGenerator');
        $list['majorcycle'] = $dbObj->GetValue('MajorCycle');
        $list['minorcycle'] = $dbObj->GetValue('MinorCycle');
        $list['totalserverframes'] = $dbObj->GetValue('ServerFramesTotal');
        return $list;
    }

    /**
     * Sets the number of Scheduler-Cycles consumed by the Batch and updates its priority
     * 
     * @param int idBatch
     * @return bool
     */
    public static function setCyclesAndPriority(int $idBatch) : bool
    {
        if (is_null($idBatch)) {
            Logger::error('Calling to set cycles and priority with no batch ID');
            return false;
        }
        $batch = new Batch($idBatch);
        /*
        $allFrames = (int) $batch->get('ServerFramesTotal');
        if ($allFrames == 0) {
            Logger::info('Batch without ServerFrames');
            $batch->set('Playing', 0);
            $batch->update();
            return false;
        }
        */
        $majorCycle = $batch->get('MajorCycle');
        $minorCycle = $batch->get('MinorCycle');
        /*
        // Unplaying batchs that exceed max num cycles
        if ($majorCycle > MAX_NUM_CICLOS_BATCH) {
            $batch->set('Playing', 0);
            $batch->update();
            Logger::info('Unplaying batch ' . $idBatch);
            return true;
        }
        */
        $cycles = $batch->calcCycles($majorCycle, $minorCycle);
        if (is_null($cycles)) {
            Logger::error('ERROR Calc cycles');
            return false;
        }
        $batch->set('MajorCycle', $cycles[0]);
        $batch->set('MinorCycle', $cycles[1]);
        
        // Calculate batch priority
        $sucessFrames = (int) $batch->get('ServerFramesSuccess');
        $fatalErrorFrames = (int) $batch->get('ServerFramesFatalError');
        $temporalErrorFrames = (int) $batch->get('ServerFramesTemporalError');
        $processedFrames = $sucessFrames + $fatalErrorFrames + $temporalErrorFrames;
        if ($processedFrames) {
            $priority = round($sucessFrames / $processedFrames, 2);
            Logger::info('Set priority to ' . $priority . ' for batch ' . $idBatch);
            $batch->set('Priority', $priority);
        }
        if ($batch->update() === false) {
            return false;
        }
        return true;
    }

    /**
     * Sets the field Playing for all Batchs
     * 
     * @param int playingValue
     */
    public function setAllBatchsPlayingOrUnplaying($playingValue)
    {
        $dbObj = new Db();
        $sql = "UPDATE Batchs set Playing = '$playingValue'";
        $dbObj->Execute($sql);
        if ($dbObj->numRows > 0) {
            Logger::info('Setting batchs to ' . ($playingValue == 1) ? 'playing' : 'unplaying');
        }
    }

    /**
     * Creates a Batch for remove documents of the publication server (Batch type Down)
     * 
     * @param int idBatchUp
     * @param int nodeId
     * @param int serverFramesTotal
     * @return bool
     */
    public function buildBatchsFromDeleteNode($idBatchUp, $nodeId, $serverFramesTotal, $userId = null)
    {
        $batch = new Batch();
        $batchDownArray = $batch->getDownBatch($idBatchUp);
        if (isset($batchDownArray) && count($batchDownArray) > 0) {

            // Updating Batch Type Down (if exists) State to Waiting
            $batchDown = new Batch($batchDownArray['IdBatch']);
            $batchDown->set('State', Batch::WAITING);
            $batchDown->set('ServerFramesTotal', $serverFramesTotal);
            $batchDown->set('ServerFramesPending', $serverFramesTotal);
            $batchDown->update();
        } else {

            // Gets portal version
            $portal = new PortalFrames();
            $idPortalFrame = $portal->upPortalFrameVersion($nodeId, Session::get('userID'), PortalFrames::TYPE_DOWN);
            if (!$idPortalFrame) {
                Logger::error('Unable to increase the portal version with ID: ' . $nodeId);
                return false;
            }

            // Creating Batch Type Down if not exist one
            $batchDown = new Batch();
            $idBatchDown = $batchDown->create(time(), Batch::TYPE_DOWN, $nodeId, DEFAULT_BATCH_PRIORITY, null, $idPortalFrame, $userId);

            // Updating Serverframes info
            $batchDown = new Batch($idBatchDown);
            $batchDown->set('ServerFramesTotal', $serverFramesTotal);
            $batchDown->set('ServerFramesPending', $serverFramesTotal);
            $batchDown->set('IdPortalFrame', $idPortalFrame);
            $batchDown->update();
        }
        return true;
    }
}
