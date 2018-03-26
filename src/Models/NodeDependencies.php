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

use Ximdex\Runtime\Db;

class NodeDependencies
{
    /**
     * @var Db
     */
    var $dbObj;

    /**
     * NodeDependencies constructor.
     */
    public function __construct()
    {
        $this->dbObj = new \Ximdex\Runtime\Db();
    }

    /**
     * @param $idSource
     * @param $idTarget
     * @param $idChannel
     * @return bool
     */
    public function set($idSource, $idTarget, $idChannel)
    {
        //check before if there is already a same dependence
        $res = $this->dbObj->Query("SELECT * FROM NodeDependencies WHERE IdNode = '$idSource' and IdResource = '$idTarget' and IdChannel = '$idChannel'");
        if ($res === false)
            return false;
        if ($this->dbObj->numRows)
        {
            //dependency already exists
            return true;
        }
        return $this->dbObj->Execute("INSERT INTO NodeDependencies (IdNode, IdResource, IdChannel) VALUES ('$idSource', '$idTarget', " 
            . (empty($idChannel)? 'null' : "'$idChannel'") . ")");
    }

    /**
     * @param $idTarget
     * @return array
     */
    public function getByTarget($idTarget)
    {
        $this->dbObj->Query("SELECT DISTINCT IdNode FROM NodeDependencies WHERE IdResource = $idTarget");
        $deps = array();
        while (!$this->dbObj->EOF) {
            
            $deps[] = $this->dbObj->GetValue("IdNode");
            $this->dbObj->Next();
        }
        return $deps;
    }

    /**
     * @param $idTarget
     * @return bool
     */
    public function deleteByTarget($idTarget)
    {
        return $this->dbObj->Execute("DELETE FROM NodeDependencies WHERE IdResource = $idTarget");
    }

    /**
     * @param $idSource
     * @return bool
     */
    public function deleteBySource($idSource)
    {
        return $this->dbObj->Execute("DELETE FROM NodeDependencies WHERE IdNode = $idSource");
    }
}
