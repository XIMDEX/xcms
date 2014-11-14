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



ModulesManager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
ModulesManager::file('/conf/synchro.conf', 'ximSYNC');
ModulesManager::file('/inc/model/orm/ChannelFrames_ORM.class.php', 'ximSYNC');

/**
*	@brief Handles operations with ChannelFrames.
*
*	A ChannelFrame stores the relationship between a NodeFrame and the channels in wich the Node will be published.
*	This class includes the methods that interact with the Database.
*/

class ChannelFrame extends ChannelFrames_ORM {

	/**
	*  Adds a row to ChannelFrames table.
	*  @param int channel
	*  @param int idNode
	*  @return int|null
	*/

    function create($channel, $idNode) {

		if ($channel == 'NULL') {
			$channel = NULL;
		}

		$this->set('ChannelId',$channel);
		$this->set('NodeId',$idNode);

		parent::add();
		$idChannelFrame = $this->get('IdChannelFrame');

		if ($idChannelFrame > 0) {
			return $idChannelFrame;
		}

		XMD_Log::info("ERROR Creando el channelFrame");
		return NULL;
    }

	/**
	*  Gets the IdChannelFrame from ChannelFrames table which matching the value of nodeId and it is the newest.
	*  @param int nodeID
	*  @param int channelID
	*  @return int|null
	*/

	function getLast($nodeID, $channelID = null) {
		if (is_null($channelID)) {
			$params = array( 'NodeId' => $nodeID );
			$condition = "NodeId = %s";
		} else {
			$params = array( 'ChannelId' => $channelID, 'NodeId' => $nodeID );
			$condition = "ChannelId = %s AND NodeId = %s";
		}

		$result = $this->find('IdChannelFrame',
                        $condition . ' ORDER BY IdChannelFrame DESC',
                        $params, MONO);
		if (is_null($result)) {
			return null;
		}

		return $result;
	}

}
?>