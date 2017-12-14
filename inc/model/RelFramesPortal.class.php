<?php
use Ximdex\Logger;

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




require_once XIMDEX_ROOT_PATH . '/inc/model/orm/RelFramesPortal_ORM.class.php';

class RelFramesPortal extends RelFramesPortal_ORM {

	function __construct($id = null)  {
		parent::__construct($id);
	}

	function addVersion($idPortalVersion, $nodeFrameId) {

		$this->set('IdPortalVersion', $idPortalVersion);
		$this->set('IdFrame', $nodeFrameId);

		//check for a duplicate of the relation to create
		$res = parent::exists('id');
		if ($res === false)
		{
		    Logger::error('When checking a duplicate element in database');
		    return false;
		}
		if ($res)
		{
		    //the relation already exists, returning the ID found
		    Logger::info('Element to create in RelFramesPortal already exists with ID: ' . $res);
		    return $res;
		}
		
		$idRel = parent::add();

		return ($idRel > 0) ? $idRel : NULL;
	}

/*
*
*	Returns de IdVersion for a node in a portal
*	@param idPortalVersion int
*	@param nodeid int
*	@return int / NULL
*
*/
	function getNodeVersion($idPortalVersion, $nodeId) {

		if (is_null($idPortalVersion) || is_null($nodeId)) {
			return NULL;
		}

		$db = new DB();
		$db->Query("SELECT n.VersionId FROM NodeFrames n, RelFramesPortal r WHERE r.IdPortalVersion = $idPortalVersion
			AND n.NodeId = $nodeId AND r.IdFrame = n.IdNodeFrame");

		if ($db->numRows == 0) {
			return NULL;
		}

		return $db->GetValue('VersionId');
	}
}