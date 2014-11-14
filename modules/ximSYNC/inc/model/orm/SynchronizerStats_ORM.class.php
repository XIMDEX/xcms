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

class SynchronizerStats_ORM extends GenericData   {
	var $_idField = 'IdStat';
	var $_table = 'SynchronizerStats';
	var $_metaData = array(
				'IdStat' => array('type' => 'int(11)', 'not_null' => 'true', 'auto_increment' => 'true'),
				'BatchId' => array('type' => 'int(11)', 'not_null' => 'false'),
				'NodeFrameId' => array('type' => 'int(11)', 'not_null' => 'false'),
				'ChannelFrameId' => array('type' => 'int(11)', 'not_null' => 'false'),
				'ServerFrameId' => array('type' => 'int(11)', 'not_null' => 'false'),
				'PumperId' => array('type' => 'int(11)', 'not_null' => 'false'),
				'Class' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'Method' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'File' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'Line' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'Type' => array('type' => 'enum(5)', 'not_null' => 'true'),
				'Level' => array('type' => 'int(11)', 'not_null' => 'true'),
				'Time' => array('type' => 'int(11)', 'not_null' => 'true'),
				'Comment' => array('type' => 'varchar(255)', 'not_null' => 'true')
				);
	var $IdStat;
	var $BatchId;
	var $NodeFrameId;
	var $ChannelFrameId;
	var $ServerFrameId;
	var $PumperId;
	var $Class;
	var $Method;
	var $File;
	var $Line;
	var $Type;
	var $Level;
	var $Time;
	var $Comment;	
}
?>