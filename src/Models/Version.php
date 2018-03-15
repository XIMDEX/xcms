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

use Ximdex\Models\ORM\VersionsOrm;

class Version extends VersionsOrm
{
    public function getLastestDocsByUser($idUser)
    {
        $query = "select n.IdNode,n.name,n.IdNodeType,n.path,Version,Subversion,max(Date) from
                        (select * from
                        (select IdVersion, IdNode, Version, Subversion, File, IdUser, Date from Versions order by Date desc) x
                        group by x.IdNode) 
                v inner join Nodes n on n.Idnode=v.Idnode where IdUser=$idUser and n.name not like'%templates_include%' and n.idnodetype not in (" 
                . \Ximdex\NodeTypes\NodeTypeConstants::METADATA_CONTAINER . "," . \Ximdex\NodeTypes\NodeTypeConstants::METADATA_SECTION . ","
                . \Ximdex\NodeTypes\NodeTypeConstants::METADATA_DOCUMENT . ") group by n.IdNode Order by IdVersion desc, path desc,
                    Subversion desc, max(Date)  LIMIT 10";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($query);
        $i = 0;
        $res = array();
        while (!$dbObj->EOF) {
            $res[$i]["IdNode"] = $dbObj->GetValue("IdNode");
            $res[$i]["IdNodeType"] = $dbObj->GetValue("IdNodeType");
            $res[$i]["name"] = $dbObj->GetValue("name");
            $res[$i]["Version"] = $dbObj->GetValue("Version");
            $res[$i]["Subversion"] = $dbObj->GetValue("Subversion");
            $res[$i]["path"] = str_replace("/Ximdex/Projects", "", $dbObj->GetValue("path"));
            $dbObj->Next();
            $i++;
        }
        return $res;
    }

    /**
     * Returns the idVersion for a idNode, version and subversion
     *
     * @param $idNode
     * @param $version
     * @param $subVersion
     * @return null | string
     */
    public function getIdVersion($idNode, $version, $subVersion)
    {
        $res = $this->find('IdVersion', 'IdNode = %s and Version = %s and SubVersion = %s', [$idNode, $version, $subVersion]);
        if( count($res) == 1 ){
            return $res[0]['IdVersion'];
        }
        return null;
    }
}