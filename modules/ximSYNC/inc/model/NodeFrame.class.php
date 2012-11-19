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




ModulesManager::file('/inc/model/orm/NodeFrames_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/ServerFrameManager.class.php', 'ximSYNC');
ModulesManager::file('/inc/model/SynchronizerStat.class.php', 'ximSYNC');
ModulesManager::file('/inc/persistence/datafactory.inc');
ModulesManager::file('/inc/model/RelStrDocChannels.class.php');


/**
*	@brief Handles operations with NodeFrames.
*
*	A NodeFrame is the representation of a node ready to published in a temporal interval.
*	This class includes the methods that interact with the Database.
*/

class NodeFrame extends NodeFrames_ORM {

    var $syncStatObj;

	/**
	*  Adds a row to NodeFrames table.
	*  @param int nodeId
	*  @param string name
	*  @param int version
	*  @param int up
	*  @param int down
	*  @return int|null
	*/

    function create($nodeId, $name, $version, $up, $down = NULL) {
		$this->set('NodeId',$nodeId);
		$this->set('VersionId',$version);
		$this->set('TimeUp',$up);
		$this->set('TimeDown',$down);
		$this->set('Active',0);
		$this->set('GetActivityFrom',0);
		$this->set('IsProcessUp',0);
		$this->set('IsProcessDown',0);
		$this->set('Name',$name);


		parent::add();
		$idNodeFrame = $this->get('IdNodeFrame');

		if ($idNodeFrame > 0) {
			return $idNodeFrame;
		}

		XMD_Log::info("ERROR Creando el nodeFrame");
		return NULL;
    }

	/**
    *	Gets all ServerFrames associated to a NodeFrame.
	*	@param int idNdFr
	*	@return array
	*/

    function getFrames($idNdFr) {
		$dbObj = new DB();
		$dbObj->Query("SELECT IdSync FROM ServerFrames WHERE IdNodeFrame = $idNdFr");

		$frames = array();
		while(!$dbObj->EOF) {
			$frames[] = $dbObj->GetValue("IdSync");
			$dbObj->Next();
		}

		return $frames;
    }

	/**
	*  Gets the time intervals without NodeFrames for a given Node.
	*	@param int nodeId
	*	@return array
	*/

    function getGaps($nodeId) {
		$dbObj = new DB();
		$arrayDates = array();
		$gaps = array();
		$now = time();
		$infinito = mktime(0,0,0,12,12,2033);
		$j=0;
		$sql = "SELECT TimeUp, TimeDown FROM NodeFrames WHERE NodeId = $nodeId AND (TimeDown > $now OR TimeDown IS NULL)
				ORDER BY TimeUp ASC";

		$dbObj->Query($sql);

		while(!$dbObj->EOF) {
			$timeUp = $dbObj->GetValue("TimeUp");
			$timeDown = $dbObj->GetValue("TimeDown");
			$arrayDates[$j]['up'] = $timeUp;

			if (!$timeDown) {
			$arrayDates[$j]['down'] = $infinito;
			} else {
			$arrayDates[$j]['down'] = $timeDown;
			}

			$j++;
			$dbObj->Next();
		}

		if ($dbObj->numRows == 0) {
			$gaps[$j]['start'] = $now;
			$gaps[$j]['end'] = $infinito;
			return $gaps;
		}

		//El infinito
		$arrayDates[$j]['up'] = $infinito;
		$arrayDates[$j]['down'] = 0;

		$j = 0;
		$size = sizeof($arrayDates);

		if ($arrayDates[0]['up'] > $now) {
			$gaps[$j]['start'] = $now;
			$gaps[$j]['end'] = $arrayDates[0]['up'];
			$j++;
		}

		for($i=1;$i<$size;$i++) {
			$tmp = $arrayDates[$i]['up'] - $arrayDates[$i-1]['down'];

			if ($tmp > 0) {
			$gaps[$j]['start'] = $arrayDates[$i-1]['down'];
			$gaps[$j]['end'] = $arrayDates[$i]['up'];
			$j++;
			}
		}

		return $gaps;
    }

	/**
    *	Gets the NodeFrame active.
	*	@param int nodeId
	*	@param int nodeFrId
	*	@param int up
	*	@param int down
	*	@param int testTime
	*	@return array / NULL
	*/

