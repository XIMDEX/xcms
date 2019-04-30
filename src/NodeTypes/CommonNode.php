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

use Ximdex\Properties\ChannelProperty;
use Ximdex\Utils\FsUtils;
use Ximdex\Models\Channel;
use Ximdex\Models\Metadata;
use Ximdex\Models\Node;

/**
 * Class for NodeType common
 */
class CommonNode extends FileNode
{   
    /**
     * Return an array with all the channels ID for the current node
     * 
     * @return array|null
     */
    public function getChannels() : ?array
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
        $values = $channelProperty->getValues(true);
        if ($values === false) {
            
            // Error
            return null;
        }
        $channels = [];
        foreach ($values as $channel) {
            $channel = new Channel($channel['Id']);
            if (! $channel->GetID()) {
                
                // Error
                return null;
            }
            
            // Only channels with render type Index will be returned
            if ($channel->getRenderType() == Channel::RENDERTYPE_INDEX) {
                $channels[] = $channel->GetID();
            } else {
                $channels[] = null;
            }
        }
        return $channels;
    }
    
    /**
     * Return the content of metadata
     * 
     * @param int $id
     * @return array
     */
    public static function getMetadata(int $id) : array
    {
        $node = new Node($id);
        $metadata = (new Metadata)->getMetadataSchemeAndGroupByNodeType($node->getNodeType(), $id);
        return $metadata;
    }
    
    public static function prepareMetadata(array $metadata) : array
    {
        $result = [];
        foreach ($metadata as $meta) {
            if (key_exists('groups', $meta) || key_exists('metadata', $meta)) {
                $result = array_merge($result, static::prepareMetadata($meta['groups'] ?? $meta['metadata'] ?? []));
                continue;
            }
            if (! $meta['value']) {
                continue;
            }
            if ($meta['type'] == Metadata::TYPE_DATE) {
                $meta['value'] = "{$meta['value']}T00:00:00Z";
            } elseif ($meta['type'] == Metadata::TYPE_IMAGE){
                $meta['value'] = "@@@RMximdex.pathto({$meta['value']})@@@";
            }
            $result[$meta['name']] = $meta['value'];
        }
        return $result;
    }
}
