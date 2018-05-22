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

namespace Ximdex\Models;

use Ximdex\Logger;

class RelFramesPortal extends \Ximdex\Data\GenericData
{
    var $_idField = 'id';
    var $_table = 'RelFramesPortal';
    var $_metaData = array(
        'id' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdPortalVersion' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdFrame' => array('type' => "int(12)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'PortalFrame' => array('IdPortalVersion', 'IdFrame')
    );
    var $_indexes = array('id');
    var $id;
    var $IdPortalVersion = 0;
    var $IdFrame = 0;

	function __construct($id = null)
	{
		parent::__construct($id);
	}

	function addVersion($idPortalVersion, $nodeFrameId)
	{
		$this->set('IdPortalVersion', $idPortalVersion);
		$this->set('IdFrame', $nodeFrameId);

		// Check for a duplicate of the relation to create
		$res = parent::exists('id');
		if ($res === false)
		{
		    Logger::error('When checking a duplicate element in database');
		    return false;
		}
		if ($res)
		{
		    // The relation already exists, returning the ID found
		    // Logger::info('Element to create in RelFramesPortal already exists with ID: ' . $res);
		    return $res;
		}
		$idRel = parent::add();
		return ($idRel > 0) ? $idRel : NULL;
	}

    /**
     * Returns de IdVersion for a node in a portal
     * 
     * @param $idPortalVersion
     * @param $nodeId
     * @return NULL|string
     */
	function getNodeVersion($idPortalVersion, $nodeId)
	{
		if (is_null($idPortalVersion) || is_null($nodeId)) {
			return NULL;
		}
		$db = new \Ximdex\Runtime\Db();
		$db->Query("SELECT n.VersionId FROM NodeFrames n, RelFramesPortal r WHERE r.IdPortalVersion = $idPortalVersion
			AND n.NodeId = $nodeId AND r.IdFrame = n.IdNodeFrame");
		if ($db->numRows == 0) {
			return NULL;
		}
		return $db->GetValue('VersionId');
	}
}