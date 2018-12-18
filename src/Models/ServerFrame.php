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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\ServerFramesOrm;
use Ximdex\NodeTypes\HTMLDocumentNode;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Runtime\Db;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * @brief Handles operations with ServerFrames
 *
 * A ServerFrame is the representation of a ChannelFrame in a Server
 * This class includes the methods that interact with the Database
 */
class ServerFrame extends ServerFramesOrm
{
    const PENDING = 'Pending';
    const DUE2IN = 'Due2In';
    const DUE2IN_ = 'Due2In_';
    const DUE2OUT = 'Due2Out';
    const DUE2OUT_ = 'Due2Out_';
    const PUMPED = 'Pumped';
    const OUT = 'Out';
    const IN = 'In';
    const REPLACED = 'Replaced';
    const REMOVED = 'Removed';
    const CANCELLED = 'Canceled';
    const DUE2INWITHERROR = 'Due2InWithError';
    const DUE2OUTWITHERROR = 'Due2OutWithError';
    const OUTDATED = 'Outdated';
    const DELAYED = 'Delayed';
    
    // Error levels
    const ERROR_LEVEL_SOFT = 1;
    const ERROR_LEVEL_HARD = 2;
    
    // Group of status
    const FINAL_STATUS = [self::IN, self::OUT, self::REMOVED, self::REPLACED, self::CANCELLED, self::OUTDATED, self::DELAYED
        , self::DUE2INWITHERROR, self::DUE2OUTWITHERROR];
    const FINAL_STATUS_OUT = [self::OUT, self::REMOVED, self::REPLACED, self::CANCELLED, self::OUTDATED, self::DELAYED];
    const FINAL_STATUS_IN = [self::IN, self::REMOVED, self::REPLACED, self::CANCELLED, self::OUTDATED, self::DELAYED];
    const PUBLISHING_STATUS = [self::PENDING, self::DUE2IN_, self::DUE2IN, self::PUMPED, self::IN, self::DELAYED, self::DUE2INWITHERROR
        , self::DUE2OUTWITHERROR];

