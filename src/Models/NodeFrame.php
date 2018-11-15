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
use Ximdex\Runtime\DataFactory;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Models\ORM\NodeFramesOrm;

/**
*	@brief Handles operations with NodeFrames.
*
*	A NodeFrame is the representation of a node ready to published in a temporal interval.
*	This class includes the methods that interact with the Database.
*/
class NodeFrame extends NodeFramesOrm
{
	/**
	*  Adds a row to NodeFrames table
	*  
	*  @param int nodeId
	*  @param string name
	*  @param int version
	*  @param int up
	*  @param int down
	*  @return int|null
	*/
    public function create($nodeId, $name, $version, $up, $down = NULL)
    {
		$this->set('NodeId', $nodeId);
		$this->set('VersionId', $version);
		$this->set('TimeUp', $up);
		$this->set('TimeDown', $down);
		$this->set('Active', 0);
		$this->set('GetActivityFrom', 0);
		$this->set('IsProcessUp', 0);
		$this->set('IsProcessDown', 0);
		$this->set('Name', $name);
		parent::add();
		$idNodeFrame = $this->get('IdNodeFrame');
		if ($idNodeFrame > 0) {
			return $idNodeFrame;
		}
		Logger::info("ERROR: Creating nodeframe");
		return NULL;
    }

	/**
    *   Gets all ServerFrames associated to a NodeFrame
    *
	*	@param int idNdFr
	*   @param string operation
	*	@return array
	*/
    public function getFrames(int $idNdFr = null, string $operation = null)
    {
        if (!$idNdFr) {
            $idNdFr = $this->IdNodeFrame;
        }
        $sql = 'SELECT IdSync FROM ServerFrames WHERE IdNodeFrame = ' . $idNdFr;
        if ($operation == Batch::TYPE_UP or $operation == Batch::TYPE_DOWN) {
            $sql .= ' and State not in (\'' . ServerFrame::REMOVED . '\', \'' . ServerFrame::REPLACED . '\', \'' 
                . ServerFrame::CANCELLED . '\'';
            $sql .= ', \'' . ServerFrame::OUT . '\')';
        }
		$dbObj = new \Ximdex\Runtime\Db();
		$dbObj->Query($sql);
		$frames = array();
		while (!$dbObj->EOF) {
			$frames[] = $dbObj->GetValue("IdSync");
			$dbObj->Next();
		}
		return $frames;
    }

	/**
    *	Checks whether exists a NodeFrame after a given time
    *
	*	@param int nodeId
	*	@param int up
	*	@param int down
	*	@return boolean
	*/
    public function existsNodeFrame($nodeId, $up, $down = NULL)
    {    
		$dataFactory = new DataFactory($nodeId);
		$idVersion = $dataFactory->GetLastVersionId();
		if (is_null($down)) {
			$condition = 'NodeId = %s AND VersionId = %s AND TimeUp <= %s AND TimeDown IS NULL';
			$params = array($nodeId, $idVersion, $up);
		} else {
			$condition = 'NodeId = %s AND VersionId = %s AND TimeUp <= %s AND (TimeDown IS NULL OR TimeDown = %s)';
			$params = array($nodeId, $idVersion, $up, $down);
		}
		$result = $this->find('IdNodeFrame', $condition, $params, MONO);
		if (empty($result)){
			return false;
		}
		$node = new Node($nodeId);
		if ($node->nodeType->get('IsStructuredDocument') > 0) {
			$channelList = array();
			foreach ($result as $idNodeFrame) {
				$sf = new ServerFrame();
				$sfResult = $sf->find('IdChannelFrame', 'IdNodeFrame = %s', array($idNodeFrame), MONO);
				if (empty($sfResult)) {
					Logger::info('ServerFrame not found for NodeFrame ' . $idNodeFrame);
					return false;
				}
				foreach($sfResult as $idChannelFrame) {
					$cf = new ChannelFrame($idChannelFrame);
					$channelList[] = $cf->get('ChannelId');
				}
			}
			
			// check if the channels from document properties are in the server frame channels list
			$properties = InheritedPropertiesManager::getValues($nodeId, true);
			if (isset($properties['Channel']))
			{
			    $strDoc = new StructuredDocument($nodeId);
			    foreach ($channelList as $channelID) {
			        if (!$strDoc->HasChannel($channelID)) {
			            return false;
			        }
			    }
			}
			else
			{
			    // there is no channels assigned to this document
			    return false;
			}
		}
		return true;
    }

