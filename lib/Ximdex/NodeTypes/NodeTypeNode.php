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

use canAttachGroups;
use description;
use hasFSEntity;
use icon;
use isContentNode;
use isRenderizable;
use name;
use Ximdex\Models\NodeType;


/**
 * @brief Manages the NodeTypes as ximDEX Nodes.
 */
class NodeTypeNode extends Root
{

    /**
     *  Does nothing.
     * @return null
     */

    function RenderizeNode()
    {

        return null;
    }

    /**
     *  Calls to method for adding a row to Actions table.
     * @param string name
     * @param int parentID
     * @param int nodeTypeID
     * @param int stateID
     * @param string icon
     * @param int isRenderizable
     * @param int hasFSEntity
     * @param int canAttachGroups
     * @param int isContentNode
     * @param string description
     * @param string class
     * @return unknown
     */

    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $icon = null, $isRenderizable = null, $hasFSEntity = null, $canAttachGroups = null, $isContentNode = null, $description = null, $class = null)
    {

        $nodeType = new NodeType();
        $nodeType->CreateNewNodeType($name, $icon, $isRenderizable, $hasFSEntity, $canAttachGroups, $isContentNode,
            $description, $class, $this->parent->get('IdNode'));

        $this->UpdatePath();
    }

    /**
     *  Calls to method for deleting.
     * @return unknown
     */

    function DeleteNode()
    {

        $ntype = new NodeType($this->nodeID);
        $ntype->DeleteNodeType();
    }
}
