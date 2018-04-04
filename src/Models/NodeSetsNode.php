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

namespace Ximdex\Models;

use Ximdex\Models\ORM\RelNodeSetsNodeOrm;

class RelNodeSetsNode extends RelNodeSetsNodeOrm
{
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Returns the rel id
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the set id
     */
    public function getIdSet()
    {
        return $this->IdSet;
    }

    /**
     * Returns the node id
     */
    public function getIdNode()
    {
        return $this->IdNode;
    }

    /**
     * Returns the node object
     */
    public function & getNode()
    {
        $node = new Node($this->IdNode);
        return $node;
    }

    /**
     * Return the set that is associated to
     */
    public function & getSet()
    {
        $set = new NodeSets($this->getIdSet());
        return $set;
    }

    /**
     * Static method that creates a new NodeSet relation and returns the related object
     * 
     * @param $idSet
     * @param $idNode
     * @return RelNodeSetsNode
     */
    static public function & create($idSet, $idNode)
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
        $ret = parent::delete();
        return $ret;
    }
}