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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Properties\ChannelProperty;
use Ximdex\Utils\FsUtils;

/**
 * Class for NodeType common
 */
class CommonNode extends FileNode
{
    /**
     * Build a new common node file.
     * Use parent CreateNode method and generate a new metadata document for the new common node created.
     * @return boolean true.
     */
    public function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = 7, $sourcePath = "")
    {
        parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);
    }
    
    /**
     * Return an array with all the channels ID for the current node
     * 
     * @return array
     */
    public function GetChannels() : ?array
    {
        // Only binary file will be published with channel frame
        if ($this->nodeType->GetID() != NodeTypeConstants::BINARY_FILE) {
            return [];
        }
        
        // Only PDF files will be published with channel frame
        $extension = FsUtils::get_extension($this->parent->GetNodeName());
        if (strtolower($extension) != 'pdf') {
            return [];
        }
        $channelProperty = new ChannelProperty($this->nodeID);
        $values = $channelProperty->getValues($this->nodeID, true);
        if ($values === false) {
            return null;
        }
        $res = [];
        foreach ($values as $channel) {
            $res[] = $channel['Id'];
        }
        return $res;
    }
}