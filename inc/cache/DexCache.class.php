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
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;


/**
 * Class DexCache
 */
class DexCache
{


    /**
     * DexCache constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $idNode
     * @return bool
     */
    public static function isModified($idNode)
    {

        if (is_null($idNode)) {
            return true;
        }

        $df = new DataFactory($idNode);

        $version = $df->GetLastVersion();
        $subversion = $df->GetLastSubVersion($version);
        $idCurrentVersion = $df->getVersionId($version, $subversion);

        $dcdb = new DexCacheDB();
        $data = $dcdb->read('idNode', $idNode);

        $idPublishedVersion = $data['idVersion'];


        if ($idCurrentVersion != $idPublishedVersion) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $idNode
     * @param $channelId
     * @param $c
     * @return bool
     */
    static public function createPersistentSyncFile($idNode, $channelId, $c)
    {
        // Creating a persistent copy of sync.
        $name = self::_createName($idNode, $channelId);
        return FsUtils::file_put_contents($name, $c);
    }

    /**
     * @param $idNode
     * @param $channelId
     * @return string
     */
    static public function _createName($idNode, $channelId)
    {
        return XIMDEX_ROOT_PATH . App::getValue('SyncRoot') . "/$idNode.$channelId.cache";
    }

    /**
     * @param $idNode
     * @param $syncs
     * @param $idVersion
     */
    function setRelation($idNode, $syncs, $idVersion)
    {


        //TODO:: To have into account channels, because they generate two different synchro files.

        // Delete first older relationd for idnode.
        $dcdb = new DexCacheDB();
        $dcdb->delete('idNode', $idNode);

        if (!is_array($syncs)) {
            $syncs = array($syncs);

        }

        foreach ($syncs as $idSync) {
            $dcdb->idNode = $idNode;
            $dcdb->idSync = $idSync;
            $dcdb->idVersion = $idVersion;
            $dcdb->commit();
        }
    }

    /**
     * @param $idNode
     * @return bool|null
     */
    function getLastVersionOfNode($idNode)
    {

        $df = new DataFactory($idNode);

        $version = $df->GetLastVersion();
        $subversion = $df->GetLastSubversion($version);

        $IdVersion = $df->getVersionId($version, $subversion);

        return $IdVersion;
    }

    /**
     * @param $idNode
     * @param $channelId
     * @return null|string
     */
    static public function &getPersistentSyncFile($idNode, $channelId)
    {

        //echo "DexCache::getPersistentSyncFile($idNode)<br/>\n";

        $name = DexCache::_createName($idNode, $channelId);

        $c = file_get_contents($name);

        if ($c) {
            return $c;
        } else {
            return NULL;
        }
    }
}