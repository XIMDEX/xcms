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

use Ximdex\Models\NodeSets;
use Ximdex\Models\ORM\RelNodeSetsUsersOrm;
use Ximdex\Models\User;

class RelNodeSetsUsers extends RelNodeSetsUsersOrm
{
    const OWNER_YES = 1;
    
    const OWNER_NO = 0;

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
     * Returns the user id
     * 
     * @return int
     */
    public function getIdUser()
    {
        return $this->IdUser;
    }

    /**
     * Returns the owner attribute
     * 
     * @return number
     */
    public function getOwner()
    {
        return $this->Owner;
    }

    /**
     * Returns the user object
     * 
     * @return \Ximdex\Models\User
     */
    public function getUser()
    {
        return new User($this->IdUser);
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
     * Static method that creates a new SetUser relation and returns the related object
     * 
     * @param int $idSet
     * @param int $idUser
     * @param int $owner
     * @return RelNodeSetsUsers
     */
    static public function create(int $idSet, int $idUser, int $owner = RelNodeSetsUsers::OWNER_NO)
    {
        $rel = new RelNodeSetsUsers();
        $user = new User($idUser);
        if ($user->get('IdUser') <= 0) {
            $rel->messages->add("Can't append the user to the set, the user id $idUser doesn't exists.", MSG_TYPE_ERROR);
        } else {
            $rel->set('IdSet', $idSet);
            $rel->set('IdUser', $idUser);
            $rel->set('Owner', $owner);
            $rel->add();
        }
        return $rel;
    }

    /**
     * Returns a RelNodeSetsUsers instance for a specific user and set
     * 
     * @param $idSet
     * @param $idUser
     * @return null|RelNodeSetsUsers
     */
    static public function getByUserId(int $idSet, int $idUser)
    {
        $ret = null ;
        $rel = new RelNodeSetsUsers();
        $rel = $rel->find(ALL, 'IdSet = %s and IdUser = %s', array($idSet, $idUser));
        if ($rel) {
            $ret = new RelNodeSetsUsers($rel['Id']);
        }
        return $ret;
    }
}
