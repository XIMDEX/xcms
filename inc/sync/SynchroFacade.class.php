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



	

	if (!defined ("XIMDEX_ROOT_PATH")) {
		define("XIMDEX_ROOT_PATH", realpath (dirname (__FILE__) . "/../../"));
	}

	require_once(XIMDEX_ROOT_PATH . '/inc/model/node.php');
	require_once(XIMDEX_ROOT_PATH . '/inc/model/Server.class.php');

	if (ModulesManager::isEnabled('ximSYNC')) {
  	   ModulesManager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
  	   ModulesManager::file('/inc/manager/NodeFrameManager.class.php', 'ximSYNC');
  	   ModulesManager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
	} else {
		require_once(XIMDEX_ROOT_PATH . '/inc/sync/synchro.php');
		require_once(XIMDEX_ROOT_PATH . '/inc/sync/SyncManager.class.php');
	}

	class SynchroFacade{ 
		
		function getServer($idTargetNode, $idTargetChannel, $idServer) {
			
			$targetNode = new Node(($idTargetNode));
			if (!($targetNode->get('IdNode') > 0)) {
				XMD_Log::error(_('No correct node received'));
				return NULL;
			}

			$server = new Server($idServer);
			if (!($server->get('IdServer') > 0)) {
				XMD_Log::error(_('No correct server received'));
				return NULL;
			}
			
			if (ModulesManager::isEnabled('ximSYNC')) {

				// Looking for a possible frame for the destiny channel
				
				$targetFrame = new ServerFrame();
				$frameID = $targetFrame->getCurrent($idTargetNode, $idTargetChannel); // esto es un idSync
				
				if (!($frameID > 0)) {
					XMD_Log::error(_("No target frame available")." FACADE - $idTargetNode - $idTargetChannel - $idServer");
					return NULL;
				}
				
				// Calculating physical origin and destiny servers
				$physicalTargetServers = $targetFrame->getCompleteServerList($idTargetNode, $idTargetChannel);
                
                if (count($physicalTargetServers) == 0) {
					XMD_Log::error(_("No physical target server available"));
					return NULL;
				}

				// Gets only enabled servers

				if (in_array($idServer, $physicalTargetServers)) {
					return $idServer;
				}
				
				return $physicalTargetServers[rand(0,sizeof($physicalTargetServers)-1)];
			}
			
			$syncro = new Synchronizer($idTargetNode);
			$idFrame = $syncro->GetCurrentFrame($idTargetChannel);
			
			if (!($idFrame > 0)) {
				XMD_Log::error(_("Not target frame available")." FACADE (2)");
				return NULL;
			}
			
			$physicalTargetServers = $syncro->GetServerListOnFrame($idFrame, $idTargetChannel);
			
			if (count($physicalTargetServers) == 0) {
				XMD_Log::info(_("No physical target server available"));
				return NULL;
			}
			
			if (in_array($idServer, $physicalTargetServers)) {
				return $idServer;
			}
			
			return $physicalTargetServers[rand(0,sizeof($physicalTargetServers)-1)];
		}

		/**
		 * Delete frames belongs to unexisting physical server
		 *
		 * @param unknown_type $physicalServerID
		 */
		function removeFromUnexistingServer($physicalServerID) {
			if (ModulesManager::isEnabled('ximSYNC')) {
				$table = 'ServerFrames';
			} else {
				$table = 'Synchronizer';
			}

			$dbObj = new DB();
			$sql = "DELETE FROM $table WHERE IdServer = $physicalServerID";
			$dbObj->Execute($sql);

			if ($dbObj->numRows > 0) {
				XMD_Log::info(sprinf(_("Deleting frames in table %s - server %s"), $table, $physicalServerID));
			} else {
				XMD_Log::info(sprinft(_("No deletion in table %s - server %s"), $table, $physicalServerID));
			}

		}

		/**
		 * Return pending tasks for a given node
		 *
		 * @param unknown_type $nodeID
		 * @return unknown
		 */
		function getPendingTasksByNode($nodeID) {
			if (is_null($nodeID)) {
				XMD_log::info("Void node");
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {
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
		 * @param unknown_type $nodeID
		 * @return boolean
		 */
		function isNodePublished($nodeID) {
			if (is_null($nodeID)) {
				XMD_log::info("Void node");
				return false;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {
				$nodeFrame = new NodeFrame();
				$result = $nodeFrame->getPublishedId($nodeID);
			} else {
				$sync = new Synchronizer($nodeID);
				$result = $sync->IsPublished();
			}
			return (boolean) $result;
		}
		
		/**
		 * Return if node is published in all of active servers
		 *
		 * @param unknown_type $nodeID
		 * @return unknown
		 */
		function isNodePublishedInAllActiveServer($nodeID) {
			if (is_null($nodeID)) {
				XMD_log::info("Void node");
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {
				$nodeFrame = new NodeFrame();
				$nodeFrameId = $nodeFrame->getNodeFrameByNode($nodeID);
				
				$node = new Node($nodeID);
				$serverID = $node->GetServer();
				$nodeServer = new Node($serverID);
				if (\App::getValue( 'PublishOnDisabledServers') == 1) {
					$physicalServers = $nodeServer->class->GetPhysicalServerList(true);
				} else {
					$physicalServers = $nodeServer->class->GetEnabledPhysicalServerList(true);
				}
				
				$serverFrame = new ServerFrame();
				$result = $serverFrame->existFrameInAllActiveServer($nodeFrameId, $physicalServers);
			} else {
				$sync = new Synchronizer($nodeID);
				$result = $sync->IsPublished();
			}

			if (!$result || is_null($result)) {
				return false;
			}

			return true;
		}
		
		/**
		 * Delete all tasks by node
		 *
		 * @param unknown_type $nodeID
		 * @param boolean $unPublish --> don't delete task, set it to Due2Out state
		 * @return unknown
		 */
		function deleteAllTasksByNode($nodeID, $unPublish = false) {
			if (is_null($nodeID)) {
				XMD_log::info(_("No existing node with id $nodeID"));
				return NULL;
			}

			if (!is_null($unPublish)) {
				XMD_log::info(_("Unpublish documents before deleting node"));
			} else {
				XMD_log::info(_("Delete node and keep documents published"));
			}

			$deleteIDs = $this->getAllTaskByNode($nodeID);
					
			foreach ($deleteIDs as $id) {
				if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * Enter description here...
		 *
		 * @param unknown_type $nodeID
		 * @return unknown
		 */
		function getAllTaskByNode ($nodeID) {
			if (is_null($nodeID)) {
				XMD_log::info(_("No existing node with id $nodeID"));
				return array();
			}
			
			$node = new Node($nodeID);

			$pendingTasks = array();
			$publishedTasks = array();
			$childList = $node->GetChildren();
			$workFlowSlaves = $node->GetWorkFlowSlaves();
			$workFlowSlaves = sizeof($workFlowSlaves) > 0 ? $workFlowSlaves : array();

			if ($childList) {
				foreach($childList as $child) {
					$childNode = new Node($child);
					$childList = array_merge($childList, $childNode->TraverseTree(), $workFlowSlaves);
				}

				if (sizeof($childList) > 0) {
					foreach($childList as $nodeID) {
						$pendingTasks =  array_merge($pendingTasks, self::getPendingTasksByNode($nodeID));
						if($result = self::isNodePublished($nodeID)) {
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
		 * @return int / NULL
		 */
		function getFrameState($idFrame){

			if (is_null($idFrame)) {
				XMD_Log::error(_('Void param idFrame'));
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * @return int / NULL
		 */
		function getFramePath($idFrame){

			if (is_null($idFrame)) {
				XMD_Log::error(_('Void param idFrame'));
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * @return int / NULL
		 */
		function getFrameName($idFrame){

			if (is_null($idFrame)) {
				XMD_Log::error(_('Void param idFrame'));
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * @return int / NULL
		 */
		function getFrameServer($idFrame){

			if (is_null($idFrame)) {
				XMD_Log::error(_('Void param idFrame'));
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * @return int / NULL
		 */
		function getFrameChannel($idFrame){

			if (is_null($idFrame)) {
				XMD_Log::error(_('Void param idFrame'));
				return NULL;
			}

			if (ModulesManager::isEnabled('ximSYNC')) {

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
		 * @param unknown_type $nodeID
		 * @param unknown_type $serverid
		 * @param unknown_type $channel
		 * @return unknown
		 */
	function getLastPublishedNews($nodeID, $serverid, $channel) {
		if (is_null($nodeID)) {
			XMD_log::info(_("Void node"));
			return NULL;
		}

		if (ModulesManager::isEnabled('ximSYNC')) {
			$nodeFrame = new NodeFrame();
			$id = $nodeFrame->getPublishedId($nodeID);
			
			$channelFrame = new ChannelFrame();
			$channelFrameId = $channelFrame->getLast($nodeID, $channelID);

			if($id && $channelFrameId[0]) {
				$serverFrame = new ServerFrame();
				$url = 	$serverFrame->getUrlLastPublicatedNews($id, $channelFrameId[0], $serverid);
				if($url != NULL) {
					$server = new Server($serverid);
					$url_server= $server->get('Url');
					if($url_server) return $url_server.$url;
				}
			}
		} else {

			$sync = new Synchronizer($nodeID);
			$result = $sync->IsPublished();
			if($result) {
				$url = 	$sync->getUrlLastPublicatedNews($nodeID, $channel, $serverid);
				if($url != NULL) {
					$server = new Server($serverid);
					$url_server= $server->get('Url');
					if($url_server) return $url_server.$url;
				}
			}
		}
		return null;

	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idNode
	 * @return unknown
	 */
	function getGaps($idNode) {
		if (ModulesManager::isEnabled('ximSYNC')) {
			$nodefr = new NodeFrame();
			$gaps = $nodefr->getGaps($idNode);
		} else {
			$synchronizer = new Synchronizer($idNode);
			$lastFrame = $synchronizer->GetLastFrameBulletin();
			$lastTime = $synchronizer->GetDateDownOnFrame($lastFrame);
	
			if ($lastFrame && !$lastTime) {
				$gaps = $synchronizer->GetGapsBetweenDates(time(), $synchronizer->GetDateUpOnFrame($lastFrame));			
			} else {
				$gaps = $synchronizer->GetGapsBetweenDates(time(), $lastTime);
				
				if($lastTime) {
					$lastGap = array($lastTime, null, null);
				} else {
					$lastGap = array(time(), null, null);
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
	 * @param timestamp $upDate --> date for publication document
	 * @param timestamp $downDate  --> date for unpublish the document
	 * @param boolean $forcePublication
         *        --> if true --> publish the document although it is in the last version
	 *  	  --> if false --> only publish the document if there is a new mayor version no publish
	 * @param boolean $forceDependencies
	 *        --> if true --> publish the dependencies although they are in the last version
	 * @param array $flagsArray
	 * @return unknown
	 */
	function pushDocInPublishingPool($idNode, $upDate, $downDate = NULL, $flagsArray = NULL, $recurrence = false) {

		if (ModulesManager::isEnabled('ximSYNC')) {
			
		    $syncMngr = new SyncManager();
			$node = new Node($idNode);
			$result = array();
			
			//dafault values

			$syncMngr->setFlag('recurrence', false);
			$syncMngr->setFlag('otfPublication', false);
			if (!isset($flagsArray['force'])) {
				$syncMngr->setFlag('force', false);
			} else {
				$syncMngr->setFlag('force', $flagsArray['force']);
			}
			

			if (($flagsArray!= null) && (is_array($flagsArray))){
				foreach ($flagsArray as $key=>$value) {
					$syncMngr->setFlag($key,$value);
				}
			}
			
			// TODO ximnews bulletins are passing by here now
	    	XMD_Log::info("Stablishing PUSH");
		    $syncMngr->setFlag('deleteOld', true);
			$result = $syncMngr->pushDocInPublishingPool($idNode, $upDate, $downDate);
	
			return $result;
		} else {
			$syncMngr = new SyncManager();
			//dafault values
			$syncMngr->setFlag('workflow', true);
			$syncMngr->setFlag('recurrence', $recurrence);
			
			//It's needs markend and linked
			if (($flagsArray!= null) && (is_array($flagsArray))){
				foreach ($flagsArray as $key=>$value) {
					$syncMngr->setFlag($key,$value);
				}
			}

			$workflow = isset($workflow) ? $workflow : true;
			$forcePublication = isset($flagsArray['force']) && $flagsArray['force'] == 1  ? true : false ;

			$syncMngr->setFlag('deleteOld', true);
			$syncMngr->setFlag('workflow', $workflow);
			$node = new Node($idNode);
			
			$result = array('ok' => array(), 'notok' => array(), 'unchanged' => array());
			
			$nodeList = $node->class->getPublishabledDeps(array('recurrence' => $recurrence));

			foreach($nodeList as $nodeID){

				// push document in publishing pool
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
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idNode
	 * @return unknown
	 */
	function HasUnlimitedLifeTime($idNode) {
		// Both calls are equivalent
		$synchronizer = new Synchronizer($idNode);
		return $synchronizer->HasUnlimitedLifeTime();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idNode
	 * @param unknown_type $dateUp
	 * @param unknown_type $dateDown
	 * @return unknown
	 */
	function publishBulletin($idNode, $dateUp, $dateDown) {
		// Publishing bulletin ximlet
		// accessing to bulletin ximlet, for its language.
		$ximNewsBulletin = new XimNewsBulletin($idNode);
		// Saving bulletin content in ximlet if the bulletin is the first
		$colectorID = $ximNewsBulletin->get('IdColector');
		
		$result = array('ok' => array(), 'notok' => array(), 'unchanged' => array());
	
		if($ximNewsBulletin->isBulletinForXimlet()){
			$ximletID = $ximNewsBulletin->GetBulletinXimlet();
			XMD_Log::info(_("Bulletin is of the ximlet ") . $ximletID);
			$ximlet = new StructuredDocument($ximletID);
			$bulletinDoc = new StructuredDocument($idNode);
			$content = $bulletinDoc->GetContent();
			$ximlet->SetContent($content);
			self::publishXimletBulletin($ximletID, $dateUp + 240, $dateDown);	
		}
		
		// publish Bulletin
		$resultBulletin = self::publishXimNewsDocument($idNode, $dateUp + 240, $dateDown, $idNode);
	
		// publish News
		$resultNews = self::publishNews($idNode, $dateUp, $dateDown);
		
		$result['ok'] = array_merge_recursive($resultBulletin['ok'], $resultNews['ok']);
		$result['notok'] = array_merge_recursive($resultBulletin['notok'], $resultNews['notok']);
		$result['unchanged'] = array_merge_recursive($resultBulletin['unchanged'], $resultNews['unchanged']);
		
		return $result;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idNode
	 * @param unknown_type $dateUp
	 * @param unknown_type $dateDown
	 */
	function publishXimletBulletin($idNode, $dateUp, $dateDown) {
	    
	    $node = new Node($idNode);
	    $nodeType = new NodeType($node->GetNodeType());
	    $nodeTypeName = $nodeType->GetName();
	
	    if($nodeTypeName == 'Ximlet'){
			XMD_Log::info(_("Starting ximlet publication"));
			self::publishXimNewsDocument($idNode, $dateUp, $dateDown);
		
			$docsToPublish = array();
			XMD_Log::info(sprintf(_("Looking for documents associated to the ximlet %s"),$nodeID));
			$docsToPublish = $node->class->getRefererDocs();
		
			//Publishing all the documents associated to the ximlet
			foreach($docsToPublish as $docID) {
			    self::publishXimNewsDocument($docID, $dateUp, $dateDown);
			}
	    }
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idBulletin
	 * @param unknown_type $dateUp
	 * @param unknown_type $dateDown
	 * @return unknown
	 */
	function publishNews($idBulletin, $dateUp, $dateDown) {
		$ximNewsBulletin = new XimNewsBulletin($idBulletin);
	
		$relNewsBulletins = new RelNewsBulletins();
		$news = $relNewsBulletins->GetNewsByBulletin($idBulletin);
		
		$deps = array();
		
		$colectorID = $ximNewsBulletin->get('IdColector');
		
		$result = array('ok' => array(), 'notok' => array(), 'unchanged' => array());
	
		foreach ($news as $newsID) {
	
			$relNewsColector = new RelNewsColector();
			$idRel = $relNewsColector->hasNews($colectorID, $newsID);
			if ($idRel > 0) {
				$relNewsColector = new RelNewsColector($idRel);
				$versionInColector = array($relNewsColector->get('Version'), $relNewsColector->get('SubVersion'));
				unset($relNewsColector);
				
				$dataFactory = new DataFactory($newsID);
		        if($dataFactory->isEditedForPublishing($versionInColector)){
		            $resultNew = self::publishXimNewsDocument($newsID, $dateUp + 120, $dateDown, $idBulletin);
					$result['ok'] = array_merge_recursive($result['ok'], $resultNew['ok']);
					$result['notok'] = array_merge_recursive($result['notok'], $resultNew['notok']);
					$result['unchanged'] = array_merge_recursive($result['unchanged'], $resultNew['unchanged']);
					
					$dependencies = new dependencies();
					$deps_tmp = $dependencies->GetMastersByType($newsID, 'LINK');	
	
					if (($deps_tmp != null) && (is_array($deps_tmp))){
						foreach($deps_tmp as $depID){
			                if(!in_array($depID,$deps)){
			                        $deps[] = $depID;
			                }
			            } 
					}
		        }
			} 
		}
	
		//Publising all the news dependencies
		if( count($deps) > 0 ) {
			foreach ($deps as $depsID) {			
				$resultDep = self::publishXimNewsDocument($depsID, $dateUp, $dateDown, $idBulletin);
				$result['ok'] = array_merge_recursive($result['ok'], $resultDep['ok']);
				$result['notok'] = array_merge_recursive($result['notok'], $resultDep['notok']);
				$result['unchanged'] = array_merge_recursive($result['unchanged'], $resultDep['unchanged']);
			}
		}
		
		return $result ? $result : array();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $idNode
	 * @param unknown_type $up
	 * @param unknown_type $down
	 * @param unknown_type $BulletinFrameID
	 * @param unknown_type $markEnd
	 * @return unknown
	 */
	function publishXimNewsDocument($idNode, $up, $down, $BulletinFrameID = null, $markEnd = null) {
		
		$node = new Node($idNode);
	    if (!($node->get('IdNode') > 0)) {
	    	XMD_log::error(sprintf(_("Unexisting node %s"), $idNode));
	        return false;
	    }
	
		$syncMngr = new SyncManager();
	
		$syncMngr->setFlag('markEnd', $markEnd);
		$syncMngr->setFlag('deleteOld', true);
		$syncMngr->setFlag('type', 'ximNEWS');
	
		//If it is a news, new flags are added to update its versions in RelNewsColector
		$nodeTypeNode = new NodeType($node->GetNodeType());
		$nodeTypeName = $nodeTypeNode->GetName();
	
		if ($nodeTypeName == "XimNewsNewLanguage"){
			$syncMngr->setFlag('mail', 'true');
			$syncMngr->setFlag('bulletinID', $BulletinFrameID);
			$ximNewsBulletin = new XimNewsBulletin($BulletinFrameID);
			$colectorID = $ximNewsBulletin->get('IdColector');
			$syncMngr->setFlag('colectorID', $colectorID);
			$syncMngr->setFlag('updateRels',1);
		}
		else if ($nodeTypeName == "XimNewsBulletinLanguage"){
			$syncMngr->setFlag('bulletinID', $idNode);
		}
	
		// push document in publishing pool
		$result = array('ok' => array(), 'notok' => array(), 'unchanged' => array());
		$syncMngr->pushDocInPublishingPool($idNode, $up, $down);
		if ($syncMngr->error()) {
			$result['notok']["#" . $idNode][0][0] = true;
		} else {
			$result['ok']["#" . $idNode][0][0] = true;
		}
	
        if ($nodeTypeName == "XimNewsNewLanguage"){
			$ximNewsBulletin = new XimNewsBulletin($BulletinFrameID);
			$colectorID = $ximNewsBulletin->get('IdColector');

			$relNewsColector = new RelNewsColector();
			$idRel = $relNewsColector->hasNews($colectorID, $idNode);

			if ($idRel > 0) {
				$dataFactory = new datafactory($idNode);
				$version = $dataFactory->getLastVersion();
				$subversion=0;
				$idVersion = $dataFactory->getVersionId($version,$subversion);

				$relNewsColector = new RelNewsColector($idRel);
				$idCache = $relNewsColector->get('IdCache');	
				$relNewsColector->set('Version', $version);
				$relNewsColector->set('SubVersion', $subversion);

				if (!$relNewsColector->update()) {
					XMD_Log::error(_('Updating version'));
				}

				if($idCache > 0){
					$ximNewsCache = new XimNewsCache($idCache);
					$ximNewsCache->set('IdVersion',$idVersion);

					if (!$ximNewsCache->update()) {
						XMD_Log::error(_('Updating cache version'));
					}
				}
			}

			XMD_Log::info(sprintf(_("node %s version %s"), $idNode, $version));
        }
        
        return $result;
	}
}
?>