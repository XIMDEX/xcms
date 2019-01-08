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

namespace Ximdex\Sync;

use Ximdex\Logger;
use Ximdex\Models\Batch;
use Ximdex\Models\NodeFrame;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\ChannelFrame;

/**
*	@brief Handles the activity of a NodeFrame.
*
*	A NodeFrame is the representation of a node ready to published in a temporal interval.
*	It have 2 posibles values for its activity 1 (published), 0 (not published).
*/
class NodeFrameManager
{
	/**
	 * Checks whether the NodeFrame must be active or not
	 * 
	 * @param int $nodeFrameId
	 * @param int $nodeId
	 * @param int $timeUp
	 * @param int $timeDown
	 * @param string $batchType
	 * @param int $testTime
	 * @return bool
	 */
    public function updateActivity(int $nodeFrameId, int $nodeId, int $timeUp, ?int $timeDown, string $batchType, int $testTime = null) : bool
    {
		if (is_null($nodeFrameId)) {
			Logger::error('Empty IdNodeFrame - updateActivity');
			return false;
		}
		$resucitar = false;
		$replacedBy = null;
		if (is_null($testTime)) {
			$now = time();
		} else {
			$now = $testTime;
		}
		if ($batchType == 'Up') {

			// I'll never be active
			if ($timeDown && $timeDown < $now) {
				$nodeFrame = new NodeFrame($nodeFrameId);
				Logger::info('NodeFrame will never be active: ' . $nodeFrameId);
				$nodeFrame->cancel();
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
					Logger::info('Lossing NodeFrame ' . $id . ' activity');
					$nodeFrame->set('Active', 0);
					$nodeFrame->set('IsProcessDown', 1);
					$nodeFrame->update();
				} else {
					Logger::info('NodeFrame never will be active: ' . $id);
					$nodeFrame->cancel();
				}
			}
			if (isset($nodeFramesInTime[0]) and $nodeFramesInTime[0]['Active'] == 0) {
				$processUp = 1;
				$replacedBy = null;
				$activity = 1;
				$idToActive = $nodeFramesInTime[0]['Id'];
				Logger::info('Check NodeFrame activity: ' . $nodeFrameId);
			} else {
			    
				// Nothing to do if nodeFrame with greather version is active
				return true;
			}
		} else if ($batchType == 'Down') {
			$nodeFrame = new NodeFrame($nodeFrameId);
			$active = $nodeFrame->get('Active');

			// Return activity to nodeFrame with previously greater version
			if ($resucitar == true) {
				$nodeFrame->set('IsProcessDown', 1);
				$nodeFrame->update();
				$nodeFramesInTime = $this->getNodeFramesInTime($nodeId, $timeUp, $now);
				$processUp = 1;
				$replacedBy = null;
				$activity = 1;
				if (isset($nodeFramesInTime[0])) {
				    $idToActive = $nodeFramesInTime[0]['Id'];
				} else {
				    $idToActive = null;
				}
			} else {
				if ($active == 1) {
					Logger::info('Lossing NodeFrame activity: ' . $nodeFrameId);
					$processUp = 0;
					$activity = 0;
					$idToActive = $nodeFrameId;
				} else {
					Logger::info('No active NodeFrame: ' . $nodeFrameId);
					$nodeFrame->set('IsProcessDown', 1);
					$nodeFrame->update();
					return true;
				}
			}
		} else {
			Logger::error(sprintf('%s is a wrong type of batch', $batchType));
			return true;
		}

