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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));

include_once (XIMDEX_ROOT_PATH . '/inc/helper/GenericData.class.php');

class SynchronizerHistory_ORM extends GenericData   {
	var $_idField = 'IdSync';
	var $_table = 'SynchronizerHistory';
	var $_metaData = array(
				'IdSync' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
				'IdServer' => array('type' => "int(12)", 'not_null' => 'true'),
				'IdNode' => array('type' => "int(12)", 'not_null' => 'true'),
				'IdChannel' => array('type' => "int(12)", 'not_null' => 'false'),
				'DateUp' => array('type' => "int(14)", 'not_null' => 'true'),
				'DateDown' => array('type' => "int(14)", 'not_null' => 'false'),
				'State' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'Error' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'ErrorLevel' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'RemotePath' => array('type' => "blob", 'not_null' => 'true'),
				'FileName' => array('type' => "varchar(255)", 'not_null' => 'true'),
				'Retry' => array('type' => "int(12)", 'not_null' => 'false'),
				'Linked' => array('type' => "tinyint(3)", 'not_null' => 'true')
				);
	var $_uniqueConstraints = array(
				'IdSync' => array('IdSync')
				);
	var $_indexes = array('IdSync');
	var $IdSync;
	var $IdServer = 0;
	var $IdNode = 0;
	var $IdChannel;
	var $DateUp = 0;
	var $DateDown = 0;
	var $State = 'DUE';
	var $Error;
	var $ErrorLevel;
	var $RemotePath;
	var $FileName;
	var $Retry = 0;
	var $Linked = 0;
}
?>
