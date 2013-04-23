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



if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));


include_once( XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/model/NodeFrame.class.php');
include_once( XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/manager/ServerFrameManager.class.php');
include_once( XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/manager/ChannelFrameManager.class.php');
include_once( XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/manager/BatchManager.class.php');

/**
*	@brief Handles the activity of a NodeFrame.
*
*	A NodeFrame is the representation of a node ready to published in a temporal interval.
*	It have 2 posibles values for its activity 1 (published), 0 (not published).
*/

class NodeFrameManager {

	/**
	*  Checks whether the NodeFrame must be active or not.
	*  @param int nodeFrameId
	*  @param int nodeId
	*  @param int timeUp
	*  @param int timeDown
	*  @param string batchType
	*  @param int batchType
	*  @return bool
	*/

    function checkActivity($nodeFrameId, $nodeId, $timeUp, $timeDown, $batchType, $testTime = NULL) {
		$nodeFrame = new NodeFrame();

		if (is_null($nodeFrameId)) {
			$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8, _("ERROR empty IdNodeFrame")." - checkActivity");
			return false;
		}

		$resucitar = false;
		$replacedBy = NULL;

		if ($batchType == "Up") {

			if (is_null($testTime)) {
				$now = time();
			} else {
				$now = $testTime;
			}

			// I'll never be active
			if ($timeDown != 0 && $timeDown < $now) {
				$nodeFrame = new NodeFrame($nodeFrameId);
				$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
					__LINE__, "INFO", 8, _("NodeFrame will never be active")."(0) ".$nodeFrameId, true);
				$nodeFrame->set('IsProcessUp', 1);
				$nodeFrame->set('IsProcessDown', 1);
				$nodeFrame->update();

				$nodeFrame->cancelServerFrames($nodeFrameId);

				return true;
			}

			$nodeFramesInTime = $this->getNodeFramesInTime($nodeId, $timeUp, $now);
			$n = count($nodeFramesInTime);

			// By default get activity nodeFrame with greater version
			// Setting activity = 0 for the rest
			for ($i = 1; $i < $n; $i++) {
				$id = $nodeFramesInTime[$i]['Id'];
				$nodeFrame = new NodeFrame($id);

				if ($nodeFramesInTime[$i]['Active'] == 1) {
					$nodeFrame->NodeFrameToLog(null, $id , null, null, null, __CLASS__, __FUNCTION__, __FILE__,
						__LINE__, "INFO", 8, "Lossing NodeFrame $id activity", true);
					$nodeFrame->set('Active', 0);
					$nodeFrame->set('IsProcessDown', 1);
				} else {
					$nodeFrame->NodeFrameToLog(null, $id , null, null, null, __CLASS__, __FUNCTION__, __FILE__,
						__LINE__, "INFO", 8, _("NodeFrame never will be active")."(1) ".$id, true);
					$nodeFrame->set('IsProcessUp', 1);
					// ???
					$nodeFrame->set('IsProcessDown', 1);
					$nodeFrame->cancelServerFrames($id);
				}

				$nodeFrame->update();
			}

			if ($nodeFramesInTime[0]['Active'] == 0){
				$processUp = 1;
				$replacedBy = NULL;
				$activity = 1;
				$idToActive = $nodeFramesInTime[0]['Id'];

				$nodeFrame = new NodeFrame();
				$nodeFrame->NodeFrameToLog(null, $nodeFrameId , null, null, null, __CLASS__, __FUNCTION__, __FILE__,
					__LINE__, "INFO", 8, _("Check NodeFrame activity")." $nodeFrameId", true);
			} else {
				// Nothing to do if nodeFrame with greather version is active
				return true;
			}

		} else if ($batchType == "Down") {
			$nodeFrame = new NodeFrame($nodeFrameId);
			$active = $nodeFrame->get('Active');

			// Return activity to nodeFrame with previouly greater version
			if ($resucitar == true) {
				$nodeFrame->set('IsProcessDown', 1);
				$nodeFrame->update();

				$nodeFramesInTime = $this->getNodeFramesInTime($nodeId, $timeUp, $now);
				$processUp = 1;
				$replacedBy = NULL;
				$activity = 1;
				$idToActive = $nodeFramesInTime[0]['Id'];
			} else {

				if ($active == 1) {
					$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
						__LINE__, "INFO", 8, _("Lossing NodeFrame activity")." $nodeFrameId", true);

					$processUp = 0;
					$activity = 0;
					$idToActive = $nodeFrameId;
				} else {
					$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
						__LINE__, "INFO", 8, _("No active NodeFrame")." $nodeFrameId", true);

					$nodeFrame->set('IsProcessDown', 1);
					$nodeFrame->update();

					return true;
				}
			}

		} else {
			$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, sprintf(_("ERROR: %s is a wrong type of batch"), $batchType));
			return true;
		}

		// Setting activity
		$nodeFrame->NodeFrameToLog(null, $nodeFrameId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "INFO", 8, sprintf(_("Setting NodeFrame %s activity to %s"),$idToActive, $activity), true);
		$result = $this->setActivity($activity, $idToActive, $nodeId, $processUp, $replacedBy);
		return $result;
    }


	/**
	 *  Sets the NodeFrame Activity and modifies the state of its associated ServerFrames.
	 *	The replacedBy param  sets the nodeFrameId which replaces this NodeFrame when it become inactive.
	 *  @param int activity
	 *  @param int nodeFrId
	 *  @param int nodeId
	 *  @param int up
	 *  @param int replacedBy
	 *  @return bool
	 */

    function setActivity($activity, $nodeFrId, $nodeId, $up, $replacedBy = NULL) {
		$nodeFrame = new NodeFrame($nodeFrId);

		if (is_null($nodeFrId)) {
			$nodeFrame->NodeFrameToLog(null, $nodeFrId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, _("ERROR empty IdNodeFrame")." - setActivity");
			return false;
		}

		$isActive = $nodeFrame->get('Active');
		$idUnactive = $nodeFrame->get('GetActivityFrom');
		$nodeFrame->set('Active',$activity);

		$canceled = NULL;
		if ( !is_null($replacedBy) ) {
			$nodeFrame->set('GetActivityFrom', $replacedBy);
			$canceled = 1;
		}

		if ($up == 1) {
			$nodeFrame->set('IsProcessUp', 1);
		} else {
			$nodeFrame->set('IsProcessDown', 1);
		}

		$result = $nodeFrame->update();

		if (!$result) {
			$nodeFrame->NodeFrameToLog(null, $nodeFrId, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8,sprintf(_("ERROR Setting NodeFrame %s activity to %s"), $nodeFrId, $activity));
			return false;
		}

		if ($activity == 1) {
			$operation = "Up";
		} else {
			$operation = "Down";
		}

		$sfOK = true;

		// todo: make foreach channelframes (not serverframes)

		$frames = $nodeFrame->getFrames($nodeFrId);

		foreach($frames as $serverFrId) {
			XMD_log::info(_("Procesing serverFrame")." $serverFrId");

			$channelFrameManager = new ChannelFrameManager();
			$result = $channelFrameManager->changeState($serverFrId, $operation, $nodeId, $canceled);

			if(!$result){
				XMD_log::error(_("The Serverframe state change has failed")." $serverFrId");
				$sfOK = false;
			}
		}

		return $sfOK;
    }

	/**
	 *  Gets all NodeFrames whose time interval is on the current time.
	 *	If two NodeFrames are the same VersionId, is seted as active the newest.
	 *	For solve the problem of consider the infinite as zero, TimeDown2 is introduced.
	 *  @param int nodeId
	 *  @param int up
	 *	@param int now
	 *  @return array|null
	 */

    function getNodeFramesInTime($nodeId, $up, $now) {
		$dbObj = new DB();

		$sql = "SELECT IdNodeFrame, Active, if(TimeDown IS NULL, 1988146800, TimeDown) as TimeDown2 FROM NodeFrames
			WHERE TimeUp < $now AND (TimeDown > $now OR TimeDown IS NULL) AND (IsProcessUp = 0 OR Active = 1)
			AND NodeId = $nodeId ORDER BY VersionId DESC, TimeDown2 DESC";

		$dbObj->Query($sql);
		if ($dbObj->numRows == 0) {
			return NULL;
		}

		$nodeFrames = array();
		$i = 0;
		while(!$dbObj->EOF) {
			$nodeFrames[$i]['Id'] = $dbObj->GetValue("IdNodeFrame");
			$nodeFrames[$i]['Active'] = $dbObj->GetValue("Active");

			$i++;
			$dbObj->Next();
		}

		return $nodeFrames;
    }

	/**
	*  Gets the NodeFrames with Activity = 0.
	*  @param int batchId
	*  @param array chunk
	*  @param string batchType
	*  @return array
	*/

    function getNotProcessNodeFrames($batchId, $chunk, $batchType) {

		$dbObj = new DB();
		$nodeFrame = new NodeFrame();

		if ($batchType == 'Up') {
			$sql = "SELECT NodeFrames.IdNodeFrame, NodeFrames.NodeId, NodeFrames.VersionId, NodeFrames.TimeUp, " .
				"NodeFrames.TimeDown, NodeFrames.Active FROM NodeFrames, ServerFrames WHERE ServerFrames.IdBatchUp = " .
				"$batchId AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND (NodeFrames.IsProcessUp = 0 "
				."OR NodeFrames.IsProcessUp IS NULL) LIMIT $chunk";
		} else if ($batchType == 'Down') {
				$batch = new Batch($batchId);
				$batchUp = $batch->getUpBatch($batchId);

				if (!is_null($batchUp)) {
					$sql = "SELECT NodeFrames.IdNodeFrame, NodeFrames.NodeId, NodeFrames.VersionId, NodeFrames.TimeUp, " .
					"NodeFrames.TimeDown, NodeFrames.Active FROM NodeFrames, ServerFrames WHERE ServerFrames.IdBatchUp = " .
					"{$batchUp[0]} AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND (NodeFrames.IsProcessDown = 0 "
					."OR NodeFrames.IsProcessDown IS NULL) LIMIT $chunk";
				} else {
					$nodeId = $batch->get('IdNodeGenerator');

					$sql = "SELECT NodeFrames.IdNodeFrame, NodeFrames.NodeId, NodeFrames.VersionId, NodeFrames.TimeUp, " .
					"NodeFrames.TimeDown, NodeFrames.Active FROM NodeFrames, ServerFrames WHERE
					NodeFrames.NodeId = $nodeId AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND
					(NodeFrames.IsProcessDown = 0 OR NodeFrames.IsProcessDown IS NULL) LIMIT $chunk";
				}

		} else {

			$nodeFrame->NodeFrameToLog($batchId, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
				__LINE__, "ERROR", 8, sprintf(_("ERROR: batch %s is a non-existent type of batch %s"),$batchId, $batchType) );
			return false;
		}

		$dbObj->Query($sql);
		$nodeFrames = array();
		$i = 0;
		while(!$dbObj->EOF) {
			$nodeFrames[$i]['nodeFrId'] = $dbObj->GetValue("IdNodeFrame");
			$nodeFrames[$i]['nodeId'] = $dbObj->GetValue("NodeId");
			$nodeFrames[$i]['version'] = $dbObj->GetValue("VersionId");
			$nodeFrames[$i]['up'] = $dbObj->GetValue("TimeUp");
			$nodeFrames[$i]['down'] = $dbObj->GetValue("TimeDown");
			$nodeFrames[$i]['active'] = $dbObj->GetValue("Active");
			$i++;
			$dbObj->Next();
		}

		return $nodeFrames;
    }

	/**
	*  Gets the NodeFrames which matching the values of IsProcessUp = 0 and Active = 0.
	*  @param int nodeID
	*  @return array
	*/

	function getPendingNodeFrames($nodeID) {
		$dbObj = new DB();

		$sql = "SELECT IdNodeFrame FROM NodeFrames WHERE NodeId = $nodeID AND IsProcessUp = 0 AND Active = 0";

		$dbObj->Query($sql);
		$nodeFrames = array();

		while(!$dbObj->EOF) {
			$nodeFrames[] = $dbObj->GetValue("IdNodeFrame");
			$dbObj->Next();
		}

		return $nodeFrames;
	}

	/**
	*   Gets all NodeFrames which matching the value of NodeId.
	*	@param int nodeId
	*	@return array
	*/

    function getByNode($nodeId) {

		$result = array();

		$nodeFrame = new NodeFrame();
		$result = $nodeFrame->find('IdNodeFrame', 'NodeId = %s', array('NodeId' => $nodeId), MULTI);

		return $result;
    }

	/**
	*  Deletes a NodeFrame and its associated channelFrames and serverFrames.
	*  If the flag unPublish is seted then the ServerFrames associated to NodeFrame are not deleted but its state is changed to Due2Out.
	*  @param int idNodeFrame
	*  @param bool unPublish
	*/

	function delete($idNodeFrame, $unPublish = false) {

		$nodeFrame = new NodeFrame($idNodeFrame);
		$nodeId = $nodeFrame->get('NodeId');

		$serverFrameMng = new ServerFrameManager();
		$serverFrames = $serverFrameMng->getByNodeFrame($idNodeFrame);

		if (sizeof($serverFrames) > 0) {

			$arrayAffectedBatchs = array();

			foreach ($serverFrames as $dataFrames) {
				$idServerFrame = $dataFrames[0];

				$serverFrame = new ServerFrame($idServerFrame);
				$batchMng = new BatchManager();

				$idChannelFrame = $serverFrame->get('IdChannelFrame');
				$state = $serverFrame->get('State');

				// Deleting channelFrame (if exists)

				$channelFrame = new ChannelFrame($idChannelFrame);

				if ($channelFrame->get('IdChannelFrame') > 0) {
					$channelFrame->delete();
				}

				// Deleting (or unpublish) serverFrame

				if ($state == 'In' && $unPublish == true) {
					XMD_log::info("Do not delete ServerFrame $idServerFrame - setting it to Due2Out to be unpublished");

					$idBatchUp = $serverFrame->get('IdBatchUp');
					$arrayAffectedBatchs[$idBatchUp] = (!isset($arrayAffectedBatchs[$idBatchUp])) ? 1 : $arrayAffectedBatchs[$idBatchUp] ++;

					// Changing ServerFrame State
					$serverFrameMng->changeState($idServerFrame, 'Down', $nodeId);
				} elseif(in_array($state, array('Due2Out', 'Due2Out_'))) {
					XMD_log::info("Do not delete serverFrame $idServerFrame - state $state");
				} else {
					$serverFrame->delete();
				}
			}

			XMD_log::info(_("Affected batchs:"). print_r($arrayAffectedBatchs, true));

			if (is_array($arrayAffectedBatchs) && count($arrayAffectedBatchs) > 0) {

				foreach ($arrayAffectedBatchs as $idBatch => $serverFramesTotal) {

					// Creating Down-Batch
					$batchMng->buildBatchsFromDeleteNode($idBatch, $nodeId, $serverFramesTotal);
				}
			}

		}

		// Deleting nodeFrame

		//$nodeFrame->delete();
	}

}
?>
