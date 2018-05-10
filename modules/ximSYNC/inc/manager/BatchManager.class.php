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
use Ximdex\Models\PortalVersions;
use Ximdex\Models\Server;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\NodeTypes\NodeTypeConstants;

\Ximdex\Modules\Manager::file('/inc/model/Batch.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/NodeFrame.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/NodesToPublish.class.php', 'ximSYNC');

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

    /**
     * Public constructor
     */
    function __construct()
    {
        $this->setFlag('idBatchUp', NULL);
        $this->setFlag('idBatchDown', NULL);
    }

    /**
     *  Sets the value of any variable.
     * @param $key string
     * @param $value
     */
    function setFlag($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Creates the Batchs needed for the publication process.
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
     * @return array[]|number[]|bool
     */
    function publicate($idNode, $docsToPublish, $docsToPublishVersion, $docsToPublishSubVersion, $up, $down, $physicalServers, array $force
        , $userId = null)
    {
        $timer = new \Ximdex\Utils\Timer();
        $timer->start();
        $node = new Node($idNode);
        if (!$node->GetID())
        {
            Logger::error('Cannot load the node with ID: ' . $idNode . ' in order to create the publication batch');
            return false;
        }
        $idServer = $node->GetServer();
        Logger::info("Publication starts for " . $node->GetPath() . " ($idNode)");
        $ancestors = array();
        $unchangedDocs = array();
        $docsToUpVersion = array();
        foreach ($docsToPublish as $idDoc)
        {
            $versionToPublish = $docsToPublishVersion[$idDoc];
            $subversionToPublish = $docsToPublishSubVersion[$idDoc];
            $docNode = new Node($idDoc);
            if (!$docNode->get('IdNode'))
            {
                Logger::error("Not existing node " . " $idDoc");
                continue;
            }

            // Updating the content of afected by enriching
            if ($node->nodeType->get('IsEnriching') == '1')
            {
                $content = $docNode->GetContent();
                $docNode->SetContent($content);
            }
            if (!$this->isPublishable($idDoc, $up, $down, $force[$idDoc]))
            {
                $docsToPublish = array_diff($docsToPublish, array($idDoc));
                $unchangedDocs[$idDoc][0][0] = 0;
                continue;
            }

            // We up version if tha current version to publish it is a draft or if the current version is 0.0 and the node is the generator node.
            // Or is a image / binary file
            if ($subversionToPublish != 0 || ($subversionToPublish == 0 && $versionToPublish == 0 && ($idDoc == $idNode or 
                $docNode->GetNodeType() == NodeTypeConstants::IMAGE_FILE or $docNode->GetNodeType() == NodeTypeConstants::BINARY_FILE)))
            {
                $docsToUpVersion[$idDoc] = $idDoc;
            }
        }

        // Get portal version
        $portal = new PortalVersions();
        $idPortalVersion = $portal->upPortalVersion($idServer);

        // Build batchs
        $docsChunked = array_chunk($docsToPublish, MAX_NUM_NODES_PER_BATCH, true);
        $docsBatch = array();
        $iCount = 1;
        $iTotal = count($docsChunked);
        $noCache = [];
        foreach ($docsChunked as $chunk)
        {
            Logger::info(sprintf("[Generator %s]: Creating bach %s / %s", $idNode, $iCount, $iTotal));
            foreach ($chunk as $id) {
                if ($force[$id]) {
                    $noCache[$id] = true;
                }
            }
            $partialDocs = $this->buildBatchs($idNode, $up, $chunk, $docsToUpVersion, $docsToPublishVersion, $docsToPublishSubVersion, $idServer
                , $physicalServers, 0.8, $down, $iCount, $iTotal, $idPortalVersion, $userId, $noCache);
            $docsBatch = array_merge($docsBatch, $partialDocs);
            $iCount++;

            // Update 'chunk' nodes state to 'processed' (state == 2)
            NodesToPublish::setProcessed($chunk, $up);
        }
        $timer->stop();
        Logger::info("Publication ended; time for publication " . " = " . $timer->display('s') . " seconds");
        return array($docsBatch, $unchangedDocs);
    }

    /**
     * Checks whether the Node can be published.
     *
     * @param int nodeId
     * @param int up
     * @param int down
     * @param bool forcePublication
     * @return bool
     */
    function isPublishable($nodeId, $up, $down, $forcePublication = false)
    {
        $node = new Node($nodeId);
        if ($node->nodeType->get('IsPublishable') == 0)
        {
            Logger::info(sprintf("Node %s belongs to an unpublished nodetype", $nodeId));
            return false;
        }
        if ($node->nodeType->get('IsFolder') == 1)
        {
            return false;
        }
        if ($forcePublication == true)
        {
            return true;
        }
        $nodeFrame = new NodeFrame();
        if ($nodeFrame->existsNodeFrame($nodeId, $up, $down))
        {
            Logger::info(sprintf("Node %s already exists in a NodeFrame", $nodeId));
            return false;
        }
        return true;
    }

    function _upVersion($docs, $generated)
    {
        // Increment version for documents batch finding if there are any otf docs
        if (!is_array($generated)) {
            $generated = array();
        }
        Logger::info(sprintf("Incrementing version for %d documents", count($docs)));
        $totalDocs = count($docs);
        $mod = (int)($totalDocs / 10);
        $i = 0;
        $versions = [];
        foreach ($docs as $value) {
            if (($totalDocs > 50) && ($i % $mod == 0)) {
                Logger::info((int)($i / $totalDocs * 100) . "% " . "completed", 1);
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

    function buildBatchs($nodeGenerator, $timeUp, $docsToPublish, $docsToUpVersion, $versions, $subversions, $server, $physicalServers, $priority
        , $timeDown = null, $statStart = 0, $statTotal = 0, $idPortalVersion = 0, $userId = null, array $noCache = [])
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
                $idBatchDown = $batch->create($timeDown, 'Down', $nodeGenerator, 1, null, $idPortalVersion, $userId);
                Logger::info('Creating down batch: ' . $timeDown);
                Logger::info(sprintf("[Generator %s]: Creating down batch with id %s", $nodeGenerator, $idBatchDown));
            }
            $batch = new Batch();
            $relBatchsServers[$serverId] = $batch->create($timeUp, 'Up', $nodeGenerator, $priority, $idBatchDown, $idPortalVersion, $userId);
            Logger::info('Creating up batch: ' . $timeUp);
            Logger::info(sprintf("[Generator %s]: Creating up batch with id %s", $nodeGenerator, $relBatchsServers[$serverId]));
        }
        $frames = $this->buildFrames($timeUp, $timeDown, $docsToPublish, $docsToUpVersion, $versions, $subversions, $server, $relBatchsServers
            , $statStart, $statTotal, $nodeGenerator, $noCache);
        return $frames;
    }

    function buildFrames($up, $down, $docsToPublish, $docsToUpVersion, $versions, $subversions, $serverID, $relBatchsServers, $statStart = 0
        , $statTotal = 0, $nodeGenerator, array $noCache = [])
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
                Logger::info((int)($j / $totalDocs * 100) . "% " . "completed", 1);
            }
            $j++;
            $node = new Node($idNode);
            $versionZero = (0 == $versions[$idNode] && 0 == $subversions[$idNode]);
            if ($versionZero && $node->nodeType->get('IsStructuredDocument') && $idNode != $nodeGenerator)
            {
                Logger::warning(sprintf("Detected 0.0 version for Linked Structured Document: %s to be published", $idNode));
            }
            $dataFactory = new DataFactory($idNode);
            $idVersion = $dataFactory->getVersionId($versions[$idNode], $subversions[$idNode]);
            if (is_null($idVersion))
            {
                $batch = new Batch();
                $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                    __LINE__, "INFO", 8, _("No version for node") . " $idNode");
                Logger::warning(sprintf("There is no version (%s.%s) publishable for the node %s", $versions[$idNode], $subversions[$idNode]
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
                if ($version = $this->_upVersion(array($docsToUpVersion[$idNode]), NULL))
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
                $nfr->update();
            }
            if (is_null($nodeFrameId)) {
                $node->unBlock();
                Logger::warning(sprintf("A NodeFrame could not be obtained for node %s", $idNode));
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
                $idFrame = NULL;
                $framesBatch = array();

                // Creating channelFrames
                $channel = new Channel($channelId);
                $channelFrame = new ChannelFrame();
                $channelFrameId = $channelFrame->create($channelId, $idNode);
                if (is_null($channelFrameId)) {
                    $node->unBlock();
                    
                    // Deleting nodeFrame previously created
                    $nodeFrame = new NodeFrame($nodeFrameId);
                    $nodeFrame->delete();
                    Logger::warning(sprintf("A ChannelFrame could not be obtained for node %s and channel %s", $idNode, $channelId));
                    Logger::warning(sprintf("Deleting Nodeframe for node %s", $idNode));
                    continue;
                }
                foreach ($relBatchsServers as $physicalServer => $idBatch) {
                    $idFrame = NULL;
                    
                    // Load the physical server for the current batch
                    if (!isset($servers[$physicalServer])) {
                        $servers[$physicalServer] = $server = new Server($physicalServer);
                    }
                    else {
                        $server = $servers[$physicalServer];
                    }
                    $serverChannels = $server->getChannels();
                    
                    // Load the channels for the node to be publish
                    $properties = InheritedPropertiesManager::getValues($idNode, true);
                    if (!isset($properties['Channel'])) {
                        continue;
                    }
                    $ok = false;
                    foreach ($properties['Channel'] as $key => $prop) {
                        if (isset($serverChannels[$key])) {
                            $ok = true;
                            break;
                        }
                    }
                    if (!$ok) {
                        continue;
                    }
                    $generatedNodes = array();
                    if ($nodeServer->class->HasChannel($physicalServer, $channelId) || $channelId == 'NULL') {
                        
                        // Creating serverFrames
                        // Generating cache (only if is structured document)
                        $name = $node->GetPublishedNodeName($channelId, true);
                        $path = $node->GetPublishedPath($channelId);
                        $publishLinked = 1;
                        $idFrame = $serverFrame->create($idNode, $physicalServer, $up, $path, $name, $publishLinked, $nodeFrameId
                            , $channelFrameId, $idBatch, $down, 0, isset($noCache[$idNode]) ? false : true);
                    }
                    if (is_null($idFrame)) {
                        Logger::warning(sprintf("Creation of ServerFrame could not be done: node %s, channel %s, batch %s", $idNode
                            , $channelId, $physicalServer, $idBatch));
                        $docsNotOk[$idNode][$physicalServer][$channelId] = $idFrame;
                    } else {
                        $isServerCreated = true;
                        $numFrames++;
                        $docsOk[$idNode][$physicalServer][$channelId] = $idFrame;
                    }
                }
                if ($numFrames <= 0) {
                    Logger::warning(sprintf("Creation of ServerFrame could not be done: node %s, channel %s", $idNode, $channelId));
                    Logger::warning(sprintf("ChannelFrame %s will be removed", $channelFrameId));
                    
                    // Deleting the channelFrame previosly created
                    $channelFrame = new ChannelFrame($channelFrameId);
                    $channelFrame->delete();
                }
            }
            if (!$isServerCreated) {
                Logger::warning(sprintf("Creation of ServerFrame could not be done: node %s", $idNode));
                Logger::warning(sprintf("NodeFrame %s will be eliminated", $nodeFrameId));
                
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
        $result = array();
        $serverFrame = new ServerFrame();
        $result = $serverFrame->find('IdBatchUp, count(IdSync)', "IdBatchUp in ($tt) group by IdBatchUp", NULL, MULTI, false);
        Logger::info(sprintf("The number of frames in %s batchs will be updated", count($result)));
        if (count($result) > 0) {
            foreach ($result as $dataFrames) {
                $id = $dataFrames[0];
                $ss[] = $dataFrames[0];
                $numFrames = $dataFrames[1];
                Logger::info(sprintf("Batch %s uploaded" . ", " . "total frames %s", $id, $numFrames));
                $batch = new Batch($id);
                $batch->set('ServerFramesTotal', $numFrames);
                $batch->update();
                $idBatchDown = $batch->get('IdBatchDown');
                if ($idBatchDown > 0) {
                    Logger::info(sprintf("Batch %s downloaded" . ", " . "total frames %s", $idBatchDown, $numFrames));
                    $batchDown = new Batch($idBatchDown);
                    $batchDown->set('ServerFramesTotal', $numFrames);
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
                Logger::info(sprintf("Batchs %s will be removed because they are empty", $idBatch));
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
     * Gets the value of any variable.
     * 
     * @param string key
     */
    function getFlag($key)
    {
        return $this->$key;
    }

    function checkFramesIntegrity()
    {
        // NOTE: See setBatchsActiveOrEnded and getBatchToProcess
        // Ensure that batchs have frames or getBatchToProcess will return the same batch over and over
        $sql = "update Batchs set State = 'NoFrames' where idbatch not in (select distinct idbatchup from ServerFrames)";
        $sql .= " and Batchs.State IN ('InTime','Closing')";
        $db = new \Ximdex\Runtime\Db();
        if ($db->execute($sql) === false) {
        	return false;
        }
        if ($db->numRows > 0) {
            Logger::warning(sprintf('Found %s Batchs without Frames, were marked as NoFrames', $db->numRows));
        }
    }

    /**
     * Starts (Ends) the activity of Batchs.
     * 
     * @param int testTime
     */
    function setBatchsActiveOrEnded($testTime = NULL)
    {
        $ended = array();
        $dbObj = new \Ximdex\Runtime\Db();

        // Ending batchs type UP
        $sql = "SELECT ServerFrames.IdBatchUp, SUM(IF(ServerFrames.State='Due2PumpedWithError',1,0)) AS Errors,
			SUM(IF(ServerFrames.State IN ('In','Canceled','Removed','Replaced','Outdated'),1,0)) AS Success,
			SUM(IF(ServerFrames.State IN ('Pumped'),1,0)) AS Pumpeds,
			COUNT(ServerFrames.IdSync) AS Total FROM ServerFrames, Batchs WHERE Batchs.State IN ('InTime','Closing') AND
			Batchs.IdBatch = ServerFrames.IdBatchUp GROUP BY ServerFrames.IdBatchUp HAVING Total = Errors + Success + Pumpeds";
     	if ($dbObj->Query($sql) === false)
        	return false;
        while (!$dbObj->EOF) {
            $idBatch = $dbObj->GetValue("IdBatchUp");
            $errors = $dbObj->GetValue("Errors");
            $success = $dbObj->GetValue("Success");
            $pumpeds = $dbObj->GetValue("Pumpeds");
            $totals = $dbObj->GetValue("Total");
            $batch = new Batch($idBatch);
            $prevState = $batch->get('State');
            $batch->set('ServerFramesSucess', $success);
            $batch->set('ServerFramesError', $errors);
            if ($pumpeds > 0) {
                $batch->set('State', 'Closing');
                $batch->BatchToLog($idBatch, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                    __LINE__, "INFO", 8, sprintf(_("Setting 'Closing' state to %s batch %d UP"), $prevState, $idBatch));
            } else {
                $batch->set('State', 'Ended');
                $batch->BatchToLog($idBatch, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                    __LINE__, "INFO", 8, sprintf(_("Ending %s  batch %d UP"), $prevState, $idBatch));
                Logger::info("Ending up batch with id " . $idBatch);
            }
            $batch->update();
            if ($batch->get('State') == 'Ended') {
                $this->setPortalRevision($idBatch);
            }
            $dbObj->Next();
        }

        // Ending batchs type DOWN
        $batch = new Batch();
        $batchsDown = $batch->find('IdBatch', "Type = 'Down' AND State = 'InTime' AND Playing = 1", NULL, MONO);
        if (sizeof($batchsDown) > 0) {
            foreach ($batchsDown as $idBatch) {
                $sql = "SELECT SUM(IF(ServerFrames.State='Due2OutWithError',1,0)) AS Errors,
					SUM(IF(ServerFrames.State IN ('Out','Canceled','Removed','Replaced'),1,0)) AS Success,
					COUNT(ServerFrames.IdSync) AS Total FROM ServerFrames, Batchs WHERE
					ServerFrames.IdBatchUp = Batchs.IdBatch AND Batchs.IdBatchDown = $idBatch";
                if ($dbObj->Query($sql) === false) {
                	return false;
                }
                $errors = $dbObj->GetValue("Errors");
                $success = $dbObj->GetValue("Success");
                $totals = $dbObj->GetValue("Total");
                $batchDown = new Batch($idBatch);
                $prevState = $batchDown->get('State');
                if ($totals == 0) {
                    Logger::info(sprintf("Batch %d type down without associated batch type up", $idBatch));
                    $generatorId = $batchDown->get('IdNodeGenerator');
                    $sql = "SELECT SUM(IF(ServerFrames.State='Due2OutWithError',1,0)) AS Errors,
						SUM(IF(ServerFrames.State IN ('Out','Canceled','Removed','Replaced'),1,0)) AS Success,
						COUNT(ServerFrames.IdSync) AS Total FROM NodeFrames, ServerFrames WHERE
							ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame and NodeFrames.NodeId = $generatorId";
                    if ($dbObj->Query($sql) === false) {
                    	return false;
                    }
                    $errors = $dbObj->GetValue("Errors");
                    $success = $dbObj->GetValue("Success");
                    $totals = $dbObj->GetValue("Total");
                }
                $batchDown->set('ServerFramesSucess', $success);
                $batchDown->set('ServerFramesError', $errors);
                if ($totals == $errors + $success) {
                    $batchDown->set('State', 'Ended');
                    $batchDown->BatchToLog($idBatch, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                        __LINE__, "INFO", 8, _("Ending " . $prevState . "for batch DOWN $idBatch"));
                    Logger::info("Ending down batch with id " . $idBatch);
                }
                $batchDown->update();
                if ($batchDown->get('State') == 'Ended') $this->setPortalRevision($idBatch);
            }
        }

        // Batchs to start
        if (!$testTime) {
            $now = time();
        } else {
            $now = $testTime;
        }
        $query = "SELECT IdBatch FROM Batchs WHERE Playing = 1 AND State = 'Waiting' AND TimeOn < $now";
        if ($dbObj->Query($query) === false) {
        	return false;
        }
        $listBatchs = array();
        while (!$dbObj->EOF) {
            $listBatchs[] = $dbObj->GetValue("IdBatch");
            $dbObj->Next();
        }
        foreach ($listBatchs as $batchId) {
            $batch = new Batch($batchId);
            $batch->BatchToLog($batchId, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, _("Starting batch") . " $batchId");
            $batch->set('State', 'InTime');
            $batch->update();
        }
    }

    /**
     * Sets the field IdPortalVersion for a Batch.
     * 
     * @param int idBatch
     * @return bool
     */
    function setPortalRevision($idBatch)
    {
        $batch = new Batch($idBatch);
        $idPortalVersion = $batch->get('IdPortalVersion');
        $batchType = $batch->get('Type');
        $result = $batch->find('IdBatch', 'State != %s AND IdPortalVersion = %s AND IdBatch != %s AND Type = %s',
            array('State' => 'Ended', 'IdPortalVersion' => $idPortalVersion, 'IdBatch' => $idBatch, 'Type' => $batchType),
            MONO);

        // There still are batchs in this portal version to close
        if (sizeof($result) != 0) {
            return true;
        }
        $portal = new PortalVersions($idPortalVersion);
        $serverId = $portal->get('IdPortal');
        $serverNode = new Node($serverId);
        $physicalServers = $serverNode->class->GetPhysicalServerList(true, ServerNode::ALL_SERVERS);
        if ($batchType == 'Down') {

            // Updates portal version for all batchs not ended
            $portal = new PortalVersions();
            $portal->upPortalVersion($serverId);
            $result = $batch->find('IdPortalVersion', 'State != %s AND IdBatch > %s AND Type = %s ORDER BY IdBatch ASC',
                array('State' => 'Ended', 'IdBatch' => $idBatch, 'Type' => 'Up'), MONO);
            if (!$result) {
                Logger::warning('No portal version found for batch: ' . $idBatch);
                return false;
            }
            $idPortalVersion = $result[0];
            $batch->set('IdPortalVersion', $idPortalVersion);
            $batch->update();
            $db = new \Ximdex\Runtime\Db();
            $res = $db->execute("UPDATE Batchs SET IdPortalVersion = IdPortalVersion + 1 WHERE State != 'Ended'
				AND IdBatch > $idBatch");
            if (!$res) {
            	return false;
            }
        }

        // All the batchs of this portal version are closed
        $serverFrame = new ServerFrame();
        $nodeFrames = $serverFrame->find('DISTINCT(IdNodeFrame)', 'IdServer IN (%s) AND State = %s',
            array('IdServer' => implode(',', $physicalServers), 'State' => 'IN'), MONO);
        if (($nodeFrames != null) && (is_array($nodeFrames))) {
            foreach ($nodeFrames as $nodeFrameId) {
                $relFramePortal = new \Ximdex\Models\RelFramesPortal();
                $relFramePortal->addVersion($idPortalVersion, $nodeFrameId);
            }
        } else {
            Logger::info("Nodesframes to be added to the portal review do not exist");
        }
        return true;
    }

    /**
     * Gets the Batch that must be processed.
     */
    function getBatchToProcess()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT IdBatch, Type, IdNodeGenerator, MajorCycle, MinorCycle, ServerFramesTotal FROM Batchs
				WHERE Playing = 1 AND State = 'InTime' AND ServerFramesTotal > 0
				ORDER BY Priority DESC, MajorCycle DESC, MinorCycle DESC, Type = 'Down' DESC LIMIT 1";
        if ($dbObj->Query($sql) === false)
        	return false;
        $num = $dbObj->numRows;
        if ($num == 0) {
            return false;
        }
        $list = array();
        $list['id'] = $dbObj->GetValue("IdBatch");
        $list['type'] = $dbObj->GetValue("Type");
        $list['nodegenerator'] = $dbObj->GetValue("IdNodeGenerator");
        $list['majorcycle'] = $dbObj->GetValue("MajorCycle");
        $list['minorcycle'] = $dbObj->GetValue("MinorCycle");
        $list['totalserverframes'] = $dbObj->GetValue("ServerFramesTotal");
        return $list;
    }

    /**
     * Sets the number of Scheduler-Cycles consumed by the Batch and updates its priority.
     * @param int idBatch
     * @return bool
     */
    function setCyclesAndPriority($idBatch)
    {
        if (is_null($idBatch)) {
            return false;
        }
        $batch = new Batch($idBatch);
        $majorCycle = $batch->get('MajorCycle');
        $minorCycle = $batch->get('MinorCycle');
        $allFrames = $batch->get('ServerFramesTotal');
        
        // Unplaying batchs that exceed max num cycles
        if ($majorCycle > MAX_NUM_CICLOS_BATCH) {
            $batch->set('Playing', 0);
            $batch->update();
            $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, _("Unplaying batch") . " $idBatch");
            return true;
        }
        $sucessFrames = $batch->get('ServerFramesSucess');
        $batchPriority = $batch->get('Priority');
        $cycles = $batch->calcCycles($majorCycle, $minorCycle);
        
        // Calc priority
        if ($allFrames == 0) {
            $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, _("Batch without ServerFrames"));
            $batch->set('Playing', 0);
            $batch->update();
            return false;
        }
        $porcentaje = 100 * $sucessFrames / $allFrames;
        if ($porcentaje > 25) {
            $systemPriority = MAX_SYSTEM_PRIORITY;
            $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, sprintf(_("Up batch %d priority"), $idBatch));
        } else if ($porcentaje < 25) {
            $systemPriority = -MIN_SYSTEM_PRIORITY;
            /*
            if ($cycles[0] > MAX_NUM_CICLOS_BATCH) {
                $systemPriority = $systemPriority - MIN_SYSTEM_PRIORITY;
            }
            */
            $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, sprintf(_("Down batch %d priority"), $idBatch));
        } else {
            $systemPriority = 0;
        }
        $priority = $batchPriority + $systemPriority;
        if ($priority < MIN_TOTAL_PRIORITY) {
            $priority = (float)MIN_TOTAL_PRIORITY;
        } else if ($priority > MAX_TOTAL_PRIORITY) {
            $priority = (float)MAX_TOTAL_PRIORITY;
        }
        if (is_null($cycles)) {
            $batch->batchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "ERROR", 8, _("ERROR Calc cycles"));
        } else {
            $batch->set('MajorCycle', $cycles[0]);
            $batch->set('MinorCycle', $cycles[1]);
        }
        $batch->set('Priority', $priority);
        $result = $batch->update();
        if (!($result > 0)) {
            return false;
        }
        return true;
    }

    /**
     * Checks if the Batch status is correct.
     * 
     * @param array activeAndEnabledServers
     * @return bool
     */
    function checkBatchState($activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $batchType = $this->get('Type');
        $totalServerFrames = $this->get('ServerFramesTotal');
        $sucessServerFrames = $this->get('ServerFramesSucess');
        $cycles = $this->get('MajorCycle');
        if ($batchType == 'Up') {
            $batchColumn = 'IdBatchUp';
        } else if ($batchType == 'Down') {
            $batchColumn = 'IdBatchDown';
        } else {
            Logger::info(sprintf("ERROR: %s rare type of batch", $batchType));
            return false;
        }
        $inactives = implode(',', $activeAndEnabledServers);
        $query = "SELECT IdSync FROM ServerFrames, Batchs, Pumpers WHERE ServerFrames.IdBatchUp = Batchs.IdBatch AND " .
            "ServerFrames.PumperId = Pumpers.PumperId AND " .
            "Batchs.$batchColumn = $batchId AND Pumpers.IdServer NOT IN ($inactives)";
        if ($dbObj->Query($query) === false) {
            return false;
        }
        $numServerFramesFromInactiveServers = $dbObj->numRows;
        if ($totalServerFrames == $numServerFramesFromInactiveServers + $sucessServerFrames) {
            Logger::info(sprintf("ERROR: %s rare type of batch", $batchType));
            $this->set('State', 'Ended');
            $this->update();
        }
    }

    /**
     * Sets the field Playing for all Batchs.
     * 
     * @param int playingValue
     */
    function setAllBatchsPlayingOrUnplaying($playingValue)
    {
        $batch = new Batch();
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "UPDATE Batchs set Playing = '$playingValue'";
        $dbObj->Execute($sql);
        if ($dbObj->numRows > 0) {
            $batch->BatchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, $dbObj->numRows . " " . _("Setting batchs to") . ($playingValue == 1) ? " playing" : " unplaying");
        }
    }

    /**
     * Gets the Batch asociated with an active NodeFrame.
     * 
     * @param int idNode
     * @param int idServer
     * @return int|null
     */
    function getPublicatedBatchForNode($idNode, $idServer)
    {
        $serverFrame = new ServerFrame();
        $serverFrameId = $serverFrame->getCurrentPublicatedFrame($idNode, $idServer);
        if (!is_null($serverFrameId)) {
            $serverFrame = new ServerFrame($serverFrameId);
            $idBatchUp = $serverFrame->get('IdBatchUp');
            if ($idBatchUp > 0) {
                return $idBatchUp;
            }
        }
        return NULL;
    }

    /**
     * Creates a Batch for remove documents of the publication server (Batch type Down).
     * 
     * @param int idBatchUp
     * @param int nodeId
     * @param int serverFramesTotal
     * @return bool
     */
    function buildBatchsFromDeleteNode($idBatchUp, $nodeId, $serverFramesTotal, $userId = null)
    {
        $batch = new Batch();
        $batchDownArray = $batch->getDownBatch($idBatchUp);
        if (isset($batchDownArray) && count($batchDownArray) > 0) {

            // Updating Batch Type Down (if exists) State to Waiting
            $batchDown = new Batch($batchDownArray['IdBatch']);
            $batchDown->set('State', 'Waiting');
            $batchDown->set('ServerFramesTotal', $serverFramesTotal);
            $batchDown->set('ServerFramesSucess', 0);
            $batchDown->set('ServerFramesError', 0);
            $batchDown->update();
        } else {

            // Gets portal version
            $node = new Node($nodeId);
            $serverID = $node->GetServer();
            if (!$serverID) {
                Logger::error('Unable to load the server with ID: ' . $serverID);
                return false;
            }
            $portal = new PortalVersions();
            $idPortalVersion = $portal->upPortalVersion($serverID);
            if (!$idPortalVersion) {
                Logger::error('Unable to increase the portal version with ID: ' . $serverID);
                return false;
            }

            // Creating Batch Type Down if not exist one
            $batchDown = new Batch();
            $idBatchDown = $batchDown->create(time(), 'Down', $nodeId, 1, null, $idPortalVersion, $userId);

            // Updating Serverframes info
            $batchDown = new Batch($idBatchDown);
            $batchDown->set('ServerFramesTotal', $serverFramesTotal);
            $batchDown->set('ServerFramesSucess', 0);
            $batchDown->set('ServerFramesError', 0);
            $batchDown->set('IdPortalVersion', $idPortalVersion);
            $batchDown->update();
        }
        return true;
    }

    /**
     * Starts the publication (again) of a Batch that is finished.
     * @param int idBatchUp
     * @param string frameState
     * @return bool
     */
    function updateBatchFromRepublishAncestors($idBatchUp, $frameState)
    {
        $batch = new Batch($idBatchUp);
        if (!($batch->get('IdBatch') > 0)) {
            $batch->BatchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, sprintf(_("Batch %d does not exist"), $idBatchUp));
            return null;
        }
        if ($batch->get('State') == 'Ended') {
            $batch->set('State', 'Waiting');
        }
        if (!strpos($frameState, 'ERROR')) {
            $batch->set('ServerFramesSucess', $batch->get('ServerFramesSucess') - 1);
        } else {
            $batch->set('ServerFramesError', $batch->get('ServerFramesError') - 1);
        }
        $batch->update();
        return null;
    }

    /**
     * Gets all batchs that must be processed (used by MPM).
     * 
     * @return array
     */
    function getAllBatchToProcess()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT IdBatch, Type, IdNodeGenerator, MajorCycle, MinorCycle, ServerFramesTotal FROM Batchs
				WHERE Playing = 1 AND State = 'InTime' AND ServerFramesTotal > 0
				ORDER BY Priority DESC, MajorCycle DESC, MinorCycle DESC, Type = 'Down'";
        if ($dbObj->Query($sql) === false) {
            return false;
        }
        if ($dbObj->numRows > 0) {
            $batchs = array();
            $i = 0;
            while (!$dbObj->EOF) {
                $list = array();
                $list['id'] = $dbObj->GetValue("IdBatch");
                $list['type'] = $dbObj->GetValue("Type");
                $list['nodegenerator'] = $dbObj->GetValue("IdNodeGenerator");
                $list['majorcycle'] = $dbObj->GetValue("MajorCycle");
                $list['minorcycle'] = $dbObj->GetValue("MinorCycle");
                $list['totalserverframes'] = $dbObj->GetValue("ServerFramesTotal");
                $batchs[$i] = $list;
                $i++;
                $dbObj->Next();
            }
        } else {
            return false;
        }
        return $batchs;
    }
}
