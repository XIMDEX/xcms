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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\Server;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\PipelineManager;

Ximdex\Modules\Manager::file('/inc/model/orm/ServerFrames_ORM.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/NodeFrame.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/conf/synchro_conf.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/PublishingReport.class.php', 'ximSYNC');

/**
 * @brief Handles operations with ServerFrames.
 *
 * A ServerFrame is the representation of a ChannelFrame in a Server.
 * This class includes the methods that interact with the Database.
 */
class ServerFrame extends ServerFrames_ORM
{
    const PENDING = 'Pending';
    const DUE2IN = 'Due2In';
    const DUE2OUT = 'Due2Out';
    const PUMPED = 'Pumped';
    const OUT = 'Out';
    const CLOSING = 'Closing';
    const IN = 'In';
    const REPLACED = 'Replaced';
    const REMOVED = 'Removed';
    const CANCELLED = 'Canceled';
    const DUE2INWITHERROR = 'Due2InWithError';
    const DUE2OUTWITHERROR = 'Due2OutWithError';
    const OUTDATED = 'Outdated';
    
    public $initialStatus;
    public $errorStatus;
    public $finalStatus;
    public $finalStatusOk;
    public $finalStatusLimbo;
    public $finalStatusFailed;
    public $syncStatObj;

    public function __construct($id = 0)
    {
        $this->initialStatus = array(
            ServerFrame::PENDING,
            ServerFrame::DUE2IN,
            ServerFrame::DUE2OUT
        );
        $this->errorStatus = array(
            ServerFrame::DUE2INWITHERROR,
            ServerFrame::DUE2OUTWITHERROR
        );
        $this->finalStatusOk = array(
            ServerFrame::IN
        );
        $this->finalStatusLimbo = array(
            ServerFrame::REPLACED,
            ServerFrame::REMOVED,
            ServerFrame::PUMPED,
            ServerFrame::OUT,
            ServerFrame::CLOSING
        );
        $this->finalStatusFailed = array(
            ServerFrame::CANCELLED
        );
        $this->finalStatus = array_merge($this->finalStatusOk, $this->finalStatusLimbo, $this->finalStatusFailed);
        $this->publishingReport = new PublishingReport();
        parent::__construct($id);
    }
    
    public function set($attribute, $value)
    {
        if ($attribute == 'State') {
            Logger::info('Changing state for server frame: ' . $this->get('IdSync') . ' from ' . $this->get('State') . ' to ' . $value);
        }
        parent::set($attribute, $value);
    }

    public function update()
    {
        if (\Ximdex\Modules\Manager::isEnabled('ximPUBLISHtools')) {
            if ($this->get('IdNodeFrame') > 0) {
                $batch = new Batch($this->get('IdBatchUp'));
                $nodeFrame = new NodeFrame($this->get('IdNodeFrame'));
                $channelFrames = new ChannelFrame($this->get('IdChannelFrame'));
                $idChannel = $channelFrames->get('ChannelId');
                $searchFields = array(
                    'IdNode' => $nodeFrame->get('NodeId'),
                    'IdSyncServer' => $this->get('IdServer'),
                    'IdChannel' => $idChannel
                );
                $updateFields = array(
                    'State' => $this->get('State'),
                    'Progress' => $this->publishingReport->progressTable[$this->get('State')]
                );
                if (($this->get('ErrorLevel') != '0')) {
                    $updateFields['State'] = 'Error';
                    $updateFields['Progress'] = '100';
                } else if ($this->get('FileSize') == "0" && in_array($this->get('State'), [
                    ServerFrame::DUE2IN,
                    ServerFrame::PUMPED,
                    ServerFrame::IN
                ])) {
                    $updateFields['State'] = 'Warning';
                    $updateFields['Progress'] = '100';
                }
                $this->publishingReport->updateReportByField($updateFields, $searchFields);
            }
        }
        return parent::update();
    }

