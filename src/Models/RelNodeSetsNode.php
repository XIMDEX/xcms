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

namespace Ximdex\Models;

use Ximdex\Models\ORM\RelNodeSetsNodeOrm;

class RelNodeSetsNode extends RelNodeSetsNodeOrm
{
    /**
     * Returns the rel id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the set id
     * 
     * @return int
     */
    public function getIdSet()
    {
        return $this->IdSet;
    }

    /**
     * Returns the node id
     * 
     * @return int
     */
    public function getIdNode()
    {
        return $this->IdNode;
    }

    /**
     * Returns the node object
     * 
     * @return \Ximdex\Models\Node
     */
    public function getNode()
    {
        return new Node($this->IdNode);
    }

    /**
     * Return the set that is associated to
     * 
     * @return \Ximdex\Models\NodeSets
     */
    public function getSet()
    {
        return new NodeSets($this->getIdSet());
    }

    /**
     * Static method that creates a new NodeSet relation and returns the related object
     * 
     * @param int $idSet
     * @param int $idNode
     * @return \Ximdex\Models\RelNodeSetsNode
     */
    static public function create(int $idSet, int $idNode)
    {
        $rel = new RelNodeSetsNode();
        $node = new Node($idNode);
        if ($node->get('IdNode') <= 0) {
            $rel->messages->add("Can't append the node to the set, the node id $idNode doesn't exists.", MSG_TYPE_ERROR);
        } else {
            $rel->set('IdSet', $idSet);
            $rel->set('IdNode', $idNode);
            $rel->add();
        }
        return $rel;
    }

    public function delete()
    {
        return parent::delete();
    }
}
