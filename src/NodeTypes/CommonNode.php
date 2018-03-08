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

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;

/***
 * Class for NodeType common
 */
class CommonNode extends FileNode
{
    /**
     * Build a new common node file.
     * Use parent CreateNode method and generate a new metadata document for the new common node created.
     * @return boolean true.
     */
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = 7, $sourcePath = "")
    {
        parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->generateMetadata();
        $mm->updateSystemMetadata();
    }

    /**
     * Delete the common file node and its metadata asociated.
     */
    function DeleteNode()
    {
        parent::DeleteNode();
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->deleteMetadata();
    }

    function RenameNode($name = null)
    {
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->updateSystemMetadata();
    }

    function SetContent($content, $commitNode = NULL, Node $node = null)
    {
        parent::SetContent($content, $commitNode, $node);
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->updateSystemMetadata();
    }

    /**
     * Get the mime type of the file
     * @param Node $node
     * @return boolean|string
     */
    public function getMimeType(Node $node)
    {
        $info = pathinfo($node->GetNodeName());
        if (strtolower($info['extension']) == 'css') {
            
            // CSS files return text/plain by default
            $mimeType = 'text/css';
        }
        else {
            
            // Obtain the mime type from the last version of the file
            $version = $node->GetLastVersion();
            if (!isset($version['IdVersion']) or !$version['IdVersion']) {
                Logger::error('There is no a version for node: ' . $node->GetID());
                return false;
            }
            $versionID = $version['IdVersion'];
            $version = new Version($versionID);
            $file = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $version->get('File');
            if (!file_exists($file)) {
                Logger::error('Cannot load the file: ' . $file . ' for version: ' . $versionID);
                return false;
            }
            $mimeType = mime_content_type($file);
            if (!$mimeType) {
                Logger::error('Cannot load the mime type for the file: ' . $file);
                return false;
            }
        }
        return $mimeType;
    }
}