    /**
     * Adds a row to ServerFrames table.
     * 
     * @param int nodeId
     * @param int server
     * @param int dateUp
     * @param string path
     * @param string name
     * @param int publishLinked
     * @param int idNodeFrame
     * @param int idChannelFrame
     * @param int idServerFrame
     * @param int idBatchUp
     * @param int dateDown
     * @param int size
     * @return int|null
     */
    public function create($nodeId, $server, $dateUp, $path, $name, $publishLinked, $idNodeFrame, $idChannel, $idChannelFrame
        , $idBatchUp, $dateDown = NULL, $size = 0, bool $cache = true)
    {
        $this->set('IdServer', $server);
        $this->set('DateUp', $dateUp);
        $this->set('DateDown', $dateDown);
        $this->set('State', 'Pending');
        $this->set('Error', NULL);
        $this->set('ErrorLevel', 0);
        $this->set('RemotePath', $path);
        $this->set('FileName', $name);
        $this->set('FileSize', $size);
        $this->set('Retry', 0);
        $this->set('Linked', $publishLinked);
        $this->set('IdNodeFrame', $idNodeFrame);
        $this->set('IdBatchUp', $idBatchUp);
        $this->set('IdChannelFrame', $idChannelFrame);
        $this->set('cache', (int) $cache);
        $this->set('NodeId', $nodeId);
        if ($idChannel) {
            $this->set('ChannelId', $idChannel);
        }
        parent::add();
        $idServerFrame = $this->get('IdSync');
        if ($idServerFrame > 0) {
            if (\Ximdex\Modules\Manager::isEnabled('ximPUBLISHtools')) {
                $batch = new Batch($idBatchUp);
                $idSection = $batch->get('IdNodeGenerator');
                $sectionNode = new Node($idSection);
                $idParentServer = $sectionNode->getServer();
                $idPortalVersion = $batch->get('IdPortalVersion');
                $channelFrames = new ChannelFrame($idChannelFrame);
                $idChannel = $channelFrames->get('ChannelId');
                $this->publishingReport->create($idSection, $nodeId, empty($idChannel) ? NULL : $idChannel, $server, $idPortalVersion
                    , time(), 'Pending', '20', $name, $path, $idServerFrame, $idBatchUp, $idParentServer);
            }
            return $idServerFrame;
        }
        $this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, "Creando el serverFrame");
        return NULL;
    }

    /**
     * Gets all Servers from ServerFrames table.
     * 
     * @param string simple
     * @return array
     */
    public function getServers($mode = "simple")
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $extraSql = ($mode == "simple") ? "" : ", Servers.Description, Servers.Url";
        $dbObj->Query("SELECT DISTINCT(ServerFrames.IdServer)" . $extraSql . " FROM ServerFrames, Servers
				WHERE ServerFrames.IdServer = Servers.IdServer AND Servers.Enabled = 1");
        $servers = array();
        while (! $dbObj->EOF) {
            if ($mode == "simple") {
                $servers[] = $dbObj->GetValue("IdServer");
            } else {
                $servers[$dbObj->GetValue("IdServer")]['Description'] = $dbObj->GetValue("Description");
                $servers[$dbObj->GetValue("IdServer")]['Url'] = $dbObj->GetValue("Url");
            }
            $dbObj->Next();
        }
        return $servers;
    }

    /**
     * Gets the field IdSync from ServerFrames join NodeFrames which matching the values of nodeId and serverId.
     * 
     * @param int nodeID
     * @param int serverID
     * @return int|null
     */
    public function getCurrentPublicatedFrame($nodeID, $serverID)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE ServerFrames.IdNodeFrame =
			NodeFrames.IdNodeFrame AND NodeFrames.NodeId = $nodeID AND ServerFrames.IdServer = $serverID AND
			(ServerFrames.State = '" . ServerFrame::IN . "' OR ServerFrames.State = 'Pumped')";
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            return $dbObj->GetValue("IdSync");
        }
        $this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8
            , "ERROR getting publicated serverFrame");
        return NULL;
    }

    /**
     * Gets the field IdSync from ServerFrames join NodeFrames which matching the value of nodeId and it is the newest.
     * 
     * @param int nodeID
     * @param int IdServer
     * @return int|null
     */
    function getLastFrame($nodeID, $IdServer)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        if (! is_null($nodeID)) {
            $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE
					ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IdNode = $nodeID AND
					ServerFrames.IdServer = $IdServer ORDER BY ServerFrames.DateUp DESC";
            $dbObj->Query($sql);
            return $dbObj->GetValue("IdSync");
        }
        $this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, "ERROR. Falta el NodeID");
    }

    /**
     * Creates the file which will be sent to the production Server
     * 
     * @param $frameID
     * @param bool $cache
     * @return boolean|NULL|int
     */
    public function createSyncFile($frameID, bool $cache = true)
    {
        $path = SERVERFRAMES_SYNC_PATH . "/" . $frameID;
        $channelFrameId = $this->get('IdChannelFrame');
        $nodeFrameId = $this->get('IdNodeFrame');
        $server = $this->get('IdServer');
        $s = new Server($server);
        $channelFrame = new ChannelFrame($channelFrameId);
        if (!$channelFrame->get('IdChannelFrame')) {
            Logger::warning('Unable to load the channel frame with ID: ' . $channelFrameId . '. Using the frame field instead');
            if ($this->get('IdSync')) {
                $channelId = $this->get('ChannelId');
            }
            else {
                $serverFrame = new ServerFrame($frameID);
                if (!$serverFrame->get('IdSync')) {
                    Logger::error('Unable to load the server frame with ID: ' . $frameID);
                    return false;
                }
                $channelId = $serverFrame->get('ChannelId');
            }
        }
        else {
            $channelId = $channelFrame->get('ChannelId');
        }
        $nodeFrame = new NodeFrame($nodeFrameId);
        $idVersion = $nodeFrame->get('VersionId');
        $idNode = $nodeFrame->get('NodeId');
        if (! ($idNode > 0)) {
            Logger::error("Unexisting node for serverframe $frameID");
            return false;
        }
        $node = new Node($idNode);
        if (!$node->GetID()) {
            return false;
        }
        $isHybrid = $node->getSimpleBooleanProperty('hybridColector');
        $data['CHANNEL'] = $channelId;
        $data['SERVER'] = $server;
        if (!$cache) {
            $data['DISABLE_CACHE'] = true;
        }
        else {
            $data['DISABLE_CACHE'] = App::getValue("DisableCache");
        }
        $transformer = $node->getProperty('Transformer');
        $data['TRANSFORMER'] = $transformer[0];
        $data['NODEID'] = $idNode;
        $pipeMng = new PipelineManager();
        if (! is_null($channelId)) {
            if ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                $process = 'HTMLToPublished';
            } else {
                $process = 'StrDocFromDexTToFinal';
            }
            $content = $pipeMng->getCacheFromProcessAsContent($idVersion, $process, $data);
            if ($content === false) {
                Logger::error('Cannot load the cache or actual version content for version: ' . $idVersion);
                return false;
            }
            if ($content === null) {
                return null;
            }
            $nodeTypeContent = $node->nodeType->get('Name');
            
            // Only encoding the content if the node is not one of this 3.
            if (! (($nodeTypeContent == 'ImageFile') || ($nodeTypeContent == 'BinaryFile'))) {
                
                // Looking for idEncode for this server
                $db = new \Ximdex\Runtime\Db();
                $sql = "SELECT idEncode FROM Servers WHERE IdServer=" . $server;
                $db->Query($sql);
                $encodingServer = $db->GetValue("idEncode");
                Logger::info("Encoding content to " . $encodingServer . ' with server: ' . $server);
                $content = \Ximdex\XML\Base::recodeSrc($content, $encodingServer);
            } else {
                Logger::warning('The node is not a structured document with a channel');
            }
            if (FsUtils::file_put_contents($path, $content) === false) {
                return false;
            }
        } else {
            
            // Replaces macros
            $node = new Node($idNode);
            if ($node->nodeType->get('Name') == 'XslTemplate') {
                $data['REPLACEMACROS'] = 'yes';
            }
            $file = $pipeMng->getCacheFromProcess($idVersion, 'NotStrDocToFinal', $data);
            FsUtils::copy($file, $path);
        }
        clearstatcache();
        
        // Its necessary to updating SyncFile size in BD
        $fileSize = file_exists($path) ? filesize($path) : 0;
        return $fileSize;
    }

    /**
     * Deletes the file associated to a ServerFrame.
     * 
     * @return bool
     */
    public function deleteSyncFile()
    {
        if (! ($this->get('IdSync')) > 0) {
            $this->ServerFrameToLog(null, null, null, $frameId, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, "Deleting Sync files");
            return false;
        }
        return FsUtils::delete(SERVERFRAMES_SYNC_PATH . '/' . $this->get('IdSync'));
    }

    /**
     * Gets all ServerFrames from a Batch.
     * 
     * @param int batchId
     * @param string batchColumn
     * @param string mode
     * @param array progress
     * @param int limitCriteria
     * @return array
     */
    public function getFramesOnBatch($batchId, $batchColumn, $mode = "simple", & $progress = array(), $limitCriteria = null)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT ServerFrames.IdSync" . (($mode == 'simple') ? '' : ', ServerFrames.DateUp, ServerFrames.DateDown, ' 
            . 'ServerFrames.FileSize, ServerFrames.State, ServerFrames.FileName, ServerFrames.PumperId, ServerFrames.IdServer, '
            . 'ServerFrames.RemotePath') 
            . " FROM ServerFrames, Batchs "
            . "WHERE (ServerFrames.IdBatchUp = Batchs.IdBatch OR ServerFrames.IdBatchDown = Batchs.IdBatch) AND Batchs.$batchColumn = $batchId";
        $dbObj->Query($sql);
        $frames = array();
        $progress['total']['totalBatchSize'] = 0;
        $progress['total']['totalBatchSizeCompleted'] = 0;
        $progress['total']['totalBatchCompleted'] = 0;
        $progress['total']['avgBatchSize'] = 0;
        $progress['total']['percentBatchSizeCompleted'] = 0;
        $progress['total']['percentBatchCompleted'] = 0;
        $progress['total']['totalBatch'] = 0;
        $serverIds = array(
            'total'
        );
        $iCounter = 0;
        $pageCounter = 0;
        while (! $dbObj->EOF) {
            if ($mode == 'simple') {
                $frames[] = $dbObj->GetValue("IdSync");
            } else {
                $iCounter ++;
                $idSync = $dbObj->GetValue("IdSync");
                $idServer = $dbObj->GetValue("IdServer");
                $fileSize = round($dbObj->GetValue("FileSize") / 1024, 2);
                $serverIds[] = $idServer;
                $pageCounter = ceil($iCounter / $limitCriteria);
                $frames[$pageCounter][$idServer][$idSync]['DateUp'] = $dbObj->GetValue("DateUp");
                $frames[$pageCounter][$idServer][$idSync]['DateDown'] = $dbObj->GetValue("DateDown");
                $frames[$pageCounter][$idServer][$idSync]['State'] = $dbObj->GetValue("State");
                $frames[$pageCounter][$idServer][$idSync]['FileName'] = $dbObj->GetValue("FileName");
                $frames[$pageCounter][$idServer][$idSync]['PumperId'] = $dbObj->GetValue("PumperId");
                $frames[$pageCounter][$idServer][$idSync]['FileSize'] = $fileSize;
                $frames[$pageCounter][$idServer][$idSync]['RemotePath'] = $dbObj->GetValue("RemotePath");
                if (! isset($progress[$idServer]['totalBatchSize'])) {
                    $progress[$idServer]['totalBatchSize'] = 0;
                }
                if (! isset($progress[$idServer]['totalBatchSizeCompleted'])) {
                    $progress[$idServer]['totalBatchSizeCompleted'] = 0;
                }
                if (! isset($progress[$idServer]['totalBatchCompleted'])) {
                    $progress[$idServer]['totalBatchCompleted'] = 0;
                }
                if (! isset($progress[$idServer]['totalBatch'])) {
                    $progress[$idServer]['totalBatch'] = 0;
                }
                if (! isset($progress[$idServer]['avgBatchSize'])) {
                    $progress[$idServer]['avgBatchSize'] = 0;
                }
                if (! isset($progress[$idServer]['percentBatchSizeCompleted'])) {
                    $progress[$idServer]['percentBatchSizeCompleted'] = 0;
                }
                if (! isset($progress[$idServer]['percentBatchCompleted'])) {
                    $progress[$idServer]['percentBatchCompleted'] = 0;
                }
                $progress['total']['totalBatchSize'] += $fileSize;
                $progress['total']['totalBatchSizeCompleted'] += ($dbObj->GetValue("State") == ServerFrame::IN || $dbObj->GetValue("State") == 'Out' 
                    || $dbObj->GetValue("State") == 'Removed' || $dbObj->GetValue("State") == 'Replaced' 
                    || $dbObj->GetValue("State") == 'Pumped') ? $fileSize : 0;
                $progress['total']['totalBatchCompleted'] += ($dbObj->GetValue("State") == ServerFrame::IN || $dbObj->GetValue("State") == 'Out' 
                    || $dbObj->GetValue("State") == 'Removed' || $dbObj->GetValue("State") == 'Replaced' 
                    || $dbObj->GetValue("State") == 'Pumped') ? 1 : 0;
                $progress['total']['totalBatch'] ++;
                $progress[$idServer]['totalBatchSize'] += $fileSize;
                $progress[$idServer]['totalBatchSizeCompleted'] += ($dbObj->GetValue("State") == ServerFrame::IN
                    || $dbObj->GetValue("State") == 'Out' 
                    || $dbObj->GetValue("State") == 'Removed' || $dbObj->GetValue("State") == 'Replaced' 
                    || $dbObj->GetValue("State") == 'Pumped') ? $fileSize : 0;
                $progress[$idServer]['totalBatchCompleted'] += ($dbObj->GetValue("State") == ServerFrame::IN 
                    || $dbObj->GetValue("State") == 'Out' 
                    || $dbObj->GetValue("State") == 'Removed' || $dbObj->GetValue("State") == 'Replaced' 
                    || $dbObj->GetValue("State") == 'Pumped') ? 1 : 0;
                $progress[$idServer]['totalBatch'] ++;
            }
            $dbObj->Next();
        }
        if ($mode != 'simple' && is_array($frames) && $progress['total']['totalBatch'] > 0) {
            foreach ($serverIds as $serverId) {
                $progress[$serverId]['avgBatchSize'] = round($progress[$serverId]['totalBatchSize'] / $progress[$serverId]['totalBatch'], 2);
                $progress[$serverId]['percentBatchCompleted'] = round(($progress[$serverId]['totalBatchCompleted'] * 100) 
                    / $progress[$serverId]['totalBatch'], 2);
                if ($progress[$serverId]['totalBatchSize'] > 0) {
                    $progress[$serverId]['percentBatchSizeCompleted'] = round(($progress[$serverId]['totalBatchSizeCompleted'] * 100) 
                        / $progress[$serverId]['totalBatchSize'], 2);
                }
            }
        }
        return $frames;
    }

    /**
     * Gets the number of ServerFrames which matching the value of pumperId and belong to a list of Servers.
     * 
     * @param int nodeId
     * @param array activeAndEnabledServers
     * @return int
     */
    public function getUncompletedTasks($pumperID, $activeAndEnabledServers)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $servers = implode(',', $activeAndEnabledServers);
        $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, Pumpers WHERE ServerFrames.PumperId = Pumpers.PumperId AND " 
            . "(ServerFrames.State = 'Due2In' OR ServerFrames.State = 'Due2Out') " 
            . "AND ServerFrames.PumperId = $pumperID AND Pumpers.IdServer IN ($servers)";
        $dbObj->Query($sql);
        $n = $dbObj->numRows;
        $this->ServerFrameToLog(null, null, null, null, $pumperID, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8
            , "Bombeador $pumperID tiene $n tareas incompletas");
        return $n;
    }

    /**
     * Sets the State field from ServerFrames table which matching the value of pumperId.
     * 
     * @param int pumperId
     */
    public function rescueErroneous($pumperId)
    {
        $sql = "UPDATE ServerFrames SET State = LEFT(State, LENGTH(State) - LENGTH('witherror'))
				WHERE State IN ('Due2PumpedWithError', 'Due2OutWithError') AND PumperId = $pumperId";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
    }

    /**
     * Gets the ServerFrame with a time interval that includes the current time, who match the values of nodeId
     * and channelId, and whose State is in (in, due2in, pending, due2in_, due2pumped, pumped, replaced and removed)
     *
     * @param int nodeID
     * @param int channelID
     * @return int|null
     */
    public function getCurrent($nodeID, $channelID = null)
    {
        $now = time();
        if ($channelID) {
            $channelClause = "AND c.ChannelId = " . $channelID . " ";
        }
        elseif ($channelID === null) {
            $channelClause = 'AND c.ChannelId IS NULL ';
        }
        else {
            $channelClause = '';
        }
        $node = new Node($nodeID);
        $serverID = $node->GetServer();
        if (! ($serverID > 0)) {
            Logger::error('Trying to publish a node that is not contained on a server ' . $nodeID);
            return NULL;
        }
        $nodeServer = new Node($serverID);
        if (App::getValue('PublishOnDisabledServers') == 1) {
            $physicalServers = $nodeServer->class->GetPhysicalServerList(true);
        } else {
            $physicalServers = $nodeServer->class->GetPhysicalServerList(true, true);
        }
        if (count($physicalServers) == 0) {
            Logger::info("[GETCURRENT]: No physical servers found. IdSync: none");
            return NULL;
        }
        $sql = sprintf("SELECT IdSync " . "FROM ServerFrames sf " . "INNER JOIN ChannelFrames c ON c.IdChannelFrame = sf.IdChannelFrame " 
            . "WHERE c.NodeId = " . $nodeID . " " . $channelClause . "AND sf.DateUp < %s AND (sf.DateDown > %s OR sf.DateDown IS NULL) " 
            . "AND sf.State IN ('" . ServerFrame::IN . "', 'Due2In_', 'Due2In', 'Due2Pumped', 'Pumped', 'Replaced', 'Removed') " 
            . "AND sf.IdServer IN (%s)", $now, $now, implode(', ', $physicalServers));
        $sql .= ' ORDER BY IdSync DESC LIMIT 1';
        Logger::info("[GETCURRENT]: Getting current frame for node " . $nodeID);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        $result = ($dbObj->EOF) ? 'IdSync: none' : 'IdSync: ' . $dbObj->GetValue("IdSync");
        Logger::info("[GETCURRENT]: Result:  " . $result);
        return ($dbObj->EOF) ? NULL : $dbObj->GetValue("IdSync");
    }

    /**
     * Return complete server list, not only the server the last server
     * 
     * @param $nodeId
     * @param $channelID
     * @return NULL|string[]
     */
    public function getCompleteServerList($nodeId, $channelID = null)
    {
        $extraCondition = "";
        if ($channelID != null) {
            $extraCondition = " AND cf.channelid=$channelID";
        }
        $sql = "SELECT distinct IdServer From ServerFrames sf inner join ChannelFrames cf on sf.idChannelFrame = cf.idChannelFrame";
        $sql .= " where cf.nodeid = $nodeId ";
        $sql .= $extraCondition;
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->GetValue("IdServer");
            $dbObj->Next();
        }
        return $list;
    }

    /**
     * Gets the fields RemotePath and FileName from ServerFrames which matching the values of Server, NodeFrame and Channel.
     * 
     * @param int frameId
     * @param int channelID
     * @param int serverID
     * @return string|null
     */
    public function getUrlLastPublicatedNews($frameId, $channelID, $serverID)
    {
        $now = time();
        $sql = "SELECT IdSync, RemotePath, FileName FROM ServerFrames WHERE  ";
        $sql .= " IdChannelFrame = $channelID AND DateUp < $now ";
        $sql .= " AND ServerFrames.IdServer = $serverID ";
        $sql .= " AND IdNodeFrame = $frameId ";
        $sql .= " AND (DateDown > $now OR DateDown IS NULL) AND State = '" . ServerFrame::IN . "'";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        $path = $dbObj->GetValue("RemotePath");
        $filename = $dbObj->GetValue("FileName");
        if ($filename) {
            return $path . "/" . $filename;
        }
        return null;
    }

    /**
     * Gets the field IdServer from ServerFrames table which matching the value of idNodeFrame.
     * 
     * @param int idNodeFrame
     * @return array|null
     */
    public function getServerListOnFrame($idNodeFrame)
    {
        $result = $this->find('IdServer', 'IdNodeFrame = ' . $idNodeFrame, array(), MONO);
        if (! (sizeof($result) > 0)) {
            return NULL;
        }
        return $result;
    }

    /**
     * Deletes a row from ServerFrames and updates the values of ServerFrames related fields in Batchs table.
     */
    public function delete()
    {
        // Updating num. serverFrames in batch
        $idBatch = $this->get('IdBatchUp');
        $state = $this->get('State');
        $batch = new Batch($idBatch);
        $batch->set('ServerFramesTotal', $batch->get('ServerFramesTotal') - 1);
        if (! strpos($state, 'ERROR')) {
            $batch->set('ServerFramesSucess', $batch->get('ServerFramesSucess') - 1);
        } else {
            $batch->set('ServerFramesError', $batch->get('ServerFramesError') - 1);
        }
        $batch->update();
        
        // Deleting serverFrame
        parent::delete();
    }

    /**
     * Logs the activity of the ServerFrame.
     * 
     * @param int batchId
     * @param int nodeFrameId
     * @param int channelFrameId
     * @param int serverFrameId
     * @param int pumperId
     * @param string class
     * @param string method
     * @param string file
     * @param int line
     * @param string type
     * @param int level
     * @param string comment
     * @param int doInsertSql
     */
    public function ServerFrameToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId, $class, $method, $file, $line
        , $type, $level, $comment, $doInsertSql = false)
    {
        if (strcmp(App::getValue("SyncStats"), "1") == 0) {
            if (! isset($this->syncStatObj)) {
                $this->syncStatObj = new SynchronizerStat();
            }
            $this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId, $class, $method, $file
                , $line, $type, $level, $comment, $doInsertSql);
        }
        Logger::debug('ServerFrameToLog -> batchId: ' . $batchId . ' nodeFrameId: ' . $nodeFrameId . ' channelFrameId: ' 
            . $channelFrameId . ' serverFrameId:' . $serverFrameId . ' pumperId:' . $pumperId . ' method:' . $method . ' file:' . $file 
            . ' line:' . $line . ' type:' . $type . ' level:' . $level . ' comment:' . $comment . ' doInsertSql:' . $doInsertSql);
    }

    /**
     * Gets the rows from ServerFrames which matching the value of idPumper.
     * 
     * @param int idPumper
     * @return array
     */
    public function getPublishableNodesForPumper($idPumper)
    {
        $query = "SELECT sf.* FROM ServerFrames sf, Batchs b WHERE (sf.IdBatchUp = b.IdBatch OR sf.IdBatchDown = b.IdBatch) AND (sf.State = '" 
            . ServerFrame::DUE2IN . "' OR " . "sf.State = '" . ServerFrame::DUE2OUT . "' OR (sf.State = '" . ServerFrame::PUMPED . "' AND b.State = '" . Batch::CLOSING . "')) " 
            . "AND sf.PumperId = $idPumper LIMIT 1";
        return $this->query($query);
    }

    public function getPublicationQueue($idServer)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT n.idnode, n.path,v.version, n.name, dateup, state, filesize,concat(v.`Version`,'.',v.`SubVersion`) ";
        $sql .= "FROM ServerFrames sf INNER JOIN NodeFrames nf using (idnodeframe) INNER JOIN Nodes n ON n.idnode=nf.nodeid ";
        $sql .= "INNER JOIN  Versions v ON v.Idnode=n.idnode INNER JOIN Servers s ON sf.IdServer=s.IdServer AND s.IdNode=$idServer ";
        $sql .= "where subversion=0  and state not in ('Removed','Replaced','Canceled') ";
        $sql .= "and Not exists (select idversion from Versions v2 where v2.idversion<> v.idversion and v.idnode=v2.idnode and v2.version>v.version) ";
        $sql .= "order by sf.idsync  DESC LIMIT 20";
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            $publications = array();
            while (! $dbObj->EOF) {
                $publication = array();
                $publication['name'] = $dbObj->GetValue("name");
                $publication['path'] = str_replace("/Ximdex/Projects", "", $dbObj->GetValue("path"));
                $publication['filesize'] = $dbObj->GetValue("filesize");
                $publication['date'] = $dbObj->GetValue("dateup");
                $publication['id'] = $dbObj->GetValue("idnode");
                $publication['state'] = $dbObj->GetValue("state");
                $publication['version'] = $dbObj->GetValue("concat(v.`Version`,'.',v.`SubVersion`)");
                array_push($publications, $publication);
                $dbObj->Next();
            }
            return $publications;
        }
        return NULL;
    }
    
    /**
     * @return int|NULL
     */
    public function getNodeID() : ?int
    {
        if (!$this->IdChannelFrame) {
            return null;
        }
        $channelFrame = new ChannelFrame($this->IdChannelFrame);
        if (!$channelFrame->get('NodeId')) {
            return null;
        }
        return $channelFrame->get('NodeId');
    }
    
    /**
     * Get all server frames whose publication period is active in a given timestamp for a specified node ID
     * 
     * @param int $nodeId
     * @param int $time
     * @return array|bool
     */
    public function getFramesOnDate(int $nodeId, int $time)
    {
        $sql = 'SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames ' . 
            'WHERE ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.NodeId = ' . $nodeId . ' AND ' . 
	        'ServerFrames.DateUp <= ' . $time . ' AND (ServerFrames.DateDown >= ' . $time . ' OR ServerFrames.DateDown IS NULL) ' . 
	        'AND ServerFrames.State NOT IN (\'' . ServerFrame::CANCELLED . '\', \'' . ServerFrame::REMOVED . '\', \'' . ServerFrame::REPLACED . '\')';
        return $this->query($sql);
    }
    
    /**
     * Get all frames that will be activated after a given timestamp and specified node ID 
     * 
     * @param int $nodeId
     * @param int $time
     * @return array|bool
     */
    public function getFutureFramesForDate(int $nodeId, int $time)
    {
        $sql = 'SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames ' .
            'WHERE ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.NodeId = ' . $nodeId . ' AND ' .
            'ServerFrames.DateUp > ' . $time . ' ' . 
            'AND ServerFrames.State NOT IN (\'' . ServerFrame::CANCELLED . '\', \'' . ServerFrame::REMOVED . '\', \'' . ServerFrame::REPLACED . '\')';
        return $this->query($sql);
    }
}