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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . '/inc/cache/DexCacheDB.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');

/**
 *  
 */
class DexCache {

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function DexCache() {
	}

	/**
	 * 
	 * @param $idNode
	 * @param $syncs
	 * @param $idVersion
	 * @return unknown_type
	 */
	function setRelation($idNode, $syncs, $idVersion) {

		//echo "DexCache::setRelation($idNode, $syncs, $idVersion)<br/>\n";

		//TODO:: To have into account channels, because they generate two different synchro files.

		// Delete first older relationd for idnode.
		$dcdb = new DexCacheDB();
		$dcdb->delete('idNode', $idNode);

		if (!is_array($syncs)) {
			$syncs = array($syncs);
			/*
			$a = array();
			$a[] = $syncs;
			$syncs = $a;
			*/
		}

		foreach ($syncs as $idSync) {
			$dcdb->idNode = $idNode;
			$dcdb->idSync = $idSync;
			$dcdb->idVersion = $idVersion;
			$dcdb->commit();
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function getRelation() {
		
		$dcdb = new DexCacheDB();
		$dcdb->read();
	}

	/**
	 * future: should be in node class.
	 * $objVersion =& $node.getVersion()
	 * 
	 * @param $idNode
	 * @return unknown_type
	 */
	function getLastVersionOfNode($idNode) {

		$df = new DataFactory($idNode);

		$version = $df->GetLastVersion();
		$subversion = $df->GetLastSubversion();

		$IdVersion = $df->getVersionId($version, $subversion);

		return $IdVersion;
	}

	/**
	 * 
	 * @param $idNode
	 * @return unknown_type
	 */
	function isModified($idNode) {

		if ( is_null($idNode) ) {
			return true;
		}
		
		$df = new DataFactory($idNode);

		$version = $df->GetLastVersion();
		$subversion = $df->GetLastSubVersion($version);
		$idCurrentVersion = $df->getVersionId($version, $subversion);

		$dcdb = new DexCacheDB();
		$data = $dcdb->read('idNode', $idNode);

		$idSync = $data['idSync'];
		$idPublishedVersion = $data['idVersion'];

		//echo "DexCache::isModified - idNode: $idNode | version: $version | subversion: $subversion | sync: " . print_r($idSync,true) . "<br/>\n";
		//echo "DexCache::isModified - idCurrentVersion: $idCurrentVersion | idPublishedVersion: $idPublishedVersion<br/>\n";

		if ( $idCurrentVersion != $idPublishedVersion) {
			//echo "RETURN true<br/>\n";
			return true;
		} else {
			//echo "RETURN false<br/>\n";
			return false;
		}
	}

	/**
	 * 
	 * @param $idNode
	 * @param $channelId
	 * @return unknown_type
	 */
	function _createName($idNode, $channelId) {
		return Config::getValue('AppRoot') . Config::getValue('SyncRoot') . "/$idNode.$channelId.cache";
	}

	/**
	 * 
	 * @param $idNode
	 * @param $channelId
	 * @param $c
	 * @return unknown_type
	 */
	function createPersistentSyncFile($idNode, $channelId, $c) {
		// Creating a persistent copy of sync.
		$name = DexCache::_createName($idNode, $channelId);
		return FsUtils::file_put_contents($name, $c);
	}

	/**
	 * 
	 * @param $idNode
	 * @param $channelId
	 * @return unknown_type
	 */
	function & getPersistentSyncFile($idNode, $channelId) {

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

?>
