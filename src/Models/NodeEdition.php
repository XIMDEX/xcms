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
use Ximdex\Data\GenericData;

class NodeEdition extends GenericData
{
    var $_idField = 'Id';
    var $_table = 'NodeEdition';
    var $_metaData = array(
        'Id' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNode' => array('type' => "int(11)", 'not_null' => 'true'),
        'IdUser' => array('type' => "int(11)", 'not_null' => 'true'),
        'StartTime' => array('type' => "int(11)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('Id');
    var $Id;
    var $IdNode;
    var $IdUser;
    var $StartTime;

    /**
     * Creates a new row in the NodeEdition database
     * 
     * @param int $idNode The Node Id
     * @param int  $idUser The User Id
     * @param int $startTime The start time of the edition
     * @return boolean indicatingwhether the creation has been successful or not
     */
	public function create($idNode, $idUser, $startTime = null)
	{
		if (is_null($idNode) || is_null($idUser))
		{
			Logger::error(_('Params node and user are mandatory'));
			return false;
		}
		$this->set('IdNode', $idNode);
		$this->set('IdUser', $idUser);
		$this->set('StartTime', is_null($startTime) ? time() : $startTime);
		parent::add();
		$nodeEditionId = $this->get('Id');
		if (!($nodeEditionId > 0))
		{
			Logger::error(_("Error Adding NodeEdition"));
			return false;
		}
		return true;
	}

    /**
     * Get the NodeEdition by node
     * 
     * @param mixed $node The Node object or node id
     * @return array Containing the number of simultaneous editions of this node
     */
    public function getByNode($node)
    {
        if (is_object($node))
        {
            $nodeId = $node->GetID();
        }
        else
        {
            $nodeId = $node;
        }
        $nodeId = empty($nodeId) ? $this->get("IdNode") : $nodeId;
        $this->ClearError();
        if (!empty($nodeId))
        {
            $result = $this->find("IdUser,StartTime", "IdNode = %s", array($nodeId));
            return $result;
        }
        return array();
    }
        
	/**
	 * Deletes a node edition by node and user
     * Indicates that a given user has finished the edition of the node
	 *
	 * @param int $idNode The node id
     * @param int $idUser The user id
	 * @return boolean indicating if the deletion of the edition has been successful or not
	 */
	function deleteByNodeAndUser($idNode = null, $idUser = null)
	{
		if (is_null($idNode) || is_null($idUser))
		{
			Logger::error(_('Params node and user are mandatory'));
			return false;
		}
 		$dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("DELETE FROM NodeEdition WHERE IdNode = %d AND IdUser = %s", $idNode, $idUser);
		$dbObj->Execute($sql);
		return true;
	}
	
	/**
	 * Deletes all node edition of a user
     * Indicates that a given user has finished the edition on the different nodes
	 *
     * @param int $idUser The user id
	 * @return boolean indicating if the deletion of the edition has been successful or not
	 */
	function deleteByUser($idUser = null)
	{
		if (is_null($idUser))
		{
			Logger::error(_('Param user is mandatory'));
			return false;
		}
 		$dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("DELETE FROM NodeEdition WHERE IdUser = %s", $idUser);
		$dbObj->Execute($sql);
		return true;
	}
}