		// Setting activity
		Logger::info(sprintf('Setting NodeFrame %s activity to %s', $idToActive, $activity));
		$result = $this->setActivity($activity, $idToActive, $nodeId, $processUp, $replacedBy);
		return $result;
    }

	/**
	 *  Sets the NodeFrame Activity and modifies the state of its associated ServerFrames.
	 *	The replacedBy param  sets the nodeFrameId which replaces this NodeFrame when it become inactive.
	 *
	 *  @param int activity
	 *  @param int nodeFrId
	 *  @param int nodeId
	 *  @param int up
	 *  @param int replacedBy
	 *  @return bool
	 */
    private function setActivity(int $activity, int $nodeFrId, int $nodeId, int $up, int $replacedBy = null)
    {
        if (is_null($nodeFrId)) {
            Logger::error('Empty IdNodeFrame - setActivity');
            return false;
        }
		$nodeFrame = new NodeFrame($nodeFrId);
		$nodeFrame->set('Active', $activity);
		$cancelled = null;
		if ( !is_null($replacedBy) ) {
			$nodeFrame->set('GetActivityFrom', $replacedBy);
			$cancelled = 1;
		}
		if ($up == 1) {
			$nodeFrame->set('IsProcessUp', 1);
		} else {
			$nodeFrame->set('IsProcessDown', 1);
		}
		$result = $nodeFrame->update();
		if (!$result) {
			Logger::error(sprintf('Setting NodeFrame %s activity to %s', $nodeFrId, $activity));
			return false;
		}
		if ($activity == 1) {
			$operation = 'Up';
		} else {
			$operation = 'Down';
		}
		$sfOK = true;
		
		// todo: make foreach channelframes (not serverframes)
		$frames = $nodeFrame->getFrames($nodeFrId, $operation);
		$channelFrameManager = new ChannelFrameManager();
		foreach($frames as $serverFrId) {
			Logger::info('Processing serverFrame ' . $serverFrId . ' for nodeFrameID: ' . $nodeFrId . ' and nodeID: ' . $nodeId);
			$result = $channelFrameManager->changeState($serverFrId, $operation, $nodeId, $cancelled);
			if ($result === false) {
				Logger::error('The Serverframe state change has failed ' . $serverFrId);
				$sfOK = false;
			}
		}
		return $sfOK;
    }

	/**
	 *  Gets all NodeFrames whose time interval is on the current time.
	 *	If two NodeFrames are the same VersionId, is seted as active the newest.
	 *	For solve the problem of consider the infinite as zero, TimeDown2 is introduced.
	 *
	 *  @param int nodeId
	 *  @param int up
	 *	@param int now
	 *  @return array|null
	 */
    function getNodeFramesInTime($nodeId, $up, $now)
    {
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT IdNodeFrame, Active, if(TimeDown IS NULL, 1988146800, TimeDown) as TimeDown2 FROM NodeFrames 
			WHERE TimeUp < ' . $now . ' AND (TimeDown > ' . $now . ' OR TimeDown IS NULL) AND (IsProcessUp = 0 OR Active = 1) 
			AND NodeId = ' . $nodeId . ' ORDER BY VersionId DESC, TimeDown2 DESC, IdNodeFrame DESC';
		$dbObj->Query($sql);
		$nodeFrames = array();
		$i = 0;
		while(!$dbObj->EOF) {
			$nodeFrames[$i]['Id'] = $dbObj->GetValue('IdNodeFrame');
			$nodeFrames[$i]['Active'] = $dbObj->GetValue('Active');
			$i++;
			$dbObj->Next();
		}
		return $nodeFrames;
    }

	/**
	 * Gets the NodeFrames with Activity = 0
	 * 
	 * @param int $batchId
	 * @param int $chunk
	 * @param string $batchType
	 * @return array|NULL
	 */
    function getNodeFramesToProcess(int $batchId, int $chunk, string $batchType) : ?array
    {
        $sql = 'SELECT NodeFrames.IdNodeFrame, NodeFrames.NodeId, NodeFrames.VersionId, NodeFrames.TimeUp, ' 
            . 'NodeFrames.TimeDown, NodeFrames.Active FROM NodeFrames, ServerFrames';
		if ($batchType == Batch::TYPE_UP) {
			$sql .= ' WHERE ServerFrames.IdBatchUp = ' . $batchId 
			    . ' AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IsProcessUp = 0 LIMIT ' . $chunk;
		} elseif ($batchType == Batch::TYPE_DOWN) {
				$batch = new Batch($batchId);
				$batchUp = $batch->getUpBatch($batchId);
				if ($batchUp) {
				    
				    // Batch type Down with a type Up linked
					$sql .= ' WHERE ServerFrames.IdBatchUp = ' . $batchUp[0] 
					   . ' AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND NodeFrames.IsProcessDown = 0 LIMIT ' . $chunk;
				} else {
				    
				    // No batch type Up associated
				    $sql .= ' WHERE ServerFrames.IdBatchDown = ' . $batchId . ' AND ServerFrames.IdNodeFrame = NodeFrames.IdNodeFrame AND ' 
				        . 'NodeFrames.IsProcessDown = 0 LIMIT ' . $chunk;
				}
		} else {
			Logger::error(sprintf('Batch %s is a non-existent type of batch %s', $batchId, $batchType));
			return null;
		}
		$dbObj = new \Ximdex\Runtime\Db();
		$dbObj->Query($sql);
		$nodeFrames = array();
		$i = 0;
		while(!$dbObj->EOF) {
			$nodeFrames[$i]['nodeFrId'] = $dbObj->GetValue('IdNodeFrame');
			$nodeFrames[$i]['nodeId'] = $dbObj->GetValue('NodeId');
			$nodeFrames[$i]['version'] = $dbObj->GetValue('VersionId');
			$nodeFrames[$i]['up'] = $dbObj->GetValue('TimeUp');
			$nodeFrames[$i]['down'] = $dbObj->GetValue('TimeDown');
			$nodeFrames[$i]['active'] = $dbObj->GetValue('Active');
			$i++;
			$dbObj->Next();
		}
		return $nodeFrames;
    }

	/**
	*  Gets the NodeFrames which matching the values of IsProcessUp = 0 and Active = 0
	*  
	*  @param int nodeID
	*  @return array
	*/
	function getPendingNodeFrames($nodeID)
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT IdNodeFrame FROM NodeFrames WHERE NodeId = ' . $nodeID . ' AND IsProcessUp = 0 AND Active = 0';
		$dbObj->Query($sql);
		$nodeFrames = array();
		while(!$dbObj->EOF) {
			$nodeFrames[] = $dbObj->GetValue('IdNodeFrame');
			$dbObj->Next();
		}
		return $nodeFrames;
	}

	/**
	*   Gets all NodeFrames which matching the value of NodeId.
	*   
	*	@param int nodeId
	*	@return array
	*/
    function getByNode($nodeId)
    {
		$nodeFrame = new NodeFrame();
		$result = $nodeFrame->find('IdNodeFrame', 'NodeId = %s', array('NodeId' => $nodeId), MULTI);
		return $result;
    }

	/**
	*  Deletes a NodeFrame and its associated channelFrames and serverFrames.
	*  If the flag unPublish is seted then the ServerFrames associated to NodeFrame are not deleted but its state is changed to Due2Out.
	*  
	*  @param int idNodeFrame
	*  @param bool unPublish
	*/
	function delete($idNodeFrame, $unPublish = false)
	{
		$nodeFrame = new NodeFrame($idNodeFrame);
		$nodeId = $nodeFrame->get('NodeId');
		$serverFrameMng = new ServerFrameManager();
		$serverFrames = $serverFrameMng->getByNodeFrame($idNodeFrame);
		if (count($serverFrames) > 0) {
			$arrayAffectedBatchs = array();
			foreach ($serverFrames as $dataFrames) {
				$idServerFrame = $dataFrames[0];
				$serverFrame = new ServerFrame($idServerFrame);
				$idChannelFrame = $serverFrame->get('IdChannelFrame');
				$state = $serverFrame->get('State');

				// Deleting channelFrame (if exists)
				$channelFrame = new ChannelFrame($idChannelFrame);
				if ($channelFrame->get('IdChannelFrame') > 0) {
					$channelFrame->delete();
				}

				// Deleting (or unpublish) serverFrame
				if ($state == ServerFrame::IN && $unPublish == true) {
					Logger::info('Do not delete ServerFrame ' . $idServerFrame . ' - setting it to Due2Out to be unpublished');
					$idBatchUp = $serverFrame->get('IdBatchUp');
					$arrayAffectedBatchs[$idBatchUp] = (!isset($arrayAffectedBatchs[$idBatchUp])) ? 1 : $arrayAffectedBatchs[$idBatchUp]++;

					// Changing ServerFrame State
					$serverFrameMng->changeState($idServerFrame, Batch::TYPE_DOWN, $nodeId);
				} elseif(in_array($state, array(ServerFrame::DUE2OUT, ServerFrame::DUE2OUT_))) {
					Logger::info('Do not delete serverFrame ' . $idServerFrame . ' - state $state');
				} else {
					$serverFrame->delete();
				}
			}
			Logger::info('Affected batchs: ' . print_r($arrayAffectedBatchs, true));
			if (is_array($arrayAffectedBatchs) && count($arrayAffectedBatchs) > 0) {
			    $batchMng = new BatchManager();
				foreach ($arrayAffectedBatchs as $idBatch => $serverFramesTotal) {

					// Creating Down-Batch
					$batchMng->buildBatchsFromDeleteNode($idBatch, $nodeId, $serverFramesTotal);
				}
			}
		}
	}
}
