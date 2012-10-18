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

class ActionsStats_ORM extends GenericData   {
	var $_idField = 'IdStat';
	var $_table = 'ActionsStats';
	var $_metaData = array(
				'IdStat' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
				'IdAction' => array('type' => "int(11)", 'not_null' => 'false'),
				'IdNode' => array('type' => "int(11)", 'not_null' => 'false'),
				'IdUser' => array('type' => "int(11)", 'not_null' => 'false'),
				'Method' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'TimeStamp' => array('type' => "int(11)", 'not_null' => 'true'),
				'Duration' => array('type' => "float(11, 6)", 'not_null' => 'true')
				);
	var $_uniqueConstraints = array(

				);
	var $_indexes = array('IdStat');
	var $IdStat;
	var $IdAction;
	var $IdNode;
	var $IdUser;
	var $Method;
	var $TimeStamp;
	var $Duration;
}
?>
