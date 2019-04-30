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

use Ximdex\Models\Iterators\IteratorNodeSets;
use Ximdex\Models\Iterators\IteratorNodeSetsNodes;
use Ximdex\Models\Iterators\IteratorNodeSetsUsers;
use RelNodeSetsUsers;
use Ximdex\Models\ORM\NodeSetsOrm;

class NodeSets extends NodeSetsOrm
{
    /**
     * Returns the set id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Returns the set name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Returns the sets count
     * 
     * @return boolean|string|NULL
     */
    public function getItems()
    {
        $db = new \Ximdex\Runtime\Db();
        $db->query(sprintf('select count(1) as total from RelNodeSetsNode where IdSet = %s', $this->getId()));
        return $db->getValue('total');
    }

    /**
     * Static method that creates a new NodeSet and returns the related object
     * 
     * @param string $name
     * @return \Ximdex\Models\NodeSets
     */
    static public function create(string $name)
    {
        $ns = new NodeSets();
        $ns->set('Name', $name);
        $ns->add();
        return $ns;
    }

    /**
     * Deletes the current set
     * 
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::delete()
     */
    public function delete()
    {
        $db = new \Ximdex\Runtime\Db();
        $sql = sprintf('delete from RelNodeSetsNode where IdSet = %s', $this->getId());
        $db->execute($sql);
        $sql = 'alter table RelNodeSetsNode auto_increment = 0';
        $db->execute($sql);
        $sql = sprintf('delete from RelNodeSetsUsers where IdSet = %s', $this->getId());
        $db->execute($sql);
        $sql = 'alter table RelNodeSetsUsers auto_increment = 0';
        $db->execute($sql);
        $ret = parent::delete();
        $sql = 'alter table NodeSets auto_increment = 0';
        $db->execute($sql);
        return $ret;
    }

    /**
     * Returns an iterator of all node sets by user
     * 
     * @param int $idUser
     * @return \Ximdex\Models\Iterators\IteratorNodeSets
     */
    static public function getSets(int $idUser)
    {
        $it = new IteratorNodeSetsUsers('IdUser = %s', array($idUser));
        $sets = array();
        while ($set = $it->next()) {
            $sets[] = $set->getIdSet();
        }
        return new IteratorNodeSets('Id in (%s)', array(implode(',', $sets)), NO_ESCAPE);
    }

    /**
     * Returns an iterator of all node sets
     * 
     * @return \Ximdex\Models\Iterators\IteratorNodeSets
     */
    static public function getAllSets()
    {
        return new IteratorNodeSets('', array());
    }

    /**
     * Returns an iterator of all related nodes in this set
     * 
     * @return \Ximdex\Models\Iterators\IteratorNodeSetsNodes
     */
    public function getNodes()
    {
        return new IteratorNodeSetsNodes('IdSet = %s', array($this->getId()));
    }

    /**
     * Adds a new node to the current set and returns the related object
     * 
     * @param int $idNode
     * @return \Ximdex\Models\RelNodeSetsNode
     */
    public function addNode(int $idNode)
    {
        return RelNodeSetsNode::create($this->getId(), $idNode);
    }

    /**
     * Deletes a node from the current set
     * 
     * @param int $idNode
     * @return \Ximdex\Models\RelNodeSetsNode
     */
    public function deleteNode(int $idNode)
    {
        $rel = new RelNodeSetsNode();
        $rel = $rel->find(ALL, 'IdSet = %s and IdNode = %s', array($this->getId(), $idNode));
        if (count($rel) > 0 && $rel[0]['Id'] > 0) {
            $rel = new RelNodeSetsNode($rel[0]['Id']);
            $rel->delete();
        }
        return $rel;
    }

    /**
     * Returns an iterator of all related users can see this set
     * 
     * @return \Ximdex\Models\Iterators\IteratorNodeSetsUsers
     */
    public function getUsers()
    {
        return new IteratorNodeSetsUSers('IdSet = %s', array($this->getId()));
    }

    /**
     * Adds a new user to the current set and returns the related object
     * 
     * @param int $idUser
     * @param int $owner
     * @return RelNodeSetsUsers
     */
    public function addUser(int $idUser, int $owner = RelNodeSetsUsers::OWNER_NO)
    {
        return RelNodeSetsUsers::create($this->getId(), $idUser, $owner);
    }

    /**
     * Deletes an user from the current set
     * 
     * @param int $idUser
     * @return \RelNodeSetsUsers
     */
    public function deleteUser(int $idUser)
    {
        $rel = new RelNodeSetsUsers();
        $rel = $rel->find(ALL, 'IdSet = %s and IdUser = %s', array($this->getId(), $idUser));
        if (count($rel) > 0 && $rel[0]['Id'] > 0) {
            $rel = new RelNodeSetsUsers($rel[0]['Id']);
            $rel->delete();
        }
        return $rel;
    }
}