    public $initialStatus;
    public $errorStatus;
    public $finalStatus;
    public $finalStatusOk;
    public $finalStatusLimbo;
    public $finalStatusFailed;

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
            ServerFrame::OUT
        );
        $this->finalStatusFailed = array(
            ServerFrame::CANCELLED
        );
        $this->finalStatus = array_merge($this->finalStatusOk, $this->finalStatusLimbo, $this->finalStatusFailed);
        parent::__construct($id);
    }
    
    public function set($attribute, $value)
    {
        if ($attribute == 'State' and $this->State != $value) {
            Logger::debug('Changing state for server frame: ' . $this->get('IdSync') . ' from ' . $this->get('State') . ' to ' . $value);
        }
        parent::set($attribute, $value);
    }

    /**
     * Adds a row to ServerFrames table
     * 
     * @param int $nodeId
     * @param int $server
     * @param int $dateUp
     * @param string $path
     * @param string $name
     * @param int $publishLinked
     * @param int $idNodeFrame
     * @param string $idChannel
     * @param int $idChannelFrame
     * @param int $idBatchUp
     * @param int $idPortalFrame
     * @param int $dateDown
     * @param int $size
     * @param bool $cache
     * @param int $idbatchDown
     * @return int|null|bool
     */
    public function create(int $nodeId, int $server, int $dateUp, string $path, string $name, int $publishLinked, int $idNodeFrame
        , ?int $idChannel, int $idChannelFrame, int $idBatchUp, int $idPortalFrame, int $dateDown = null, int $size = 0, bool $cache = true
        , int $idbatchDown = null)
    {
        $this->set('IdServer', $server);
        $this->set('DateUp', $dateUp);
        $this->set('DateDown', $dateDown);
        $this->set('State', ServerFrame::PENDING);
        $this->set('ErrorLevel', null);
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
        $this->set('IdPortalFrame', $idPortalFrame);
        if ($idChannel) {
            $this->set('ChannelId', $idChannel);
        }
        $this->set('IdBatchDown', $idbatchDown);
        $id = parent::add();
        if (! $id) {
            Logger::error('Creating server frame');
            return null;
        }
        return $id;
    }

    /**
     * Gets all Servers from ServerFrames table.
     * 
     * @param string simple
     * @return array
     */
    public function getServers($mode = "simple")
    {
        $dbObj = new Db();
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
     * Gets the field IdSync from ServerFrames join NodeFrames which matching the values of nodeId and serverId
     * 
     * @param int nodeID
     * @param int serverID
     * @return int|null
     */
    public function getCurrentPublicatedFrame($nodeID, $serverID)
    {
        $dbObj = new Db();
        $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE ServerFrames.IdNodeFrame =
			NodeFrames.IdNodeFrame AND NodeFrames.NodeId = $nodeID AND ServerFrames.IdServer = $serverID AND
			(ServerFrames.State = '" . ServerFrame::IN . "' OR ServerFrames.State = '" . ServerFrame::PUMPED . "')";
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            return $dbObj->GetValue("IdSync");
        }
        Logger::error('Getting publicated serverFrame');
        return null;
    }

    /**
     * Gets the field IdSync from ServerFrames join NodeFrames which matching the value of nodeId and it is the newest
     * 
     * @param int nodeID
     * @param int IdServer
     * @return int|null
     */
    function getLastFrame($nodeID, $IdServer)
    {
        $dbObj = new Db();
        if (! is_null($nodeID)) {
            $sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE
					ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IdNode = $nodeID AND
					ServerFrames.IdServer = $IdServer ORDER BY ServerFrames.DateUp DESC";
            $dbObj->Query($sql);
            return $dbObj->GetValue("IdSync");
        }
        Logger::error('NodeID is needed');
    }

    /**
     * Creates the file which will be sent to the production Server
     * 
     * @param $frameID
     * @param bool $cache
     * @return boolean|null|int
     */
    public function createSyncFile($frameID, bool $cache = true)
    {
        $path = SERVERFRAMES_SYNC_PATH . "/" . $frameID;
        $channelFrameId = $this->get('IdChannelFrame');
        $nodeFrameId = $this->get('IdNodeFrame');
        $server = $this->get('IdServer');
        $channelFrame = new ChannelFrame($channelFrameId);
        if (! $channelFrame->get('IdChannelFrame')) {
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
        if (! $idNode) {
            Logger::error("Unexisting node for serverframe $frameID");
            return false;
        }
        $node = new Node($idNode);
        if (!$node->GetID()) {
            return false;
        }
        $data = [];
        $data['CHANNEL'] = $channelId;
        $data['SERVER'] = $server;
        if (! $cache) {
            $data['DISABLE_CACHE'] = true;
        }
        else {
            $data['DISABLE_CACHE'] = App::getValue('DisableCache');
        }
        $transformer = $node->getProperty('Transformer');
        $data['TRANSFORMER'] = $transformer[0];
        $data['NODEID'] = $idNode;
        $transition = new Transition();
        if (! is_null($channelId) && $node->nodeType->GetIsStructuredDocument()) {
            if ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                $channel = new Channel($channelId);
                if ($channel->getRenderType() == HTMLDocumentNode::MODE_INDEX) {
                    $process = 'PublishXIF';
                } else {
                    $process = 'PublishHTML';
                }
            } else {
                $process = 'PublishXML';
            }
            try {
                $file = $transition->process($process, $data, $idVersion);
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
                return false;
            }
            if ($file === null) {
                return null;
            }
            $content = FsUtils::file_get_contents($file);
            
            // Only encoding the content if the node is not one of this 3
            $nodeTypeContent = $node->nodeType->get('Name');
            if ($nodeTypeContent != 'ImageFile' and $nodeTypeContent != 'BinaryFile') {
                
                // Looking for idEncode for this server
                $db = new \Ximdex\Runtime\Db();
                $sql = 'SELECT idEncode FROM Servers WHERE IdServer = ' . $server;
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
            if ($node->nodeType->get('Name') == 'XslTemplate') {
                $data['REPLACEMACROS'] = 'yes';
            }
            // $file = $pipeMng->getCacheFromProcess($idVersion, 'NotStrDocToFinal', $data);
            try {
                $file = $transition->process('ToFinal', $data, $idVersion);
            } catch (\Exception $e) {
                Logger::error($e->getMessage());
                return false;
            }
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
        if (! $this->get('IdSync')) {
            return false;
        }
        if (! FsUtils::file_exists(SERVERFRAMES_SYNC_PATH . '/' . $this->get('IdSync'))) {
            return null;
        }
        return FsUtils::delete(SERVERFRAMES_SYNC_PATH . '/' . $this->get('IdSync'));
    }

    /**
     * Gets the number of ServerFrames which matching the value of pumperId and belong to a list of Servers
     * 
     * @param int nodeId
     * @param array activeAndEnabledServers
     * @return int
     */
    public function getUncompletedTasks($pumperID, $activeAndEnabledServers)
    {
        $servers = implode(',', $activeAndEnabledServers);
        
        // Use count and delete Pumpers table
        $sql = 'SELECT count(ServerFrames.IdSync) as total FROM ServerFrames '
            . 'WHERE (ServerFrames.State = \'' . ServerFrame::DUE2IN . '\' OR ServerFrames.State = \'' . ServerFrame::DUE2OUT . '\') ' 
            . 'AND ServerFrames.PumperId = ' . $pumperID . ' AND ServerFrames.IdServer IN (' . $servers . ')';
        $dbObj = new Db();
        $dbObj->Query($sql);
        $n = (int) $dbObj->GetValue('total');
        Logger::debug('Pumper ' . $pumperID . ' contain ' . $n . ' incomplete tasks');
        return $n;
    }

    /**
     * Sets the State field from ServerFrames table which matching the value of pumperId
     * 
     * @param int pumperId
     */
    public function rescueErroneous($pumperId)
    {
        $sql = "UPDATE ServerFrames SET State = LEFT(State, LENGTH(State) - LENGTH('witherror'))
				WHERE State IN ('" . ServerFrame::DUE2INWITHERROR . "', '" . ServerFrame::DUE2OUTWITHERROR 
            . "') AND PumperId = $pumperId";
        $dbObj = new Db();
        $dbObj->Execute($sql);
    }

    /**
     * Gets the ServerFrame with a time interval that includes the current time, who match the values of nodeId
     * and channelId, and whose State is in (pending, in, due2in, due2in_, pumped)
     *
     * @param int $nodeID
     * @param int $channelID
     * @param int $idServer
     * @return int|null
     */
    public function getCurrent($nodeID, $channelID = null, int $idServer = null)
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
            return null;
        }
        if ($idServer) {
            $physicalServers = [$idServer];
        } else {
            $nodeServer = new Node($serverID);
            if (App::getValue('PublishOnDisabledServers') == 1) {
                $physicalServers = $nodeServer->class->GetPhysicalServerList(true);
            } else {
                $physicalServers = $nodeServer->class->GetPhysicalServerList(true, true);
            }
            if (count($physicalServers) == 0) {
                Logger::warning("[GETCURRENT]: No physical servers found. IdSync: none");
                return null;
            }
        }
        $sql = sprintf("SELECT IdSync " . "FROM ServerFrames sf " . "INNER JOIN ChannelFrames c ON c.IdChannelFrame = sf.IdChannelFrame " 
            . "WHERE c.NodeId = " . $nodeID . " " . $channelClause . "AND sf.DateUp < %s AND (sf.DateDown > %s OR sf.DateDown IS NULL) " 
            . "AND sf.State IN ('" . ServerFrame::PENDING . "', '" . ServerFrame::IN . "', '" . ServerFrame::DUE2IN . "', '" 
            . ServerFrame::DUE2IN_ . "', '" . ServerFrame::PUMPED . "') " 
            . "AND sf.IdServer IN (%s)", $now, $now, implode(', ', $physicalServers)) . ' ORDER BY IdSync DESC LIMIT 1';
        Logger::debug("[GETCURRENT]: Getting current frame for node " . $nodeID);
        $dbObj = new Db();
        $dbObj->Query($sql);
        $result = ($dbObj->EOF) ? 'IdSync: none' : 'IdSync: ' . $dbObj->GetValue("IdSync");
        Logger::debug("[GETCURRENT]: Result:  " . $result);
        return ($dbObj->EOF) ? null : $dbObj->GetValue("IdSync");
    }

    /**
     * Return complete server list, not only the server the last server
     * 
     * @param $nodeId
     * @param $channelID
     * @return null|string[]
     */
    public function getCompleteServerList($nodeId, $channelID = null)
    {
        $extraCondition = "";
        if ($channelID != null) {
            $extraCondition = " AND cf.channelid = $channelID";
        }
        $sql = "SELECT distinct IdServer From ServerFrames sf inner join ChannelFrames cf on sf.idChannelFrame = cf.idChannelFrame";
        $sql .= " where cf.nodeid = $nodeId ";
        $sql .= $extraCondition;
        $dbObj = new Db();
        $dbObj->Query($sql);
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->GetValue("IdServer");
            $dbObj->Next();
        }
        return $list;
    }

    /**
     * Gets the fields RemotePath and FileName from ServerFrames which matching the values of Server, NodeFrame and Channel
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
        $dbObj = new Db();
        $dbObj->Query($sql);
        $path = $dbObj->GetValue("RemotePath");
        $filename = $dbObj->GetValue("FileName");
        if ($filename) {
            return $path . "/" . $filename;
        }
        return null;
    }

    /**
     * Gets the field IdServer from ServerFrames table which matching the value of idNodeFrame
     * 
     * @param int idNodeFrame
     * @return array|null
     */
    public function getServerListOnFrame($idNodeFrame)
    {
        $result = $this->find('IdServer', 'IdNodeFrame = ' . $idNodeFrame, array(), MONO);
        if (! (sizeof($result) > 0)) {
            return null;
        }
        return $result;
    }

    /**
     * Deletes a row from ServerFrames and updates the values of ServerFrames related fields in Batchs table
     */
    public function delete()
    {
        // Updating num. serverFrames in batch
        $idBatch = $this->get('IdBatchUp');
        $state = $this->get('State');
        $batch = new Batch($idBatch);
        $batch->set('ServerFramesTotal', $batch->get('ServerFramesTotal') - 1);
        if (! strpos($state, 'ERROR')) {
            $batch->set('ServerFramesSuccess', $batch->get('ServerFramesSuccess') - 1);
        } else {
            if ($this->get('ErrorLevel') == self::ERROR_LEVEL_HARD) {
                $batch->set('ServerFramesFatalError', $batch->get('ServerFramesFatalError') - 1);
            } else {
                $batch->set('ServerFramesTemporalError', $batch->get('ServerFramesTemporalError') - 1);
            }
        }
        $batch->update();
        
        // Deleting serverFrame
        parent::delete();
    }

    /**
     * Gets the rows from ServerFrames which matching the value of idPumper
     * 
     * @param int idPumper
     * @return array
     */
    public function getPublishableNodesForPumper($idPumper)
    {
        $query = "SELECT sf.* FROM ServerFrames sf, Batchs b WHERE (sf.IdBatchUp = b.IdBatch OR sf.IdBatchDown = b.IdBatch) AND (sf.State = '" 
            . ServerFrame::DUE2IN . "' OR " . "sf.State = '" . ServerFrame::DUE2OUT . "' OR (sf.State = '" . ServerFrame::PUMPED 
            . "' AND b.State = '" . Batch::CLOSING . "')) " . "AND sf.PumperId = $idPumper ORDER BY sf.Retry LIMIT 1";
        return $this->query($query);
    }

    public function getPublicationQueue($idServer)
    {
        $dbObj = new Db();
        $sql = "SELECT n.idnode, n.path,v.version, n.name, dateup, state, filesize, concat(v.`Version`, '.', v.`SubVersion`) ";
        $sql .= "FROM ServerFrames sf INNER JOIN NodeFrames nf using (idnodeframe) INNER JOIN Nodes n ON n.idnode = nf.nodeid ";
        $sql .= "INNER JOIN  Versions v ON v.Idnode = n.idnode INNER JOIN Servers s ON sf.IdServer = s.IdServer AND s.IdNode = $idServer ";
        $sql .= "where subversion = 0 and state not in ('" . ServerFrame::REMOVED . "', '" . ServerFrame::REPLACED . "', '" 
            . ServerFrame::CANCELLED . "') ";
        $sql .= "and Not exists (select idversion from Versions v2 where v2.idversion <> v.idversion and v.idnode = v2.idnode and v2.version > v.version) ";
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
        return null;
    }
    
    /**
     * @return int|null
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
     * @param int $idServer
     * @return array|bool
     */
    public function getFramesOnDate(int $nodeId, int $time, int $idServer = null)
    {
        $sql = 'SELECT ServerFrames.IdSync, ServerFrames.IdServer FROM ServerFrames, NodeFrames ' . 
            'WHERE ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.NodeId = ' . $nodeId . ' AND ' . 
	        'ServerFrames.DateUp <= ' . $time . ' AND (ServerFrames.DateDown >= ' . $time . ' OR ServerFrames.DateDown IS NULL) ' . 
	        'AND ServerFrames.State NOT IN (\'' . ServerFrame::CANCELLED . '\', \'' . ServerFrame::REMOVED . 
            '\', \'' . ServerFrame::REPLACED . '\')';
        if ($idServer) {
            $sql .= ' AND ServerFrames.IdServer = ' . $idServer;
        } else {
            $sql .= ' ORDER BY ServerFrames.IdServer';
        }
        return $this->query($sql, MULTI, null, true);
    }
    
    /**
     * Get all server frames that will be activated after a given timestamp and specified node ID 
     * 
     * @param int $nodeId
     * @param int $time
     * @return array|bool
     */
    public function getFutureFramesForDate(int $nodeId, int $time)
    {
        $sql = 'SELECT IdSync FROM ServerFrames WHERE NodeId = ' . $nodeId . ' AND DateUp > ' . $time . ' '
            . 'AND State NOT IN (\'' . ServerFrame::CANCELLED . '\', \'' . ServerFrame::REMOVED . '\', \'' . ServerFrame::REPLACED . '\')';
        return $this->query($sql, MULTI, null, true);
    }
    
    /**
     * Retrieve a list of servers keys in currently active server frames (no final states)
     * 
     * @throws \Exception
     * @return array
     */
    public static function serversInActiveServerFrames() : array
    {
        $excludeStates = ServerFrame::FINAL_STATUS;
        $sql = 'SELECT IdServer FROM ServerFrames WHERE State NOT IN (\'' . implode('\', \'', $excludeStates) . '\') GROUP BY IdServer';
        $dbObj = new Db();
        if ($dbObj->Query($sql) === false) {
            throw new \Exception($dbObj->getDesErr());
        }
        $servers = [];
        while (!$dbObj->EOF) {
            $servers[] = $dbObj->GetValue('IdServer');
            $dbObj->Next();
        }
        return $servers;
    }
    
    /**
     * Retrieve a total of server frames matching any criteria
     * 
     * @param array $includeStates
     * @param array $excludeStates
     * @param bool $active
     * @param int $serverId
     * @param int $channelId
     * @throws \Exception
     * @return int
     */
    public static function countServerFrames(array $includeStates = [], array $excludeStates = [], int $errorType = null
        , bool $active = true, int $serverId = null, int $channelId = null) : int
    {
        $sql = 'SELECT COUNT(IdSync) AS total FROM ServerFrames WHERE TRUE';
        if ($includeStates) {
            $sql .= ' AND State IN (\'' . implode('\', \'', $includeStates) . '\')';
        }
        if ($excludeStates) {
            $sql .= ' AND State NOT IN (\'' . implode('\', \'', $excludeStates) . '\')';
        }
        if ($errorType) {
            $sql .= ' AND ErrorLevel = ' . $errorType;
        }
        if ($active) {
            $sql .= ' AND (DateUp <= UNIX_TIMESTAMP() OR DateDown <= UNIX_TIMESTAMP())';
        }
        if ($serverId) {
            $sql .= ' AND IdServer = ' . $serverId;
        }
        if ($channelId) {
            $sql .= ' AND ChannelId = ' . $channelId;
        } elseif ($channelId === 0) {
            
            // This case specify a null channel ID in server frames
            $sql .= ' AND ChannelId IS NULL';
        }
        $dbObj = new Db();
        if ($dbObj->Query($sql) === false) {
            throw new \Exception($dbObj->getDesErr());
        }
        if ($dbObj->numRows) {
            return (int) $dbObj->GetValue('total');
        }
        return 0;
    }
    
    /**
     * Cancel the server frame with force option (all states)
     * 
     * @param bool $force
     * @throws \Exception
     */
    public function cancel(bool $force = true) : void
    {
        if (!$this->IdSync) {
            throw new \Exception('No IdSync value sent to cancel the server frame');
        }
        if ($this->State == ServerFrame::CANCELLED) {
            return;
        }
        if (!$force) {
            if (!in_array($this->State, [ServerFrame::PENDING, ServerFrame::DUE2IN_, ServerFrame::DUE2OUT])) {
                
                // Not in pending state
                return;
            }
            if ($this->State == ServerFrame::IN and $this->DateDown) {
                
                // Not in expiration state
                return;
            }
        }
        $this->set('State', ServerFrame::CANCELLED);
        $this->set('ErrorLevel', null);
        if ($this->update() === false) {
            throw new \Exception('Cannot cancel the server frame ' . $this->IdSync);
        }
        $this->deleteSyncFile();
    }
}
