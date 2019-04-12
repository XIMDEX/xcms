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

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\Section;
use Ximdex\Logger;

/**
 * @brief Handles ximDEX sections
 */
class SectionNode extends FolderNode
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::createNode()
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, array $subfolders = array()
        , int $idSectionType = null)
    {
        $section = new Section();
        $section->setIdNode($this->parent->get('IdNode'));
        $section->setIdSectionType($idSectionType);
        if (! $section->add()) {
            return false;
        }
        $this->updatePath();
        return true;
    }
    
    /**
     * Gets the documents that must be published together with the section
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::getPublishabledDeps()
     */
    function getPublishabledDeps(array $params = []) : ?array
    {
        $childList = $this->parent->GetChildren();
        $docsToPublish = array();
        foreach ($childList as $childID) {
            $childNode = new Node($childID);
            $childNodeTypeID = $childNode->get('IdNodeType');
            $childNodeType = new NodeType($childNodeTypeID);
            $childNodeTypeName = $childNodeType->get('Name');
            if (isset($params['recursive']) || ($childNodeTypeName != "Section" && !isset($params['recursive']))) {
                $docsToPublish = array_merge($docsToPublish, $childNode->TraverseTree(6));
            }
        }
        return $docsToPublish;
    }

    /**
     * Deletes the Section and its dependencies
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::deleteNode()
     */
    public function deleteNode() : bool
    {
        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        if ($depsMngr->deleteBySource(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        $section = new Section($this->parent->get('IdNode'));
        if ($section->delete() === false) {
            return false;
        }
        Logger::info('Section dependencies deleted');
        return true;
    }
}