	/**
	 * Calls for cancel the ServerFrames which matching the value of nodeId
	 * Can ignore down server frames (DateDown field is not null)
	 * 
	 * @param int $idNodeFrame
	 * @param bool $ignoreDownFrames
	 */
	public function cancelServerFrames(int $idNodeFrame = null, bool $ignoreDownFrames = false)
	{
	    if (!$idNodeFrame) {
	        $idNodeFrame = $this->IdNodeFrame;
	    }
		$condition = 'IdNodeFrame = %s';
		if ($ignoreDownFrames) {
		    $condition .= ' AND DateDown IS NULL';
		}
		$params = ['IdNodeFrame' => $idNodeFrame];
		$serverFrame = new ServerFrame();
		$result = $serverFrame->find('IdSync', $condition, $params, MULTI);
		if ($result) {
			foreach ($result as $dataFrames) {
			    $serverFrame = new ServerFrame($dataFrames[0]);
				$serverFrame->set('State', ServerFrame::CANCELLED);
				$serverFrame->set('ErrorLevel', null);
				$serverFrame->update();
			}
		}
	}

	/**
	*  Gets the field IdNodeFrame from NodeFrames table which matching the value of nodeId and is active
	*  
	*  @param int nodeId
	*  @return int|null
	*/
	public function getPublishedId($nodeId)
	{
		$condition = 'NodeId = %s AND Active = 1';
		$params = array('NodeId' => $nodeId);
		$result = parent::find('IdNodeFrame', $condition, $params, MONO);
		return count($result) > 0 ? $result[0] : NULL;
	}

 	/**
	*	Gets the NodeFrame prior to another one
	*
	*	@param int idNode
	*	@param int idNodeFrame
	*	@return int|null
 	*/
 	public function getPrevious($idNode, $idNodeFrame)
 	{
		$condition = 'NodeId = %s AND IdNodeFrame < %s ORDER BY IdNodeFrame DESC';
		$params = array($idNode, $idNodeFrame);
		$result = parent::find('IdNodeFrame', $condition, $params, MONO);
		return count($result) > 0 ? $result[0] : NULL;
	}
	
	/**
	 * Get all node frames whose publication period is active in a given timestamp for a specified node ID
	 *
	 * @param int $nodeId
	 * @param int $time
	 * @return array
	 */
	public function getNodeFramesOnDate(int $nodeId, int $time) : array
	{
	    $sql = 'SELECT IdNodeFrame FROM NodeFrames'
	        . ' WHERE NodeId = ' . $nodeId . ' AND TimeUp <= ' . $time;    // AND (TimeDown >= ' . $time . ' OR TimeDown IS NULL)';
	    $dbObj = new \Ximdex\Runtime\Db();
	    $dbObj->Query($sql);
	    $frames = [];
	    while (!$dbObj->EOF) {
	        $frames[] = $dbObj->GetValue('IdNodeFrame');
	        $dbObj->Next();
	    }
	    return $frames;
	}
	
	/**
	 * Get all node frames that will be activated after a given timestamp and specified node ID
	 *
	 * @param int $nodeId
	 * @param int $time
	 * @return array
	 */
	public function getFutureNodeFramesForDate(int $nodeId, int $time) : array
	{
	    $sql = 'SELECT IdNodeFrame FROM NodeFrames WHERE NodeId = ' . $nodeId . ' AND TimeUp > ' . $time . ' AND TimeDown IS NULL';
	    $dbObj = new \Ximdex\Runtime\Db();
	    $dbObj->Query($sql);
	    $frames = [];
	    while (!$dbObj->EOF) {
	        $frames[] = $dbObj->GetValue('IdNodeFrame');
	        $dbObj->Next();
	    }
	    return $frames;
	}
}
