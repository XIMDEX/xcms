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

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\Section;
use Ximdex\Logger;

/**
 * @brief Handles ximDEX sections.
 */
class SectionNode extends FolderNode
{
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $subfolders = array(), $idSectionType = null)
    {
        $section = new Section();
        $section->setIdNode($this->parent->get('IdNode'));
        $section->setIdSectionType($idSectionType);
        if (!$section->add()) {
            return false;
        }
        $this->updatePath();
    }
    
    /**
     * Gets the documents that must be published together with the section
     * 
     * @param array params
     * @return array
     */
    function getPublishabledDeps($params)
    {
        $childList = $this->parent->GetChildren();
        /*
        $node = new Node($this->parent->get('IdNode'));
        $idNodeType = $node->get('IdNodeType');
        $nodeType = new NodeType($idNodeType);
        $sectionId = null;
        */
        $docsToPublish = array();
        foreach ($childList as $childID) {
            $childNode = new Node($childID);
            $childNodeTypeID = $childNode->get('IdNodeType');
            $childNodeType = new NodeType($childNodeTypeID);
            $childNodeTypeName = $childNodeType->get('Name');
            if (isset($params['recurrence']) || ($childNodeTypeName != "Section" && !isset($params['recurrence']))) {
                $docsToPublish = array_merge($docsToPublish, $childNode->TraverseTree(6));
            }
        }
        return $docsToPublish;
    }

    /**
     * Deletes the Section and its dependencies
     */
    function DeleteNode()
    {
        $section = new Section($this->parent->get('IdNode'));
        if ($section->delete() === false) {
            return false;
        }
        
        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        $depsMngr->deleteBySource(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode'));
        Logger::info('Section dependencies deleted');
    }
}