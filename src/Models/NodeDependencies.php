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

class NodeDependencies
{
    /**
     * @var \Ximdex\Runtime\Db
     */
    private $dbObj;

    public function __construct()
    {
        $this->dbObj = new \Ximdex\Runtime\Db();
    }

    public function set(int $idSource, int $idTarget, int $idChannel = null) : bool
    {
        // Check before if there is already a same dependence
        $sql = 'SELECT * FROM NodeDependencies WHERE IdNode = ' . $idSource . ' and IdResource = ' . $idTarget;
        $res = $this->dbObj->Query($sql);
        if ($idChannel === null) {
            $sql .= ' and IdChannel IS NULL';
        } else {
            $sql .= ' and IdChannel = ' . $idChannel;
        }
        if ($res === false) {
            return false;
        }
        if ($this->dbObj->numRows) {
            
            // Dependency already exists
            return true;
        }
        return $this->dbObj->execute('INSERT INTO NodeDependencies (IdNode, IdResource, IdChannel) VALUES (' . $idSource . ', ' 
            . $idTarget . ', ' . (empty($idChannel) ? 'NULL' : $idChannel) . ')');
    }

    public function getByTarget(int $idTarget) : array
    {
        $this->dbObj->query("SELECT DISTINCT IdNode FROM NodeDependencies WHERE IdResource = $idTarget");
        $deps = array();
        while (! $this->dbObj->EOF) {
            $deps[] = $this->dbObj->getValue("IdNode");
            $this->dbObj->next();
        }
        return $deps;
    }

    public function deleteByTarget(int $idTarget)
    {
        return $this->dbObj->execute("DELETE FROM NodeDependencies WHERE IdResource = $idTarget");
    }

    public function deleteBySource(int $idSource)
    {
        return $this->dbObj->execute("DELETE FROM NodeDependencies WHERE IdNode = $idSource");
    }
}
