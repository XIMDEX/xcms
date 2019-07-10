<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
    public function getLastestDocsByUser(int $idUser) : array
    {
        $query = 'select n.IdNode,n.name,n.IdNodeType,n.path,Version,Subversion,max(Date) from
                        (select * from
                        (select IdVersion, IdNode, Version, Subversion, File, IdUser, Date from Versions order by Date desc) x
                        group by x.IdNode) 
                v inner join Nodes n on n.Idnode = v.Idnode where IdUser = ' . $idUser  . ' and n.name not like \'%templates_include%\' 
                group by n.IdNode Order by IdVersion desc, path desc, Subversion desc, max(Date)  LIMIT 10';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        $i = 0;
        $res = [];
        while (! $dbObj->EOF) {
            $res[$i]['IdNode'] = $dbObj->getValue('IdNode');
            $res[$i]['IdNodeType'] = $dbObj->getValue('IdNodeType');
            $res[$i]['name'] = $dbObj->getValue('name');
            $res[$i]['Version'] = $dbObj->getValue('Version');
            $res[$i]['Subversion'] = $dbObj->getValue('Subversion');
            $res[$i]['path'] = str_replace('/Ximdex/Projects', '', $dbObj->getValue('path'));
            $dbObj->next();
            $i++;
        }
        return $res;
    }

    /**
     * Returns the idVersion for a idNode, version and subversion
     * 
     * @param int $idNode
     * @param int $version
     * @param int $subversion
     * @return int|NULL
     */
    public function getIdVersion(int $idNode, int $version, int $subversion) : ?int
    {
        $res = $this->find('IdVersion', 'IdNode = %s and Version = %s and SubVersion = %s', [$idNode, $version, $subversion]);
        if ($res and count($res) == 1){
            return $res[0]['IdVersion'];
        }
        return null;
    }
    
    /**
     * Remove all cache generated for the current version
     * 
     * @throws \Exception
     */
    public function deleteCache()
    {
        if (! $this->IdVersion) {
            throw new \Exception('No version specified to delete cache');
        }
        $cache = new TransitionCache();
        $cache = $cache->find('id', 'versionId = ' . $this->IdVersion, null, MONO);
        foreach ($cache as $id) {
            $cache = new TransitionCache($id);
            $cache->delete();
        }
    }
}
