<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Models\Channel;
use Ximdex\Models\NodeProperty;
use Ximdex\Models\ServerFrame;

/**
 * @brief Handles channels
 *
 * Channels are responsible of the document transformation to different output formats (html, text, ...)
 */
class ChannelNode extends Root
{
	/**
	 * Calls to method for creating a Channel
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::createNode()
	 */
	public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $channelName = null
	    , string $extension = null, string $format = null, string $description = null, string $filter = "", string $renderMode = null
	    , string $outputType = null, string $renderType = null, string $language = null)
	{
		$channel = new Channel();
		$channel->createNewChannel($channelName, $extension, $format, $description, $this->parent->get('IdNode'), $filter,
			 $renderMode, $outputType, $renderType, $language);
		$this->updatePath();
		return true;
	}

	/**
	 * Deletes the rows of the Channel from both tables Channels and NodeProperties
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::deleteNode()
	 */
	public function deleteNode() : bool
	{
	    if (! $this->nodeID) {
	        return false;
	    }
	    $channel = new Channel($this->nodeID);
	    if (! $channel->getID()) {
	        return false;
	    }
	    if ($channel->hasServers()) {
	        $this->messages->add('This channel is in use by any server. deletion denied', MSG_TYPE_ERROR);
	        return false;
	    }
	    
	    // Remove related server frames with sync files
	    $serverFrame = new ServerFrame();
	    $serverFrames = $serverFrame->find('IdSync', 'ChannelId = ' . $this->nodeID, null, MONO);
	    if ($serverFrames === false) {
	        return false;
	    }
	    foreach ($serverFrames as $id) {
	        $serverFrame = new ServerFrame($id);
	        if (! $serverFrame->get('IdSync')) {
	            continue;
	        }
	        if ($serverFrame->deleteSyncFile() === false) {
	            return false;
	        }
	        if ($serverFrame->delete() === false) {
	            return false;
	        }
	    }
	    $nodeProperty = new NodeProperty();
	    if ($nodeProperty->cleanUpPropertyValue('channel', $this->parent->get('IdNode')) === false) {
	        return false;
	    }
		if ($channel->deleteChannel() === false) {
		    return false;
		}
		return true;
	}
}
