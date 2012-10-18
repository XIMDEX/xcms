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




 


ModulesManager::file('/inc/helper/GenericData.class.php');

class NodeFrames_ORM extends GenericData   {
	var $_idField = 'IdNodeFrame';
	var $_table = 'NodeFrames';
	var $_metaData = array(
				'IdNodeFrame' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true'),
				'NodeId' => array('type' => 'int(12)', 'not_null' => 'false'),
				'VersionId' => array('type' => 'int(12)', 'not_null' => 'false'),
				'TimeUp' => array('type' => 'int(12)', 'not_null' => 'false'),
				'TimeDown' => array('type' => 'int(12)', 'not_null' => 'false'),
				'Active' => array('type' => 'int(12)', 'not_null' => 'false'),
				'GetActivityFrom' => array('type' => 'int(12)', 'not_null' => 'false'),
				'IsProcessUp' => array('type' => 'int(12)', 'not_null' => 'false'),
				'IsProcessDown' => array('type' => 'int(12)', 'not_null' => 'false'),
				'Name' => array('type' => 'varchar(255)', 'not_null' => 'false')
				);
	var $IdNodeFrame;
	var $NodeId = 0;
	var $VersionId = 0;
	var $TimeUp = 0;
	var $TimeDown;
	var $Active = 0;
	var $GetActivityFrom = 0;
	var $IsProcessUp = 0;
	var $IsProcessDown = 0;	
	var $Name;
}
?>
