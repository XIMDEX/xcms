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
    public function create(int $nodeId, string $name, int $version, int $up, int $idPortal, int $down = null)
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
		$this->set('IdPortalFrame', $idPortal);
		parent::add();
		if ($idNodeFrame = $this->get('IdNodeFrame')) {
			return $idNodeFrame;
		}
		Logger::error('Creating nodeframe');
		return null;
    }

	/**
	 * Gets all ServerFrames associated to a NodeFrame
	 * 
	 * @param int $idNdFr
	 * @param string $operation
	 * @param array $enabledServers
	 * @return array
	 */
    public function getFrames(int $idNdFr = null, string $operation = null, array $enabledServers = []) : array
    {
        if (! $idNdFr) {
            $idNdFr = $this->IdNodeFrame;
        }
        $sql = 'SELECT IdSync FROM ServerFrames WHERE IdNodeFrame = ' . $idNdFr;
        if ($operation == Batch::TYPE_UP or $operation == Batch::TYPE_DOWN) {
            $sql .= ' AND State NOT IN (\'' . ServerFrame::REMOVED . '\', \'' . ServerFrame::REPLACED . '\', \'' 
                . ServerFrame::CANCELLED . '\', \'' . ServerFrame::OUT . '\')';
        }
        if ($enabledServers) {
            $sql .= ' AND IdServer IN (' . implode(', ', $enabledServers) . ')';
        }
		$dbObj = new \Ximdex\Runtime\Db();
		$dbObj->Query($sql);
		$frames = array();
		while (! $dbObj->EOF) {
			$frames[] = $dbObj->GetValue('IdSync');
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
    public function existsNodeFrame(int $nodeId, int $up, int $down = null)
    {
		$dataFactory = new DataFactory($nodeId);
		$idVersion = $dataFactory->GetPublishedIdVersion();
		$condition = 'NodeId = %s AND VersionId = %s AND TimeUp <= %s AND IsProcessDown = 0';
		if (is_null($down)) {
			$condition .= ' AND TimeDown IS NULL';
			$params = array($nodeId, $idVersion, $up);
		} else {
			$condition .= ' AND TimeDown = %s';
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
			
			// Check if the channels from document properties are in the server frame channels list
			$properties = InheritedPropertiesManager::getValues($nodeId, true);
			if (isset($properties['Channel']))
			{
			    $strDoc = new StructuredDocument($nodeId);
			    foreach ($channelList as $channelID) {
			        if (! $strDoc->HasChannel($channelID)) {
			            return false;
			        }
			    }
			}
			else
			{
			    // There is no channels assigned to this document
			    return false;
			}
		}
		return true;
    }

    /**
     * Cancel server frames for this node frame
     * 
     * @param bool $force
     * @throws \Exception
     */
    public function cancel(bool $force = true) : void
    {
        if (! $this->IdNodeFrame) {
            throw new \Exception('No node frame ID given in order to cancel it');
        }
        $this->set('IsProcessUp', 1);
        $this->set('IsProcessDown', 1);
        $this->update();
        $this->cancelServerFrames(false, $force);
    }

	/**
	*  Gets the field IdNodeFrame from NodeFrames table which matching the value of nodeId and is active
	*  
	*  @param int nodeId
	*  @return int|null
	*/
	public function getPublishedId(int $nodeId)
	{
		$condition = 'NodeId = %s AND Active = 1';
		$params = array('NodeId' => $nodeId);
		$result = parent::find('IdNodeFrame', $condition, $params, MONO);
		return count($result) > 0 ? $result[0] : null;
	}

 	/**
	*	Gets the NodeFrame prior to another one
	*
	*	@param int idNode
	*	@param int idNodeFrame
	*	@return int|null
 	*/
 	public function getPrevious(int $idNode, int $idNodeFrame)
 	{
		$condition = 'NodeId = %s AND IdNodeFrame < %s ORDER BY IdNodeFrame DESC';
		$params = array($idNode, $idNodeFrame);
		$result = parent::find('IdNodeFrame', $condition, $params, MONO);
		return count($result) > 0 ? $result[0] : null;
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
	    $sql = 'SELECT IdNodeFrame FROM NodeFrames' . ' WHERE NodeId = ' . $nodeId . ' AND TimeUp <= ' . $time . ' AND IsProcessDown = 0';
	    $dbObj = new \Ximdex\Runtime\Db();
	    $dbObj->Query($sql);
	    $frames = [];
	    while (! $dbObj->EOF) {
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
	 * @param array $enabledServers
	 * @return array
	 */
	public function getFutureNodeFramesForDate(int $nodeId, int $time) : array
	{
	    $sql = 'SELECT IdNodeFrame FROM NodeFrames WHERE NodeId = ' . $nodeId . ' AND TimeUp > ' . $time . ' AND TimeDown IS NULL';
	    $sql .= ' AND IsProcessDown = 0';
	    $dbObj = new \Ximdex\Runtime\Db();
	    $dbObj->Query($sql);
	    $frames = [];
	    while (! $dbObj->EOF) {
	        $frames[] = $dbObj->GetValue('IdNodeFrame');
	        $dbObj->Next();
	    }
	    return $frames;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ximdex\Data\GenericData::update()
	 */
	public function update()
	{
	    $updatedFields = $this->updatedFields;
	    if ($res = parent::update()) {
	        if (isset($updatedFields['Active'])) {
	            
	            // Update the active node
	            try {
                    $this->updateActiveNode();
	            } catch (\Exception $e) {
	                Logger::error($e->getMessage());
	                return false;
	            }
	        }
	    }
	    return $res;
	}
	
	/**
	 * Increase the total of server frames in IN state for this node frame ( +1 )
	 * 
	 * @return bool
	 */
	public function increaseServerFrameIN() : bool
	{
	    $sql = 'UPDATE NodeFrames SET SF_IN = SF_IN + 1 WHERE IdNodeFrame = ' . $this->IdNodeFrame;
	    $dbObj = new \Ximdex\Runtime\Db();
	    return (bool) $dbObj->execute($sql);
	}
	
	/**
	 * Increase the total of server frames in OUT state for this node frame ( +1 )
	 * 
	 * @return bool
	 */
	public function increaseServerFrameOUT() : bool
	{
	    $sql = 'UPDATE NodeFrames SET SF_OUT = SF_OUT + 1 WHERE IdNodeFrame = ' . $this->IdNodeFrame;
	    $dbObj = new \Ximdex\Runtime\Db();
	    return (bool) $dbObj->execute($sql);
	}
	
	/**
	 * Calls for cancel the ServerFrames which matching the value of nodeId
	 * Can ignore down server frames (DateDown field is not null)
	 *
	 * @param bool $ignoreDownFrames
	 * @param bool $force
	 */
	private function cancelServerFrames(bool $ignoreDownFrames = false, bool $force = true) : void
	{
	    $condition = 'IdNodeFrame = %s';
	    if ($ignoreDownFrames) {
	        $condition .= ' AND DateDown IS NULL';
	    }
	    $params = ['IdNodeFrame' => $this->IdNodeFrame];
	    $serverFrame = new ServerFrame();
	    $result = $serverFrame->find('IdSync', $condition, $params, MULTI);
	    if ($result) {
	        foreach ($result as $dataFrames) {
	            $serverFrame = new ServerFrame($dataFrames[0]);
	            if ($serverFrame->get('State') == ServerFrame::CANCELLED) {
	                continue;
	            }
	            try {
	                $serverFrame->cancel($force);
	            } catch (\Exception $e) {
	                Logger::error($e->getMessage());
	            }
	        }
	    }
	}
	
	private function updateActiveNode()
	{
	    if (! $this->IdNodeFrame) {
	        throw new \Exception('No given ID for node frame in order to update the active node');
	    }
	    $node = new Node($this->NodeId);
	    if (! $node->GetID()) {
	        throw new \Exception('No given ID for node in order to update the active node frame');
	    }
	    if ($this->Active) {
	        
	        // Activate the node
	        if ($node->ActiveNF == $this->IdNodeFrame) {
	            
	            // Node frame is already the active in the related node
	            return;
	        }
	        $nodeFrameActive = $this->IdNodeFrame;
	    } else {
	        
	        // Deactivate the node
	        if ($node->ActiveNF != $this->IdNodeFrame) {
	            
	            // Node frame is not the active in the related node
	            return;
	        }
	        $nodeFrameActive = null;
	    }
	    $node->set('ActiveNF', $nodeFrameActive);
	    if ($node->update() === false) {
	        throw new \Exception('Cannot update the active node frame in node with ID: ' . $node->GetID());
	    }
	}
}
