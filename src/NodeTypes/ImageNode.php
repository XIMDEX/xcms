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
use DOMDocument;
use Ximdex\Models\Node;


/***
 * Class for NodeType Image
 */
class ImageNode extends FileNode
{

    /**
     * Build a new image node file.
     * Use parent CreateNode method and generate a new metadata document for the new image node created.
     * @return boolean true.
     */
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = 7, $sourcePath = "")
    {
        parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);
        /*
        $mm = new \Ximdex\Metadata\MetadataManager($this->nodeID);
        $mm->generateMetadata();
        $mm->updateSystemMetadata();
        */
    }

    /**
     * Delete image node and the metadata asociated.
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

    function GetCustomMetadata(){
        $domNode = new DOMDocument('1.0', 'utf-8');
        $domNode->formatOutput = true;
        $domNode->preserveWhiteSpace = false;

        $width = $domNode->createElement("width");
        $height = $domNode->createElement('height');

        $node = new Node($this->nodeID);
        $info = $node->GetLastVersion();
        /*
        $pathToFile = XIMDEX_ROOT_PATH . '/data/files/' . $info['File'];
        list($w, $h) =  getimagesize($pathToFile);
        $width->nodeValue = $w;
        $height->nodeValue = $h;


        */

        $fileData = $domNode->createElement("file_data");
        $fileData->appendChild($width);
        $fileData->appendChild($height);
        $domNode->appendChild($fileData);
        return $fileData;
    }

}