    function getActiveNodeFrame($nodeId,$nodeFrId,$up,$down=null,$testTime = NULL) {
		$dbObj = new DB();

		if (!$testTime) {
			$now = mktime();
		} else {
			$now = $testTime;
		}

		//Si la función es llamada desde el formulario de publicación
		/*$sql0 = "TimeUp, TimeDown, VersionId";
		$sql1 = " ORDER BY Active = 1 DESC, Active = 0 DESC, IdNodeFrame DESC LIMIT 1";*/

		if (!$down) {
			$sql = "SELECT IdNodeFrame, TimeDown, VersionId FROM NodeFrames WHERE ((TimeDown IS NOT NULL
				AND $up < TimeDown) OR (TimeDown IS NULL)) AND NodeId = $nodeId AND TimeUp < $now
				AND IdNodeFrame != $nodeFrId AND Active = 1";
		} else {
			$sql = "SELECT IdNodeFrame, TimeDown, VersionId FROM NodeFrames WHERE ((TimeDown IS NOT NULL
				AND ((TimeUp < $up AND $up  <TimeDown) OR (TimeUp < $down AND $down < TimeDown)))
				OR (TimeDown IS NULL AND TimeUp < $down)) AND NodeId = $nodeId AND TimeUp < $now
				AND IdNodeFrame != $nodeFrId AND Active = 1";
		}

		$i = 0;
		$dbObj->Query($sql);
		if ($dbObj->numRows != 1) {
			return NULL;
		}

		$idNodeFr = $dbObj->GetValue("IdNodeFrame");
		$version = $dbObj->GetValue("VersionId");
		$timeDown = $dbObj->GetValue("TimeDown");

		return array($idNodeFr,$version,$timeDown);
    }

	/**
    *	Checks whether exists a NodeFrame after a given time.
	*	@param int nodeId
	*	@param int up
	*	@param int down
	*	@return boolean
	*/

    function existsNodeFrame($nodeId, $up, $down = NULL) {
		$dataFactory = new DataFactory($nodeId);
		$idVersion = $dataFactory->GetLastVersionId();

		if (is_null($down)) {
			$condition = 'NodeId = %s AND VersionId = %s AND TimeUp <= %s AND TimeDown IS NULL';
			$params = array('NodeId' => $nodeId, 'VersionId' => $idVersion, 'TimeUp' => $up);
		} else {
			$condition = 'NodeId = %s AND VersionId = %s AND TimeUp <= %s AND (TimeDown IS NULL OR TimeDown = %s)';
			$params = array('NodeId' => $nodeId, 'VersionId' => $idVersion, 'TimeUp' => $up, 'TimeDown' => $down);
		}

		$result = $this->find('IdNodeFrame', $condition, $params, MONO);

		if (is_null($result)) {
			return false;
		}

		// extensión de la validación de si existe un node frame
		// Comprueba para un structureddocument dado si todos sus canales han sido publicados
		$node = new Node($nodeId);
		if ($node->nodeType->get('IsStructuredDocument') > 0) {
			$channelList = array();
			foreach ($result as $idNodeFrame) {
				$sf = new ServerFrame();
				$sfResult = $sf->find('IdChannelFrame', 'IdNodeFrame = %s', array($idNodeFrame), MONO);
				if (empty($sfResult)) {
					XMD_Log::error('ServerFrame not found for NodeFrame ' . $idNodeFrame);
					return false;
				}
				foreach($sfResult as $idChannelFrame) {
					$cf = new ChannelFrame($idChannelFrame);
					$channelList[] = $cf->get('ChannelId');
				}
			}
			$rdc = new RelStrDocChannels();
			$rdcResult = $rdc->find('IdChannel', 'IdDoc = %s', array($nodeId), MONO);
			foreach ($rdcResult as $idChannel) {
				if (!in_array($idChannel, $channelList)) {
					return false;
				}
			}
		}
		return true;
    }

	/**
	*  Gets the field IdNodeFrame from NodeFrames table which matching the value of nodeId.
	*  @param int nodeId
	*  @return int|null
	*/

    function getNodeFrameByNode($nodeId) {
		$dataFactory = new DataFactory($nodeId);
		$idVersion = $dataFactory->GetLastVersionId();

		$condition = 'NodeId = %s AND VersionId = %s';
		$params = array('NodeId' => $nodeId, 'VersionId' => $idVersion);

		$result = parent::find('IdNodeFrame', $condition, $params, MONO);
		if(!$result || is_null($result)) {
			return null;
		}
		return $result[0];
    }

	/**
    *	Checks whether the NodeFrame has been renamed.
	*	@param int nodeId
	*	@return boolean
	*/

	function isRenamed($nodeId) {

		$condition = 'NodeId = %s ORDER BY IdNodeFrame DESC LIMIT 1';
		$params = array('NodeId' => $nodeId);
		$result = parent::find('IdNodeFrame, Name', $condition, $params, MULTI);

		if (is_null($result)) {
			// En este caso es la primera publicación y me interesa considerarlo 'renamed' para forzar la publicación de ancestros
			return true;
		}

		$idNodeFrame = $result[0]['IdNodeFrame'];
		$name = $result[0]['Name'];
		$node = new Node($nodeId);

		if ($node->get('Name') != $name) {
			XMD_Log::info("Document's name changed: rep. ancestors");
			return true;
		}

		$serverFrame = new ServerFrame();
		$condition = 'IdNodeFrame = %s ORDER BY IdSync DESC LIMIT 1';
		$result = $serverFrame->find('RemotePath', $condition, array('IdNodeFrame' => $idNodeFrame), MONO);
		$path = $result[0];

		if ($path != $node->GetPublishedPath()) {
			XMD_Log::info("Document's path changed: rep. ancestors");
			return true;
		}

		return false;
	}

	/**
	*  Calls for cancel the ServerFrames which matching the value of nodeId.
	*  @param int idNodeFrame
	*  @return unknown
	*/

	function cancelServerFrames($idNodeFrame) {
		$condition = 'IdNodeFrame = %s';
		$params = array('IdNodeFrame' => $idNodeFrame);

		$serverFrame = new ServerFrame();
		$result = $serverFrame->find('IdSync', $condition, $params, MULTI);

		if (sizeof($result) > 0) {
			foreach ($result as $dataFrames) {
				$idServerFrame = $dataFrames[0];

				$serverFrame = new ServerFrame($idServerFrame);
				$serverFrame->set('State', 'Canceled');
				$serverFrame->update();
			}
		}
	}

	/**
	*  Gets the field VersionId from NodeFrames table which matching the value of nodeId.
	*  @param int nodeId
	*  @return int|null
	*/

	function getPublishedVersion($nodeId) {
		$condition = 'NodeId = %s AND Active = 1';
		$params = array('NodeId' => $nodeId);

		$result = parent::find('VersionId', $condition, $params, MONO);

		return sizeof($result) > 0 ? $result[0] : NULL;
	}

	/**
	*  Gets the field IdNodeFrame from NodeFrames table which matching the value of nodeId and is active.
	*  @param int nodeId
	*  @return int|null
	*/

	function getPublishedId($nodeId) {
		$condition = 'NodeId = %s AND Active = 1';
		$params = array('NodeId' => $nodeId);

		$result = parent::find('IdNodeFrame', $condition, $params, MONO);

		return sizeof($result) > 0 ? $result[0] : NULL;
	}

 	/**
	*	Gets the NodeFrame prior to another one.
	*	@param int idNode
	*	@param int idNodeFrame
	*	@return int|null
 	*/

 	function getPrevious($idNode, $idNodeFrame) {
		$condition = 'NodeId = %s AND IdNodeFrame < %s ORDER BY IdNodeFrame DESC';
		$params = array($idNode, $idNodeFrame);

		$result = parent::find('IdNodeFrame', $condition, $params, MONO);

		return sizeof($result) > 0 ? $result[0] : NULL;
	}

	/**
	*  Logs the activity of the NodeFrame.
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

    function NodeFrameToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
					 $class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false) {

    	if(!isset($this->syncStatObj)) {

    		$this->syncStatObj = new SynchronizerStat();
    	}

    	$this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
					 $class, $method, $file, $line, $type, $level, $comment, $doInsertSql);

    }
}
?>
