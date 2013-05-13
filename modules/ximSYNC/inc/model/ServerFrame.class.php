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



ModulesManager::file('/inc/model/orm/ServerFrames_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
ModulesManager::file('/conf/synchro.conf', 'ximSYNC');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_FilterMacros.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_UnpublishOTF.class.php');
ModulesManager::file('/inc/persistence/datafactory.inc');
ModulesManager::file('/inc/log/XMD_log.class.php');
ModulesManager::file('/inc/xml/XmlBase.class.php');
ModulesManager::file('/inc/model/PublishingReport.class.php', 'ximSYNC'); 

/**
*	@brief Handles operations with ServerFrames.
*
*	A ServerFrame is the representation of a ChannelFrame in a Server.
*	This class includes the methods that interact with the Database.
*/

class ServerFrame extends ServerFrames_ORM {

	const PENDING = 'Pending';
	const DUE2IN = 'Due2In';
	const DUE2OUT = 'Due2Out';
	const PUMPED = 'Pumped';
	const OUT = 'Out';
	const CLOSING = 'Closing';
	const IN = 'In';
	const REPLACED = 'Replaced';
	const REMOVED = 'Removed';
	const CANCELED = 'Canceled';
	const DUE2INWITHERROR = 'Due2InWithError';
	const DUE2OUTWITHERROR = 'Due2OutWithError';

	public $initialStatus;
	public $errorStatus;
	public $finalStatus;
	public $finalStatusOk;
	public $finalStatusLimbo;
	public $finalStatusFailed;

	var $syncStatObj;

	function __construct($id = 0) {
		$this->initialStatus = array(ServerFrame::PENDING, ServerFrame::DUE2IN, ServerFrame::DUE2OUT);
		$this->errorStatus = array(ServerFrame::DUE2INWITHERROR, ServerFrame::DUE2OUTWITHERROR);
		$this->finalStatusOk = array(ServerFrame::IN);
		$this->finalStatusLimbo = array(ServerFrame::REPLACED, ServerFrame::REMOVED,
				ServerFrame::PUMPED, ServerFrame::OUT, ServerFrame::CLOSING);
		$this->finalStatusFailed = array(ServerFrame::CANCELED);
		$this->finalStatus = array_merge($this->finalStatusOk, $this->finalStatusLimbo, $this->finalStatusFailed);
                $this->publishingReport = new PublishingReport(); 
		parent::GenericData($id);
	}


	function update() {
		if(ModulesManager::isEnabled('ximPUBLISHtools')) {
			if($this->get('IdNodeFrame') > 0) {
				$searchFields = array(
					'IdSync' => $this->get('IdSync')
				);
				$updateFields = array(
					'State' => $this->get('State'),
					'Progress' => $this->get('Error') != '' ? '-1' : $this->publishingReport->progressTable[$this->get('State')]
				);
				$this->publishingReport->updateReportByField($updateFields, $searchFields);
			}
		}
		return parent::update();
	}



	/**
	*  Adds a row to ServerFrames table.
	*  @param int nodeId
	*  @param int server
	*  @param int dateUp
	*  @param string path
	*  @param string name
	*  @param int publishLinked
	*  @param int idNodeFrame
	*  @param int idChannelFrame
	*  @param int idServerFrame
	*  @param int idBatchUp
	*  @param int dateDown
	*  @param int size
	*  @return int|null
	*/

	function create($nodeId, $server, $dateUp, $path, $name, $publishLinked, $idNodeFrame,
			$idChannelFrame, $idBatchUp, $dateDown = NULL, $size = 0) {
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

		parent::add();
		$idServerFrame = $this->get('IdSync');

		if ($idServerFrame > 0) {
			if(ModulesManager::isEnabled('ximPUBLISHtools')) {
				$batch = new Batch($idBatchUp);
				$idSection = $batch->get('IdNodeGenerator');
				$sectionNode = new Node($idSection);
				$idParentServer = $sectionNode->getServer();
				$idPortalVersion = $batch->get('IdPortalVersion');
				$channelFrames = new ChannelFrame($idChannelFrame);
				$idChannel = $channelFrames->get('ChannelId');
				$this->publishingReport->create($idSection, $nodeId, $idChannel, $server, $idPortalVersion
					, time(), 'Pending', '20', $name, $path, $idServerFrame, $idBatchUp, $idParentServer);
			}
			return $idServerFrame;
		}

		$this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, "Creando el serverFrame");
		return NULL;
	}

	/**
	*  Gets all Servers from ServerFrames table.
	*  @param string simple
	*  @return array
	*/

	function getServers($mode = "simple") {

		$dbObj = new DB();
		$extraSql = ($mode == "simple") ? "" : ", Servers.Description, Servers.Url";
		$dbObj->Query(
				"SELECT DISTINCT(ServerFrames.IdServer)" . $extraSql . " FROM ServerFrames, Servers
				WHERE ServerFrames.IdServer = Servers.IdServer AND Servers.Enabled = 1");

		$servers = array();
		while (!$dbObj->EOF) {

			if ($mode == "simple") {

				$servers[] = $dbObj->GetValue("IdServer");
			} else {

				$servers[$dbObj->GetValue("IdServer")]['Description'] = $dbObj->GetValue(
						"Description");
				$servers[$dbObj->GetValue("IdServer")]['Url'] = $dbObj->GetValue("Url");
			}
			$dbObj->Next();
		}

		return $servers;
	}

	/**
	*  Gets the field IdSync from ServerFrames join NodeFrames which matching the values of nodeId and serverId.
	*  @param int nodeID
	*  @param int serverID
	*  @return int|null
	*/

	function getCurrentPublicatedFrame($nodeID, $serverID) {
		$dbObj = new DB();

		$sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE ServerFrames.IdNodeFrame =
			NodeFrames.IdNodeFrame AND NodeFrames.NodeId = $nodeID AND ServerFrames.IdServer = $serverID AND
			(ServerFrames.State='IN' OR ServerFrames.State='Pumped')";

		$dbObj->Query($sql);

		if ($dbObj->numRows > 0) {
			return $dbObj->GetValue("IdSync");
		}

		$this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, "ERROR getting publicated serverFrame");

		return NULL;
	}

	/**
	*  Gets the field IdSync from ServerFrames join NodeFrames which matching the value of nodeId and have a time interval that includes a given time.
	*  @param int nodeID
	*  @param int time
	*  @return int|null
	*/

	function getFrameOnDate($nodeID, $time = null) {
		$dbObj = new DB();
		if (!is_null($this->nodeID)) {
			if (!$time) {
				$time = time();
			}

			$sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE
					ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IdNode = $nodeID AND
					ServerFrames.DateUp > $time AND (ServerFrames.DateDown < $time OR ServerFrames.DateDown IS NULL)";
			$dbObj->Query($sql);

			return $dbObj->GetValue("IdSync");
		} else {
			$this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__,
					__FILE__, __LINE__, "ERROR", 8, "ERROR. Falta el NodeID");
		}
	}

	/**
	*  Gets the field IdSync from ServerFrames join NodeFrames which matching the value of nodeId and it is the newest.
	*  @param int nodeID
	*  @param int IdServer
	*  @return int|null
	*/

	function getLastFrame($nodeID, $IdServer) {
		$dbObj = new DB();
		if (!is_null($nodeID)) {
			$sql = "SELECT ServerFrames.IdSync FROM ServerFrames, NodeFrames WHERE
					ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IdNode = $nodeID AND
					ServerFrames.IdServer = $IdServer ORDER BY ServerFrames.DateUp DESC";
			$dbObj->Query($sql);

			return $dbObj->GetValue("IdSync");
		} else {
			$this->ServerFrameToLog(null, null, null, null, null, __CLASS__, __FUNCTION__,
					__FILE__, __LINE__, "ERROR", 8, "ERROR. Falta el NodeID");
		}
	}

	/**
	*  Creates the file which will be sended to the production Server.
	*  @param int frameID
	*  @return int|null
	 */

	function createSyncFile($frameID) {

		$path = SERVERFRAMES_SYNC_PATH . "/" . $frameID;

		$channelFrameId = $this->get('IdChannelFrame');
		$nodeFrameId = $this->get('IdNodeFrame');
		$server = $this->get('IdServer');
		$s = new Server($server);
		if ($s->get('otf') == '1') {
			$isServerOTF = true;
		} else {
			$isServerOTF = false;
		}

		$channelFrame = new ChannelFrame($channelFrameId);
		$channelId = $channelFrame->get('ChannelId');

		$nodeFrame = new NodeFrame($nodeFrameId);
		$idVersion = $nodeFrame->get('VersionId');
		$idNode = $nodeFrame->get('NodeId');
		$node = new Node($idNode);
		$isOTF = $node->getSimpleBooleanProperty('otf');
		$isHybrid = $node->getSimpleBooleanProperty('hybridColector');

		if (!($idNode > 0)) {
			XMD_Log::error("Unexisting node for serverframe $frameID");
			return NULL;
		}

		$pipeMng = new PipelineManager();

		$data['CHANNEL'] = $channelId;
		$data['SERVER'] = $server;

		$nodo = new Node($idNode);

		if ($nodo->get('IdNode') < 1) {
			return 0;
		}

		$transformer = $nodo->getProperty('Transformer');
		$data['TRANSFORMER'] = $transformer[0];

		if (!is_null($channelId)) {

			$channel = new Channel($channelId);

			if (!$isOTF || (!$isServerOTF && $isHybrid)) {
				$content = $pipeMng->getCacheFromProcessAsContent($idVersion, 'StrDocFromDexTToFinal',$data);

				$nodoTypeContent = $nodo->nodeType->get('Name');

				//only encoding the content if the node is not one of this 3.
				if (!(($nodoTypeContent == 'XimNewsImageFile') || ($nodoTypeContent == 'ImageFile') ||
						 ($nodoTypeContent == 'BinaryFile'))) {

					//Looking for idEncode for this server
					$db = new DB();
					$sql = "SELECT idEncode FROM Servers WHERE IdServer=" . $server;
					$db->Query($sql);
					$encodingServer = $db->GetValue("idEncode");

					XMD_Log::info("Codificando contenido a " . $encodingServer . 'con server=' . $server);
					$content = XmlBase::recodeSrc($content, $encodingServer);
				}

				if (FsUtils::file_put_contents($path, $content) === false) {
					return false;
				}

			} else {

				$data['PATH'] = $path;
				$data['NODENAME'] = $nodeFrame->get('Name');
				$data['NODEID'] = $idNode;

				$node = new Node($idNode);

				if (!($node->get('IdNode') > 0)) {

					// Node has deleted call to unpublish pipeline


					$viewUnpublishOTF = new View_UnpublishOTF();
					$content = $viewUnpublishOTF->transform(NULL, '', $data);

					if (FsUtils::file_put_contents($path, $content) === false) {
						return false;
					}
				} else {

					// OTF pipeline packs a tar.gz file, don't returns content
					$pipeMng->getCacheFromProcess($idVersion,
							'ximOTFTargz', $data);
				}
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

		// Its necessary to updating SyncFile size in BD


		$fileSize = filesize($path);

		return $fileSize;
	}

	/**
	*  Deletes the file associated to a ServerFrame.
	*  @return bool
	 */

	function deleteSyncFile() {

		if (!($this->get('IdSync')) > 0) {
			$this->ServerFrameToLog(null, null, null, $frameId, null, __CLASS__, __FUNCTION__,
					__FILE__, __LINE__, "ERROR", 8, "Deleting Sync files");
			return false;
		}

		return FsUtils::delete(SERVERFRAMES_SYNC_PATH . '/' . $this->get('IdSync'));
	}

	/**
	*  Gets all ServerFrames from a Batch.
	*  @param int batchId
	*  @param string batchColumn
	*  @param string mode
	*  @param array progress
	*  @param int limitCriteria
	*  @return array
	 */

	function getFramesOnBatch($batchId, $batchColumn, $mode = "simple", $progress = array(), $limitCriteria = null) {

		$dbObj = new DB();
		$sql = "SELECT ServerFrames.IdSync" . (($mode == 'simple') ? '' : ', ServerFrames.DateUp, ServerFrames.DateDown, ' .
				 'ServerFrames.FileSize, ServerFrames.State, ServerFrames.FileName, ServerFrames.PumperId, ServerFrames.IdServer, ServerFrames.RemotePath') .
				 " FROM ServerFrames, Batchs WHERE ServerFrames.IdBatchUp = Batchs.IdBatch AND Batchs.$batchColumn = $batchId";
		$dbObj->Query($sql);

		$frames = array();
		$progress['total']['totalBatchSize'] = 0;
		$progress['total']['totalBatchSizeCompleted'] = 0;
		$progress['total']['totalBatchCompleted'] = 0;
		$progress['total']['avgBatchSize'] = 0;
		$progress['total']['percentBatchSizeCompleted'] = 0;
		$progress['total']['percentBatchCompleted'] = 0;
		$progress['total']['totalBatch'] = 0;

		$serverIds = array('total');

		$iCounter = 0;
		$pageCounter = 0;

		while (!$dbObj->EOF) {

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
				$frames[$pageCounter][$idServer][$idSync]['DateDown'] = $dbObj->GetValue(
						"DateDown");
				$frames[$pageCounter][$idServer][$idSync]['State'] = $dbObj->GetValue("State");
				$frames[$pageCounter][$idServer][$idSync]['FileName'] = $dbObj->GetValue(
						"FileName");
				$frames[$pageCounter][$idServer][$idSync]['PumperId'] = $dbObj->GetValue(
						"PumperId");
				$frames[$pageCounter][$idServer][$idSync]['FileSize'] = $fileSize;
				$frames[$pageCounter][$idServer][$idSync]['RemotePath'] = $dbObj->GetValue(
						"RemotePath");

				if (!isset($progress[$idServer]['totalBatchSize'])) {

					$progress[$idServer]['totalBatchSize'] = 0;
				}
				if (!isset($progress[$idServer]['totalBatchSizeCompleted'])) {

					$progress[$idServer]['totalBatchSizeCompleted'] = 0;
				}
				if (!isset($progress[$idServer]['totalBatchCompleted'])) {

					$progress[$idServer]['totalBatchCompleted'] = 0;
				}
				if (!isset($progress[$idServer]['totalBatch'])) {

					$progress[$idServer]['totalBatch'] = 0;
				}
				if (!isset($progress[$idServer]['avgBatchSize'])) {

					$progress[$idServer]['avgBatchSize'] = 0;
				}
				if (!isset($progress[$idServer]['percentBatchSizeCompleted'])) {

					$progress[$idServer]['percentBatchSizeCompleted'] = 0;
				}
				if (!isset($progress[$idServer]['percentBatchCompleted'])) {

					$progress[$idServer]['percentBatchCompleted'] = 0;
				}

				$progress['total']['totalBatchSize'] += $fileSize;

				$progress['total']['totalBatchSizeCompleted'] += ($dbObj->GetValue("State") == 'In' ||
						 $dbObj->GetValue("State") == 'Out' || $dbObj->GetValue("State") == 'Removed' ||
						 $dbObj->GetValue("State") == 'Replaced' || $dbObj->GetValue("State") ==
						 'Pumped') ? $fileSize : 0;

				$progress['total']['totalBatchCompleted'] += ($dbObj->GetValue("State") == 'In' ||
						 $dbObj->GetValue("State") == 'Out' || $dbObj->GetValue("State") == 'Removed' ||
						 $dbObj->GetValue("State") == 'Replaced' || $dbObj->GetValue("State") ==
						 'Pumped') ? 1 : 0;

				$progress['total']['totalBatch'] ++;

				$progress[$idServer]['totalBatchSize'] += $fileSize;

				$progress[$idServer]['totalBatchSizeCompleted'] += ($dbObj->GetValue("State") == 'In' ||
						 $dbObj->GetValue("State") == 'Out' || $dbObj->GetValue("State") == 'Removed' ||
						 $dbObj->GetValue("State") == 'Replaced' || $dbObj->GetValue("State") ==
						 'Pumped') ? $fileSize : 0;

				$progress[$idServer]['totalBatchCompleted'] += ($dbObj->GetValue("State") == 'In' ||
						 $dbObj->GetValue("State") == 'Out' || $dbObj->GetValue("State") == 'Removed' ||
						 $dbObj->GetValue("State") == 'Replaced' || $dbObj->GetValue("State") ==
						 'Pumped') ? 1 : 0;

				$progress[$idServer]['totalBatch'] ++;
			}

			$dbObj->Next();
		}

		if ($mode != 'simple' && is_array($frames) && $progress['total']['totalBatch'] > 0) {

			foreach ($serverIds as $serverId) {
				$progress[$serverId]['avgBatchSize'] = round(
						$progress[$serverId]['totalBatchSize'] / $progress[$serverId]['totalBatch'],
						2);
				$progress[$serverId]['percentBatchCompleted'] = round(
						($progress[$serverId]['totalBatchCompleted'] * 100) / $progress[$serverId]['totalBatch'],
						2);

				if ($progress[$serverId]['totalBatchSize'] > 0) {
					$progress[$serverId]['percentBatchSizeCompleted'] = round(
							($progress[$serverId]['totalBatchSizeCompleted'] * 100) / $progress[$serverId]['totalBatchSize'],
							2);
				}
			}
		}

		return $frames;
	}

	/**
	*  Gets the number of ServerFrames which matching the value of pumperId and belong to a list of Servers.
	*  @param int nodeId
	*  @param array activeAndEnabledServers
	*  @return int
	*/

	function getUncompletedTasks($pumperID, $activeAndEnabledServers) {
		$dbObj = new DB();
		$servers = implode(',', $activeAndEnabledServers);

		$sql = "SELECT ServerFrames.IdSync FROM ServerFrames, Pumpers WHERE ServerFrames.PumperId = Pumpers.PumperId AND " .
				 "(ServerFrames.State = 'Due2In' OR ServerFrames.State = 'Due2Out') " . "AND ServerFrames.PumperId = $pumperID AND Pumpers.IdServer IN ($servers)";
		$dbObj->Query($sql);

		$n = $dbObj->numRows;

		$this->ServerFrameToLog(null, null, null, null, $pumperID, __CLASS__, __FUNCTION__,
				__FILE__, __LINE__, "INFO", 8, "Bombeador $pumperID tiene $n tareas incompletas");

		return $n;
	}

	/**
	*  Sets the State field from ServerFrames table which matching the value of pumperId.
	*  @param int pumperId
	*  @return unknown
	 */

	function rescueErroneous($pumperId) {
		$sql = "UPDATE ServerFrames SET State = LEFT(State,LENGTH(State)-LENGTH('witherror'))
				WHERE State IN ('Due2PumpedWithError','Due2OutWithError') WHERE PumperId = $pumperId";

		$dbObj = new DB();
		$dbObj->Execute($sql);
	}

	/**
	*  Gets the ServerFrame with a time interval that includes the current time, who match the values of nodeId and channelId, and whose State is in (in,due2in,pending).
	*  @param int nodeID
	*  @param int channelID
	*  @return int|null
	 */

	function getCurrent($nodeID, $channelID = null) {
		$now = mktime();

		$channelClause = "";
		if (!is_null($channelID)) {
			$channelClause = "AND c.ChannelId = " . $channelID . " ";
		}

		$node = new Node($nodeID);
		$serverID = $node->GetServer();
		if (!($serverID > 0)) {
			XMD_log::error(
					'Se ha intentado publicar un nodo que no esta contenido en un servidor: ' . $nodeID);
			return NULL;
		}
		$nodeServer = new Node($serverID);

		if (Config::getValue('PublishOnDisabledServers') == 1) {
			$physicalServers = $nodeServer->class->GetPhysicalServerList(true);
		} else {
			$physicalServers = $nodeServer->class->GetEnabledPhysicalServerList(true);
		}

		if (count($physicalServers) == 0) {
			XMD_log::info("[GETCURRENT]: No physical servers found. IdSync: none");
			return NULL;
		}

		$sql = sprintf(
				"SELECT IdSync " . "FROM ServerFrames sf " . "INNER JOIN ChannelFrames c ON c.IdChannelFrame = sf.IdChannelFrame " .
						 "WHERE c.NodeId = " . $nodeID . " " . $channelClause . "AND sf.DateUp < %s AND (sf.DateDown > %s OR sf.DateDown IS NULL) " .
						 "AND sf.State IN ('In', 'Due2In_', 'Due2In', 'Due2Pumped', 'Pumped', 'Replaced', 'Removed') " .
						 "AND sf.IdServer IN (%s)", $now, $now,
						implode(', ', $physicalServers));

		XMD_log::info("[GETCURRENT]: Getting current frame for node " . $nodeID);

		$dbObj = new DB();
		$dbObj->Query($sql);

		$result = ($dbObj->EOF) ? 'IdSync: none' : 'IdSync: ' . $dbObj->GetValue("IdSync");

		XMD_log::info("[GETCURRENT]: Result:  " . $result);

		return ($dbObj->EOF) ? NULL : $dbObj->GetValue("IdSync");
	}



	 /*
        Return complete server list, not only the server the last server
        */

        function getCompleteServerList($nodeId, $channelID=null){

                $extraCondition = "";
                if ($channelID != null){
                        $extraCondition = " AND cf.channelid=$channelID";
                }
                $sql = " SELECT distinct IdServer From ServerFrames sf 
inner join ChannelFrames cf on sf.idChannelFrame=cf.idChannelFrame
where cf.nodeid=$nodeId ";

                $sql .= $extraCondition;

                $dbObj = new DB();
                $dbObj->Query($sql);
                $list = array();
                while (!$dbObj->EOF) {
                        $list[] = $dbObj->GetValue("IdServer");
                        $dbObj->Next();
                }

                return $list;
        }



	/**
	*  Gets the fields RemotePath and FileName from ServerFrames which matching the values of Server, NodeFrame and Channel.
	*  @param int frameId
	*  @param int channelID
	*  @param int serverID
	*  @return string|null
	*/

	function getUrlLastPublicatedNews($frameId, $channelID, $serverID) {

		$now = mktime();

		$sql = "SELECT IdSync, RemotePath, FileName FROM ServerFrames WHERE  ";
		$sql .= " IdChannelFrame = $channelID AND DateUp < $now ";
		$sql .= " AND ServerFrames.IdServer = $serverID ";
		$sql .= " AND IdNodeFrame = $frameId ";
		$sql .= " AND (DateDown > $now OR DateDown IS NULL) AND State = 'In'";

		$dbObj = new DB();
		$dbObj->Query($sql);
		$path = $dbObj->GetValue("RemotePath");
		$filename = $dbObj->GetValue("FileName");

		if ($filename)
			return $path . "/" . $filename;

		return null;
	}

	/**
	*  Gets the field IdServer from ServerFrames table which matching the value of idNodeFrame.
	*  @param int idNodeFrame
	*  @return array|null
	*/

	function getServerListOnFrame($idNodeFrame) {

		$result = $this->find('IdServer', 'IdNodeFrame = '.$idNodeFrame, array(),
				MONO);

		if (!(sizeof($result) > 0)) {
			return NULL;
		}

		return $result;
	}

	/**
	*  Deletes a row from ServerFrames and updates the values of ServerFrames related fields in Batchs table.
	*  @return unknown
	*/

	function delete() {

		// Updating num. serverFrames in batch

		$idBatch = $this->get('IdBatchUp');
		$state = $this->get('State');

		$batch = new Batch($idBatch);
		$batch->set('ServerFramesTotal', $batch->get('ServerFramesTotal') - 1);

		if (!strpos($state, 'ERROR')) {
			$batch->set('ServerFramesSucess', $batch->get('ServerFramesSucess') - 1);
		} else {
			$batch->set('ServerFramesError', $batch->get('ServerFramesError') - 1);
		}

		$batch->update();

		// Deleting serverFrame


		parent::delete();
	}

	/**
	*  Checks whether a ServerFrame exists for all of active servers
	*  @param int idNodeFrame
	*  @param array physicalServers
	*  @return bool
	 */

	function existFrameInAllActiveServer($idNodeFrame, $physicalServers) {
		$now = mktime();

		if (is_null($idNodeFrame)) {
			XMD_log::error("void nodeframe");
			return false;
		}

		$physicalServersString = "";
		$i = 0;
		if (is_array($physicalServers) && count($physicalServers) > 0) {
			foreach ($physicalServers as $physicalServer) {
				if ($i > 0)
					$physicalServersString .= ", ";
				$physicalServersString .= $physicalServer;
				$i ++;
			}
		}

		$sql = sprintf(
				"SELECT s.IdServer" . " FROM Servers s" . " LEFT JOIN ServerFrames sf ON s.IdServer = sf.IdServer" .
						 " AND sf.IdNodeFrame = %s AND sf.DateUp < %s" . " AND (sf.DateDown > %s OR sf.DateDown IS NULL)" .
						 " AND sf.State IN ('In', 'Due2In_', 'Due2In', 'Due2Pumped', 'Pumped', 'Replaced', 'Removed')" .
						 " WHERE s.IdServer IN (" . $physicalServersString . ") AND sf.IdSync IS NULL",
						$idNodeFrame, $now, $now);
		$dbObj = new DB();
		$dbObj->Query($sql);

		return ($dbObj->EOF) ? true : false;
	}

	/**
	*  Logs the activity of the ServerFrame.
	*  @param int batchId
	*  @param int nodeFrameId
	*  @param int channelFrameId
	*  @param int serverFrameId
	*  @param int pumperId
	*  @param string class
	*  @param string method
	*  @param string file
	*  @param int line
	*  @param string type
	*  @param int level
	*  @param string comment
	*  @param int doInsertSql
	*/

	function ServerFrameToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
			$class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false) {

		if (!isset($this->syncStatObj)) {

			$this->syncStatObj = new SynchronizerStat();
		}

		$this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId,
				$pumperId, $class, $method, $file, $line, $type, $level, $comment, $doInsertSql);

	}

	/**
	*  Gets the rows from ServerFrames which matching the value of idPumper.
	*  @param int idPumper
	*  @return array
	*/

	function getPublicableNodesForPumper($idPumper) {
		$query = "SELECT s.* FROM ServerFrames s, Batchs b WHERE s.IdBatchUp = b.IdBatch AND (s.State = 'Due2In' OR " .
				 "s.State = 'Due2Out' OR (s.State = 'Pumped' AND b.State = 'Closing')) " . "AND s.PumperId = $idPumper LIMIT 1";
		return $this->query($query);
	}
}
?>
