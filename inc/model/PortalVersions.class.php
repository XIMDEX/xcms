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



if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/PortalVersions_ORM.class.php';

class PortalVersions extends PortalVersions_ORM {

	function __construct($id = null)  {
		parent::GenericData($id);
	}

	function upPortalVersion($portalId) {
		$portalVersion = $this->getLastVersion($portalId);
		$portalVersion++;
		
		$this->set('IdPortal', $portalId);
		$this->set('Version', $portalVersion);
		$this->set('TimeStamp', mktime());

		$idPortalVersion = parent::add();

		return ($idPortalVersion > 0) ? $idPortalVersion : 0;
	}

	function getLastVersion($portalId) {

		$result = parent::find('MAX(Version)', 'IdPortal = %s', array('IdPortal' => $portalId), MONO);

		return (int) $result[0];
	}

	function getId($portalId, $version) {

		$result = parent::find('id', 'IdPortal = %s AND Version = %s', 
			array('IdPortal' => $portalId, 'Version' => $version), MONO);

		return (int) $result[0];
	}

	function getAllVersions($portalId) {
		
		$result = parent::find('id, Version', 'IdPortal = %s', array('IdPortal' => $portalId), MULTI);

		foreach ($result as $resultData) {
			$portalVersions[] = array('id' => $resultData['id'], 'version' => $resultData['Version']);
		}

		return $portalVersions;
	}

}